<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketMessageRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\TicketMessageResource;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $query = Ticket::with(['creator', 'assignee']);

            if (!$this->isAdmin($user)) {
                $query->where(function ($inner) use ($user) {
                    $inner->where('created_by', $user->id);
                    if (!empty($user->branch)) {
                        $inner->orWhere('source_branch', $user->branch)
                            ->orWhere('target_branch', $user->branch);
                    }
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            if ($request->filled('branch')) {
                $branch = $request->branch;
                $query->where(function ($inner) use ($branch) {
                    $inner->where('source_branch', $branch)
                        ->orWhere('target_branch', $branch);
                });
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('subject', 'like', "%{$search}%");
            }

            $query->orderByDesc('last_message_at')->orderByDesc('created_at');

            $tickets = $query->paginate(20);

            return response()->json([
                'success' => true,
                'data' => [
                    'data' => TicketResource::collection($tickets),
                    'meta' => [
                        'current_page' => $tickets->currentPage(),
                        'last_page' => $tickets->lastPage(),
                        'per_page' => $tickets->perPage(),
                        'total' => $tickets->total(),
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении тикетов: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(Ticket $ticket, Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $this->authorizeTicket($user, $ticket);

            $ticket->load(['creator', 'assignee']);
            $messages = $ticket->messages()
                ->with('user')
                ->orderBy('created_at')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'ticket' => new TicketResource($ticket),
                    'messages' => TicketMessageResource::collection($messages),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении тикета: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function store(StoreTicketRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $category = $request->input('category', 'support');
            $priority = $request->input('priority', 'normal');

            $ticket = Ticket::create([
                'subject' => $request->subject,
                'status' => 'open',
                'priority' => $priority,
                'category' => $category,
                'created_by' => $user->id,
                'source_branch' => $user->branch,
                'target_branch' => $request->input('target_branch'),
                'assigned_to' => $request->input('assigned_to'),
                'last_message_at' => now(),
            ]);

            TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'message_type' => 'text',
                'body' => $request->message,
            ]);

            $ticket->load(['creator', 'assignee']);

            return response()->json([
                'success' => true,
                'message' => 'Тикет успешно создан',
                'data' => new TicketResource($ticket),
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании тикета: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket): JsonResponse
    {
        try {
            $user = $request->user();
            $this->authorizeTicket($user, $ticket);

            $payload = $request->only(['subject', 'status', 'priority', 'assigned_to']);

            if (isset($payload['status']) && in_array($payload['status'], ['resolved', 'closed'], true)) {
                $payload['closed_at'] = now();
            }

            $ticket->update($payload);
            $ticket->load(['creator', 'assignee']);

            return response()->json([
                'success' => true,
                'message' => 'Тикет успешно обновлен',
                'data' => new TicketResource($ticket),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении тикета: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function storeMessage(StoreTicketMessageRequest $request, Ticket $ticket): JsonResponse
    {
        try {
            $user = $request->user();
            $this->authorizeTicket($user, $ticket);

            $message = TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'message_type' => 'text',
                'body' => $request->message,
            ]);

            $ticket->last_message_at = now();
            if (in_array($ticket->status, ['resolved', 'closed'], true)) {
                $ticket->status = 'in_progress';
                $ticket->closed_at = null;
            }
            $ticket->save();

            $message->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Сообщение отправлено',
                'data' => new TicketMessageResource($message),
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при отправке сообщения: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function branches(Request $request): JsonResponse
    {
        try {
            $branches = User::query()
                ->whereNotNull('branch')
                ->where('branch', '<>', '')
                ->distinct()
                ->orderBy('branch')
                ->pluck('branch');

            return response()->json([
                'success' => true,
                'data' => $branches,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении списка филиалов: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function authorizeTicket(User $user, Ticket $ticket): void
    {
        if ($this->isAdmin($user)) {
            return;
        }

        $allowed = $ticket->created_by === $user->id;

        if (!$allowed && !empty($user->branch)) {
            $allowed = $ticket->source_branch === $user->branch || $ticket->target_branch === $user->branch;
        }

        if (!$allowed) {
            abort(403, 'Доступ запрещен');
        }
    }

    private function isAdmin(User $user): bool
    {
        return $user->position === 'admin' || $user->hasRole('admin');
    }
}
