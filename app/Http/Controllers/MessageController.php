<?php

namespace App\Http\Controllers;

use App\DTO\MessageDTO;
use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Factories\MessageStrategyFactory;
use App\Http\Requests\SendMessageRequest;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class MessageController extends Controller
{
    public function create()
    {
        return Inertia::render('Send');
    }

    public function store(SendMessageRequest $request, MessageStrategyFactory $messageStrategyFactory)
    {
        // Api request are not autenthicated, so we fetch a hardcoded user
        $userId = $request->user() ? $request->user()->id : 0;
        if($userId == 0) {
            $user = User::first();
            $userId = $user->id;
        }

        $data = $request->validated();
        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
        }
        $messsage = new MessageDTO($data['recipients'], $data['content'], $path, $userId);

        // Generate batch to send message to all recipients
        $messageStrategy = $messageStrategyFactory->make($data['platform']);
        $pendingBatch = $messageStrategy->sendMassMessage($messsage);

        // Invalidate cache after all messages where sent
        $pendingBatch->finally(function () use ($userId) {
                $maxPagesToClear = 10;
                for ($page = 1; $page <= $maxPagesToClear; $page++) {
                    Cache::forget("sent_messages_user_{$userId}_page_{$page}");
                }
            })->dispatch();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Messages are being processed.']);
        }

        return back();
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $page = $request->input('page', 1);
        $cacheKey = "sent_messages_user_{$user->id}_page_{$page}";

        $messages = Cache::remember($cacheKey, 300, function () use ($user) {
            return $user
                ->messages()
                ->latest()
                ->paginate(10)
                ->through(fn ($msg) => [
                    'id' => $msg->id,
                    'platform' => $msg->platform,
                    'content' => $msg->content,
                    'recipient' => $msg->recipient,
                    'attachment_url' => $msg->attachment_path ? asset('storage/' . $msg->attachment_path) : null,
                    'created_at' => $msg->created_at,
                ])->toArray();
        });

        return Inertia::render('Sent', [
            'messages' => $messages,
        ]);
    }
}
