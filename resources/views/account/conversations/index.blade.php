<x-layouts.app active="chat" title="Messages — AkibaShop">
    <div class="max-w-3xl mx-auto px-margin-mobile md:px-margin-desktop pt-4 md:pt-6">
        <h1 class="text-headline-lg text-on-surface mb-4">Messages</h1>

        @if($conversations->isEmpty())
            <x-empty-state icon="chat" title="Aucune conversation" text="Écrivez à un vendeur depuis une annonce avec « Envoyer un message ». Les échanges apparaîtront ici.">
                <a href="{{ route('listings.index') }}" class="btn btn-primary">Parcourir les annonces</a>
            </x-empty-state>
        @else
            <ul class="card divide-y divide-surface-variant overflow-hidden">
                @foreach($conversations as $conversation)
                    @php
                        $other = $conversation->otherParticipant($user);
                        $cover = $conversation->listing?->coverImage();
                        $last = $conversation->latestMessage;
                        $unread = $conversation->unread_count > 0;
                    @endphp
                    <li>
                        <a href="{{ route('account.conversations.show', $conversation) }}" class="flex items-center gap-3 p-3 md:p-4 hover:bg-surface-container-low transition-colors {{ $unread ? 'bg-primary-fixed/40' : '' }}">
                            <span class="relative w-14 h-14 shrink-0">
                                <span class="w-14 h-14 rounded-xl overflow-hidden bg-surface-container-low flex items-center justify-center">
                                    @if($cover)
                                        <img src="{{ $cover->url() }}" alt="" loading="lazy" class="w-full h-full object-cover" />
                                    @else
                                        <x-icon name="image" class="text-[22px] text-outline" />
                                    @endif
                                </span>
                                <x-avatar :user="$other" size="w-7 h-7 text-[11px]" class="absolute -right-1.5 -bottom-1.5 ring-2 ring-surface-container-lowest" />
                            </span>
                            <span class="flex-1 min-w-0">
                                <span class="flex items-baseline justify-between gap-2">
                                    <span class="text-[15px] {{ $unread ? 'font-extrabold' : 'font-bold' }} text-on-surface truncate">{{ $other?->name ?? 'Membre' }}</span>
                                    <time class="shrink-0 text-[12px] {{ $unread ? 'text-primary font-bold' : 'text-on-surface-variant' }}" datetime="{{ $conversation->last_message_at?->toIso8601String() }}">
                                        {{ $conversation->last_message_at?->isToday() ? $conversation->last_message_at->format('H:i') : $conversation->last_message_at?->translatedFormat('d M') }}
                                    </time>
                                </span>
                                <span class="block text-[12px] text-on-surface-variant truncate">{{ $conversation->listing?->title ?? 'Annonce supprimée' }}</span>
                                <span class="flex items-center justify-between gap-2 mt-0.5">
                                    <span class="text-[13px] truncate {{ $unread ? 'text-on-surface font-semibold' : 'text-on-surface-variant' }}">
                                        @if($last?->sender_id === $user->id)<span class="text-on-surface-variant">Vous : </span>@endif{{ $last?->body }}
                                    </span>
                                    @if($unread)
                                        <span class="shrink-0 min-w-5 h-5 px-1.5 rounded-full bg-primary text-on-primary text-[11px] font-bold flex items-center justify-center">{{ $conversation->unread_count }}</span>
                                    @endif
                                </span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-layouts.app>
