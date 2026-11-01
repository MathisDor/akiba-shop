@php($listing = $conversation->listing)

<x-layouts.app active="chat" :title="'Discussion avec '.($other?->name ?? 'un membre').' — AkibaShop'" hide-footer>
    <div
        class="max-w-3xl mx-auto md:px-margin-desktop md:pt-6 flex flex-col h-[calc(100dvh-71px-4rem)] md:h-[calc(100dvh-121px-3rem)]"
        x-data="chat({
            messages: @js($messages),
            pollUrl: @js(route('account.conversations.poll', $conversation)),
            sendUrl: @js(route('account.conversations.send', $conversation)),
            pollSeconds: {{ (int) config('marketplace.chat_poll_seconds') }},
        })"
    >
        {{-- Thread header: who, and which listing --}}
        <div class="flex items-center gap-3 px-margin-mobile md:px-4 py-3 bg-surface-container-lowest border-b md:border md:rounded-t-2xl border-surface-variant">
            <a href="{{ route('account.conversations.index') }}" class="w-9 h-9 shrink-0 flex items-center justify-center rounded-full hover:bg-surface-container-low" aria-label="Toutes les conversations">
                <x-icon name="arrow_back" class="text-[20px]" />
            </a>
            @if($listing)
                <a href="{{ route('listings.show', $listing) }}" class="flex items-center gap-3 min-w-0 flex-1 group">
                    <span class="w-11 h-11 shrink-0 rounded-lg overflow-hidden bg-surface-container-low flex items-center justify-center">
                        @if($cover = $listing->coverImage())
                            <img src="{{ $cover->url() }}" alt="" class="w-full h-full object-cover" />
                        @else
                            <x-icon name="image" class="text-[18px] text-outline" />
                        @endif
                    </span>
                    <span class="min-w-0">
                        <span class="block text-[15px] font-bold text-on-surface truncate">{{ $other?->name ?? 'Membre' }}</span>
                        <span class="block text-[12px] text-on-surface-variant truncate group-hover:text-primary">{{ $listing->title }} · {{ $listing->formattedPrice() }}</span>
                    </span>
                </a>
            @else
                <span class="text-[15px] font-bold text-on-surface">{{ $other?->name ?? 'Membre' }}</span>
            @endif
        </div>

        {{-- Messages --}}
        <div x-ref="scroller" class="flex-1 overflow-y-auto px-margin-mobile md:px-4 py-4 flex flex-col gap-2 bg-surface md:border-x border-surface-variant" aria-live="polite">
            <template x-for="(message, i) in messages" :key="message.id">
                <div class="flex flex-col" :class="message.mine ? 'items-end' : 'items-start'">
                    <p x-show="i === 0 || messages[i - 1].date !== message.date" class="self-center my-2 text-[11px] font-semibold text-on-surface-variant" x-text="message.date"></p>
                    <div class="max-w-[80%] rounded-2xl px-3.5 py-2 text-[15px] leading-5 whitespace-pre-line break-words" :class="message.mine ? 'bg-primary text-on-primary rounded-br-md' : 'bg-surface-container-lowest text-on-surface border border-surface-variant rounded-bl-md'">
                        <span x-text="message.body"></span>
                        <span class="block text-right text-[10px] mt-0.5" :class="message.mine ? 'text-on-primary/75' : 'text-on-surface-variant'" x-text="message.time"></span>
                    </div>
                </div>
            </template>
            <p x-show="messages.length === 0" class="m-auto text-[14px] text-on-surface-variant">Aucun message pour l’instant.</p>
        </div>

        {{-- Composer --}}
        <form @submit.prevent="send()" class="flex items-end gap-2 px-margin-mobile md:px-4 py-3 bg-surface-container-lowest border-t md:border md:rounded-b-2xl border-surface-variant">
            <label for="chat-body" class="sr-only">Votre message</label>
            <textarea
                id="chat-body"
                x-model="draft"
                x-ref="input"
                rows="1"
                maxlength="2000"
                placeholder="Écrire un message…"
                @keydown.enter="if (!$event.shiftKey && window.matchMedia('(min-width: 768px)').matches) { $event.preventDefault(); send() }"
                @input="$el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 120) + 'px'"
                class="field flex-1 resize-none !min-h-[44px] max-h-[120px]"
            ></textarea>
            <button type="submit" class="btn btn-primary !px-4 h-11" :disabled="sending || draft.trim() === ''" aria-label="Envoyer">
                <x-icon name="arrow_forward" class="text-[20px]" />
            </button>
        </form>
        <p x-show="error" x-cloak class="px-margin-mobile md:px-4 pb-2 field-error" x-text="error"></p>
    </div>

    @push('head')
        <script>
            function chat(config) {
                return {
                    ...config,
                    draft: '',
                    sending: false,
                    error: '',
                    timer: null,

                    init() {
                        this.scrollDown();
                        this.timer = setInterval(() => this.poll(), this.pollSeconds * 1000);
                        // Catch up immediately when the member comes back to the tab.
                        document.addEventListener('visibilitychange', () => { if (!document.hidden) this.poll(); });
                    },
                    destroy() {
                        clearInterval(this.timer);
                    },
                    lastId() {
                        return this.messages.length ? this.messages[this.messages.length - 1].id : 0;
                    },
                    headers() {
                        return { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content };
                    },
                    append(incoming) {
                        const known = new Set(this.messages.map((m) => m.id));
                        const fresh = incoming.filter((m) => !known.has(m.id));
                        if (fresh.length) {
                            this.messages.push(...fresh);
                            this.scrollDown();
                        }
                    },
                    async poll() {
                        if (document.hidden) return;
                        try {
                            const response = await fetch(this.pollUrl + '?after=' + this.lastId(), { headers: this.headers() });
                            if (response.ok) this.append((await response.json()).messages);
                        } catch (e) {
                            // Offline for a moment: the next tick retries.
                        }
                    },
                    async send() {
                        const body = this.draft.trim();
                        if (!body || this.sending) return;
                        this.sending = true;
                        this.error = '';
                        try {
                            const response = await fetch(this.sendUrl, {
                                method: 'POST',
                                headers: { ...this.headers(), 'Content-Type': 'application/json' },
                                body: JSON.stringify({ body }),
                            });
                            if (!response.ok) throw new Error();
                            this.append([(await response.json()).message]);
                            this.draft = '';
                            this.$nextTick(() => { this.$refs.input.style.height = 'auto'; this.$refs.input.focus(); });
                        } catch (e) {
                            this.error = 'Le message n’a pas pu être envoyé. Vérifiez votre connexion et réessayez.';
                        } finally {
                            this.sending = false;
                        }
                    },
                    scrollDown() {
                        this.$nextTick(() => { this.$refs.scroller.scrollTop = this.$refs.scroller.scrollHeight; });
                    },
                };
            }
        </script>
    @endpush
</x-layouts.app>
