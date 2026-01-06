<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;

class UpdateShareTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:update-share-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing payments with share tokens';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating payments with share tokens...');

        $count = 0;
        Payment::whereNull('share_token')->chunk(100, function ($payments) use (&$count) {
            foreach ($payments as $payment) {
                $payment->update([
                    'share_token' => Payment::generateShareToken()
                ]);
                $count++;
            }
        });

        $this->info("Updated {$count} payments with share tokens.");

        return Command::SUCCESS;
    }
}
