@php
    use App\Enums\ListingStatus;
    use App\Enums\ReportReason;

    $seller = $listing->user;
    $images = $listing->images;
    $dataSaver = request()->cookie('eco') === '1';
    $shareText = $listing->title.' — '.$listing->formattedPrice().' sur AkibaShop';
    $shareUrl = route('listings.show', $listing);
@endphp

<x-layouts.app
    :title="$listing->title.' — '.$listing->formattedPrice().' · '.$listing->city->name.' | AkibaShop'"
    :description="\Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', $listing->description), 155)"
    :og-image="$images->first()?->url()"
>
    <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop pt-4 md:pt-6">

        @if($isOwner && ! $listing->isVisible())
            <div class="mb-4 rounded-xl px-4 py-3 text-[14px] leading-5 flex items-start gap-3 {{ $listing->status === ListingStatus::Refusee ? 'bg-error-container text-on-error-container' : 'bg-tertiary-container text-on-tertiary-container' }}">
                <x-icon name="info" class="text-[20px] shrink-0" />
                <div class="flex-1">
                    <p class="font-bold">
                        @if($listing->status === ListingStatus::Publiee && $listing->isExpired())
                            Annonce expirée — renouvelez-la depuis « Mes annonces ».
                        @else
                            {{ $listing->status->label() }} — seul vous voyez cette page pour l’instant.
                        @endif
                    </p>
                    @if($listing->rejection_reason)
                        <p class="mt-1">Motif : {{ $listing->rejection_reason }}</p>
                    @endif
                </div>
                @if($listing->status !== ListingStatus::Vendue)
                    <a href="{{ route('account.listings.edit', $listing) }}" class="font-bold underline shrink-0">Modifier</a>
                @endif
            </div>
        @endif

        <nav aria-label="Fil d’Ariane" class="text-[13px] text-on-surface-variant mb-3">
            <ol class="flex flex-wrap items-center gap-1">
                <li><a href="{{ route('home') }}" class="hover:text-primary">Accueil</a></li>
                @if($listing->category->parent)
                    <li aria-hidden="true">›</li>
                    <li><a href="{{ route('categories.show', $listing->category->parent) }}" class="hover:text-primary">{{ $listing->category->parent->name }}</a></li>
                @endif
                <li aria-hidden="true">›</li>
                <li><a href="{{ route('categories.show', $listing->category) }}" class="hover:text-primary">{{ $listing->category->name }}</a></li>
            </ol>
        </nav>

        <div class="grid lg:grid-cols-[1fr_360px] gap-6 items-start">
            <div class="flex flex-col gap-4 min-w-0">

                {{-- Gallery --}}
                @if($images->isNotEmpty())
                    <section
                        aria-label="Photos"
                        x-data="{ index: 0, count: {{ $images->count() }}, show: @js(! $dataSaver) }"
                        class="card overflow-hidden"
                    >
                        <div class="relative aspect-[4/3] md:aspect-[16/10] bg-inverse-surface">
                            <template x-if="show">
                                <div class="absolute inset-0">
                                    @foreach($images as $i => $image)
                                        <img
                                            x-show="index === {{ $i }}"
                                            @if($i > 0) x-cloak @endif
                                            src="{{ $image->url() }}"
                                            alt="{{ $listing->title }} — photo {{ $i + 1 }}"
                                            loading="{{ $i === 0 ? 'eager' : 'lazy' }}"
                                            decoding="async"
                                            class="absolute inset-0 w-full h-full object-contain"
                                        />
                                    @endforeach
                                </div>
                            </template>

                            <div x-show="!show" class="absolute inset-0 flex flex-col items-center justify-center gap-3 text-inverse-on-surface">
                                <x-icon name="data_saver" class="text-[32px]" />
                                <p class="text-[14px]">Économie de données activée</p>
                                <button type="button" @click="show = true" class="btn btn-accent btn-sm">Afficher les {{ $images->count() }} photo{{ $images->count() > 1 ? 's' : '' }}</button>
                            </div>

                            @if($images->count() > 1)
                                <button type="button" x-show="show" @click="index = (index - 1 + count) % count" class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/90 text-on-surface flex items-center justify-center shadow" aria-label="Photo précédente">
                                    <x-icon name="chevron_left" class="text-[22px]" />
                                </button>
                                <button type="button" x-show="show" @click="index = (index + 1) % count" class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/90 text-on-surface flex items-center justify-center shadow" aria-label="Photo suivante">
                                    <x-icon name="chevron_right" class="text-[22px]" />
                                </button>
                                <span x-show="show" class="absolute bottom-3 right-3 rounded-md bg-black/60 text-white px-2 py-0.5 text-[12px] font-semibold" x-text="(index + 1) + ' / ' + count"></span>
                            @endif
                        </div>

                        @if($images->count() > 1)
                            <div x-show="show" class="flex gap-2 p-2 overflow-x-auto hide-scrollbar">
                                @foreach($images as $i => $image)
                                    <button type="button" @click="index = {{ $i }}" class="shrink-0 w-16 h-12 md:w-20 md:h-14 rounded-lg overflow-hidden ring-2 transition" :class="index === {{ $i }} ? 'ring-primary' : 'ring-transparent opacity-70 hover:opacity-100'" aria-label="Voir la photo {{ $i + 1 }}">
                                        <template x-if="show"><img src="{{ $image->url() }}" alt="" loading="lazy" class="w-full h-full object-cover" /></template>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </section>
                @endif

                {{-- Title & price --}}
                <section class="card p-4 md:p-6 flex flex-col gap-3">
                    <div class="flex flex-wrap items-center gap-2">
                        @if($listing->isBoosted())
                            <span class="inline-flex items-center gap-1 rounded-md bg-tertiary text-on-tertiary px-2 py-0.5 text-[12px] font-bold"><x-icon name="boost" class="text-[13px]" :filled="true" /> À la une</span>
                        @endif
                        @if($listing->condition)
                            <span class="rounded-md bg-surface-container-low text-on-surface px-2 py-0.5 text-[12px] font-semibold">{{ $listing->condition->label() }}</span>
                        @endif
                        <span class="rounded-md bg-surface-container-low text-on-surface-variant px-2 py-0.5 text-[12px] font-semibold">{{ $listing->category->name }}</span>
                    </div>

                    <h1 class="text-headline-md md:text-headline-lg text-on-surface break-words">{{ $listing->title }}</h1>

                    <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
                        <p class="text-[28px] md:text-[32px] leading-none font-extrabold text-primary">{{ $listing->formattedPrice() }}</p>
                        @if($listing->is_negotiable)
                            <span class="text-[13px] font-semibold text-on-surface-variant">Prix négociable</span>
                        @endif
                    </div>

                    <ul class="flex flex-wrap gap-x-4 gap-y-1 text-[13px] text-on-surface-variant">
                        <li class="flex items-center gap-1"><x-icon name="location_on" class="text-[15px]" /> {{ $listing->location() }}</li>
                        @if($listing->published_at)
                            <li class="flex items-center gap-1"><x-icon name="clock" class="text-[15px]" /> <time datetime="{{ $listing->published_at->toIso8601String() }}" title="{{ $listing->published_at->translatedFormat('d F Y à H\hi') }}">Publiée {{ $listing->published_at->diffForHumans() }}</time></li>
                        @endif
                        <li class="flex items-center gap-1"><x-icon name="views" class="text-[15px]" /> {{ number_format($listing->views, 0, ',', ' ') }} vue{{ $listing->views > 1 ? 's' : '' }}</li>
                    </ul>
                </section>

                {{-- Details --}}
                @php($specs = $listing->specList())
                @if($specs !== [])
                    <section class="card p-4 md:p-6">
                        <h2 class="text-[17px] font-extrabold text-on-surface mb-3">Caractéristiques</h2>
                        <dl class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($specs as $label => $value)
                                <div class="rounded-xl bg-surface-container-low px-3 py-2.5">
                                    <dt class="text-[12px] text-on-surface-variant">{{ $label }}</dt>
                                    <dd class="text-[15px] font-bold text-on-surface break-words">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </section>
                @endif

                <section class="card p-4 md:p-6">
                    <h2 class="text-[17px] font-extrabold text-on-surface mb-3">Description</h2>
                    <div class="text-body-md text-on-surface whitespace-pre-line break-words">{{ $listing->description }}</div>
                </section>

                {{-- Share & report --}}
                <section class="card p-4 md:p-6 flex flex-col gap-4" x-data="{ copied: false, reportOpen: @js($errors->has('reason')) }">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-[14px] font-bold text-on-surface mr-1">Partager :</span>
                        <a href="https://wa.me/?text={{ rawurlencode($shareText.' '.$shareUrl) }}" target="_blank" rel="noopener" class="btn btn-sm btn-whatsapp"><x-icon name="whatsapp" class="text-[16px]" /> WhatsApp</a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode($shareUrl) }}" target="_blank" rel="noopener" class="btn btn-sm text-white bg-[#1877f2] hover:bg-[#1465d6]"><x-icon name="facebook" class="text-[16px]" /> Facebook</a>
                        <button type="button" class="btn btn-sm btn-outline" @click="navigator.clipboard.writeText(@js($shareUrl)).then(() => { copied = true; setTimeout(() => copied = false, 2000) })">
                            <x-icon name="share" class="text-[16px]" />
                            <span x-text="copied ? 'Lien copié !' : 'Copier le lien'">Copier le lien</span>
                        </button>
                    </div>

                    @unless($isOwner)
                        <div class="border-t border-surface-variant pt-4">
                            <button type="button" @click="reportOpen = !reportOpen" class="inline-flex items-center gap-2 text-[14px] font-semibold text-on-surface-variant hover:text-error" :aria-expanded="reportOpen.toString()">
                                <x-icon name="report" class="text-[16px]" /> Signaler cette annonce
                            </button>
                            <form x-show="reportOpen" x-cloak method="POST" action="{{ route('listings.report', $listing) }}" class="mt-3 flex flex-col gap-3 max-w-lg">
                                @csrf
                                <div>
                                    <label for="report-reason" class="field-label">Motif</label>
                                    <select id="report-reason" name="reason" class="field" required>
                                        <option value="">Choisissez un motif…</option>
                                        @foreach(ReportReason::cases() as $reason)
                                            <option value="{{ $reason->value }}" @selected(old('reason') === $reason->value)>{{ $reason->label() }}</option>
                                        @endforeach
                                    </select>
                                    @error('reason') <p class="field-error">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="report-details" class="field-label">Précisions <span class="font-normal text-on-surface-variant">(facultatif)</span></label>
                                    <textarea id="report-details" name="details" rows="3" maxlength="1000" class="field">{{ old('details') }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-outline self-start">Envoyer le signalement</button>
                            </form>
                        </div>
                    @endunless
                </section>
            </div>

            {{-- Seller & contact --}}
            <aside class="flex flex-col gap-4 lg:sticky lg:top-[136px]">
                <section class="card p-4 md:p-5 flex flex-col gap-4">
                    <a href="{{ route('sellers.show', $seller) }}" class="flex items-center gap-3 group">
                        <span class="w-12 h-12 rounded-full bg-primary-fixed text-on-primary-fixed-variant flex items-center justify-center text-[18px] font-extrabold shrink-0">{{ mb_strtoupper(mb_substr($seller->name, 0, 1)) }}</span>
                        <span class="min-w-0">
                            <span class="flex items-center gap-1.5">
                                <span class="text-[16px] font-bold text-on-surface truncate group-hover:text-primary">{{ $seller->name }}</span>
                                @if($seller->is_verified)
                                    <x-icon name="verified" class="text-[18px] text-secondary" title="Vendeur vérifié" />
                                @endif
                            </span>
                            <span class="block text-[12px] text-on-surface-variant">
                                {{ $seller->is_pro ? 'Professionnel' : 'Particulier' }} · membre depuis {{ $seller->created_at->translatedFormat('F Y') }}
                            </span>
                        </span>
                    </a>

                    <div class="flex flex-wrap gap-2">
                        @if($seller->is_verified)
                            <span class="inline-flex items-center gap-1 rounded-md bg-secondary-container text-on-secondary-container px-2 py-0.5 text-[12px] font-bold"><x-icon name="verified" class="text-[13px]" /> Vendeur vérifié</span>
                        @endif
                        @if($seller->is_pro)
                            <span class="rounded-md bg-secondary-container text-on-secondary-container px-2 py-0.5 text-[12px] font-bold">Pro</span>
                        @endif
                        <a href="{{ route('sellers.show', $seller) }}" class="text-[13px] font-semibold text-secondary hover:underline">{{ $seller->visible_listings_count }} annonce{{ $seller->visible_listings_count > 1 ? 's' : '' }} en ligne</a>
                    </div>

                    <div class="hidden md:flex flex-col gap-2" x-data="{ reveal: false }">
                        @if($listing->accepts_whatsapp)
                            <a href="{{ $listing->whatsappUrl() }}" target="_blank" rel="noopener" class="btn btn-whatsapp w-full h-12 text-[16px]">
                                <x-icon name="whatsapp" class="text-[20px]" /> Contacter sur WhatsApp
                            </a>
                        @endif
                        <a x-show="reveal" x-cloak href="{{ $listing->telUrl() }}" class="btn btn-primary w-full h-12 text-[16px]">
                            <x-icon name="phone" class="text-[20px]" /> {{ $listing->formattedPhone() }}
                        </a>
                        <button x-show="!reveal" type="button" @click="reveal = true" class="btn btn-outline w-full h-12 text-[16px]">
                            <x-icon name="phone" class="text-[20px]" /> Afficher le numéro
                        </button>
                    </div>

                    @if(! $isOwner)
                        <div class="flex items-center justify-between gap-2 border-t border-surface-variant pt-3">
                            <span class="text-[14px] text-on-surface-variant">Garder cette annonce</span>
                            <x-favorite-button :listing="$listing" :active="auth()->check() && $listing->favoritedBy->contains(auth()->id())" class="!bg-surface-container-low !shadow-none" />
                        </div>
                    @endif
                </section>

                <section class="rounded-2xl bg-tertiary-container text-on-tertiary-container p-4 md:p-5">
                    <h2 class="flex items-center gap-2 text-[15px] font-extrabold"><x-icon name="warning" class="text-[18px]" /> Achetez en toute sécurité</h2>
                    <ul class="mt-2 flex flex-col gap-1.5 text-[13px] leading-5 list-disc pl-5">
                        <li>Rencontrez le vendeur dans un lieu public, de jour.</li>
                        <li>Vérifiez l’article avant de payer.</li>
                        <li>N’envoyez jamais d’argent (Airtel Money, Moov Money) avant d’avoir vu l’article.</li>
                    </ul>
                    <a href="{{ route('pages.safety') }}" class="mt-2 inline-block text-[13px] font-bold underline">Tous nos conseils</a>
                </section>
            </aside>
        </div>

        @if($similar->isNotEmpty())
            <section class="pt-10">
                <h2 class="text-headline-md text-on-surface mb-4">Annonces similaires</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4">
                    @foreach($similar as $item)
                        <x-listing-card :listing="$item" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    {{-- Mobile contact bar, sits right above the bottom navigation --}}
    @unless($isOwner)
        <div class="md:hidden fixed inset-x-0 z-30 bg-surface-container-lowest border-t border-surface-variant px-margin-mobile py-2 flex gap-2" style="bottom: calc(4rem + env(safe-area-inset-bottom));">
            <a href="{{ $listing->telUrl() }}" class="btn btn-outline flex-1"><x-icon name="phone" class="text-[18px]" /> Appeler</a>
            @if($listing->accepts_whatsapp)
                <a href="{{ $listing->whatsappUrl() }}" target="_blank" rel="noopener" class="btn btn-whatsapp flex-[1.4]"><x-icon name="whatsapp" class="text-[18px]" /> WhatsApp</a>
            @endif
        </div>
        <div class="md:hidden h-16" aria-hidden="true"></div>
    @endunless

    @push('head')
        <script type="application/ld+json">{!! json_encode(array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $listing->title,
            'description' => \Illuminate\Support\Str::limit($listing->description, 500),
            'image' => $images->map->url()->all() ?: null,
            'category' => $listing->category->name,
            'itemCondition' => match ($listing->condition?->value) {
                'neuf' => 'https://schema.org/NewCondition',
                'occasion' => 'https://schema.org/UsedCondition',
                default => null,
            },
            'offers' => array_filter([
                '@type' => 'Offer',
                'url' => $shareUrl,
                'priceCurrency' => 'XAF',
                'price' => $listing->price,
                'availability' => $listing->isVisible() ? 'https://schema.org/InStock' : 'https://schema.org/SoldOut',
                'areaServed' => $listing->city->name,
                'seller' => ['@type' => $seller->is_pro ? 'Organization' : 'Person', 'name' => $seller->name],
            ], fn ($value) => $value !== null),
        ], fn ($value) => $value !== null), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    @endpush
</x-layouts.app>
