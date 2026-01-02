<?php

namespace Tests\Feature\API;

use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
        $this->artisan('db:seed', ['--class' => 'PaymentTypesSeeder']);

        $this->user = User::factory()->create(['status' => 'active']);
        $this->user->assignRole('admin');
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    public function test_can_get_list_of_payments(): void
    {
        Payment::factory()->count(5)->create(['cashier_id' => $this->user->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/payments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'date',
                        'amount',
                        'commission',
                        'total',
                        'status',
                    ],
                ],
                'meta',
            ]);
    }

    public function test_can_create_payment(): void
    {
        $paymentType = PaymentType::first();

        $paymentData = [
            'date' => now()->toDateString(),
            'time' => now()->toTimeString(),
            'payment_type_id' => $paymentType->id,
            'payer_name' => 'Test Payer',
            'purpose' => 'Test payment purpose',
            'amount' => 1000,
            'payment_method' => 'cash',
            'currency' => 'UZS',
        ];

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/payments', $paymentData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'amount',
                    'commission',
                    'total',
                ],
            ]);

        $this->assertDatabaseHas('payments', [
            'payer_name' => 'Test Payer',
            'amount' => 1000,
        ]);
    }

    public function test_cannot_create_payment_with_invalid_data(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/payments', [
                'payer_name' => '', // Invalid: empty
                'amount' => -100, // Invalid: negative
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['payer_name', 'amount']);
    }

    public function test_can_confirm_payment(): void
    {
        $payment = Payment::factory()->create([
            'cashier_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson("/api/payments/{$payment->id}/confirm");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_can_filter_payments_by_date(): void
    {
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        Payment::factory()->create([
            'cashier_id' => $this->user->id,
            'date' => $today,
        ]);

        Payment::factory()->create([
            'cashier_id' => $this->user->id,
            'date' => $yesterday,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson("/api/payments?date={$today}");

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
    }

    public function test_can_filter_payments_by_status(): void
    {
        Payment::factory()->create([
            'cashier_id' => $this->user->id,
            'status' => 'confirmed',
        ]);

        Payment::factory()->create([
            'cashier_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/payments?status=confirmed');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('confirmed', $data[0]['status']);
    }
}
