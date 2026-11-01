<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Listing;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Member-to-member chat about a listing. There is no websocket server on the
 * host, so the open conversation polls `messages` for anything newer.
 */
class ConversationController extends Controller
{
    public function index(): View
    {
        $user = $this->user();

        $conversations = $user->conversations()
            ->with(['listing.images', 'buyer', 'seller', 'latestMessage'])
            ->withCount(['messages as unread_count' => fn ($q) => $q->where('sender_id', '!=', $user->id)->whereNull('read_at')])
            ->whereNotNull('last_message_at')
            ->latest('last_message_at')
            ->get();

        return view('account.conversations.index', compact('conversations', 'user'));
    }

    public function show(Conversation $conversation): View
    {
        $user = $this->user();
        abort_unless($conversation->involves($user), 404);

        $conversation->load(['listing.images', 'buyer', 'seller']);
        $conversation->markReadFor($user);

        $messages = $conversation->messages()->get()->map->toChatArray($user);

        return view('account.conversations.show', [
            'conversation' => $conversation,
            'other' => $conversation->otherParticipant($user),
            'messages' => $messages,
            'user' => $user,
        ]);
    }

    /** First message from a buyer, sent from the listing page. */
    public function start(Request $request, Listing $listing): RedirectResponse
    {
        $user = $this->user();
        abort_unless($listing->isVisible(), 404);

        if ($listing->user_id === $user->id) {
            return back()->with('status', 'C’est votre propre annonce.');
        }

        $body = $this->validatedBody($request);

        $conversation = DB::transaction(function () use ($listing, $user, $body) {
            $conversation = Conversation::firstOrCreate(
                ['listing_id' => $listing->id, 'buyer_id' => $user->id],
                ['seller_id' => $listing->user_id],
            );
            $this->append($conversation, $user, $body);

            return $conversation;
        });

        return redirect()->route('account.conversations.show', $conversation);
    }

    public function send(Request $request, Conversation $conversation): JsonResponse|RedirectResponse
    {
        $user = $this->user();
        abort_unless($conversation->involves($user), 404);

        $message = $this->append($conversation, $user, $this->validatedBody($request));

        return $request->wantsJson()
            ? response()->json(['message' => $message->toChatArray($user)])
            : redirect()->route('account.conversations.show', $conversation);
    }

    /** Messages newer than ?after=<id>; also marks them read since the thread is on screen. */
    public function poll(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $this->user();
        abort_unless($conversation->involves($user), 404);

        $messages = $conversation->messages()->where('id', '>', $request->integer('after'))->get();
        $conversation->markReadFor($user);

        return response()->json([
            'messages' => $messages->map->toChatArray($user)->values(),
        ]);
    }

    /** Unread count for the navigation badges. */
    public function unread(): JsonResponse
    {
        return response()->json(['unread' => $this->user()->unreadMessagesCount()]);
    }

    private function append(Conversation $conversation, User $sender, string $body): Message
    {
        $message = $conversation->messages()->create(['sender_id' => $sender->id, 'body' => $body]);
        $conversation->update(['last_message_at' => $message->created_at]);

        return $message;
    }

    private function validatedBody(Request $request): string
    {
        return trim($request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ], [
            'body.required' => 'Écrivez un message.',
        ])['body']);
    }

    private function user(): User
    {
        return Auth::guard('web')->user();
    }
}
