@php
    use App\Support\Phone;

    $isMe = auth()->id() === $seller->id;
    $details = array_filter([
        $seller->location() ? ['location_on', 'Localisation', collect([$seller->address, $seller->location()])->filter()->implode(' · ')] : null,
        ['local_shipping', 'Livraison', $seller->delivers ? ($seller->delivery_note ?: 'Livraison possible') : 'Remise en main propre uniquement'],
        $seller->opening_hours ? ['clock', 'Horaires', $seller->opening_hours] : null,
        ['calendar', 'Sur AkibaShop', 'Depuis '.$seller->created_at->translatedFormat('F Y')],
    ]);
    $shareUrl = route('sellers.show', $seller);
@endphp

<x-layouts.app
    :title="$seller->name.' — boutique sur AkibaShop'"
    :description="$seller->bio ? \Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', $seller->bio), 155) : 'Toutes les annonces de '.$seller->name.' sur AkibaShop.'"
    :og-image="$seller->coverUrl() ?? $seller->avatarUrl()"
>
    <div class="max-w-container-max mx-auto md:px-margin-desktop md:pt-6">
        {{-- Storefront header --}}
        <section class="bg-surface-container-lowest md:card md:overflow-hidden border-b border-surface-variant md:border-b">
            <div class="relative aspect-[5/2] md:aspect-[5/1] bg-gradient-to-br from-primary to-primary-container overflow-hidden">
                @if($seller->coverUrl())
                    <img src="{{ $seller->coverUrl() }}" alt="" class="absolute inset-0 w-full h-full object-cover" />
                @else
                    <x-logo mark-only class="absolute right-4 bottom-4 opacity-25 scale-150" />
                @endif
                @if($isMe)
                    <a href="{{ route('account.shop.edit') }}" class="absolute top-3 right-3 btn btn-sm bg-black/55 text-white hover:bg-black/70"><x-icon name="edit" class="text-[15px]" /> Modifier ma boutique</a>
                @endif
            </div>

            <div class="px-margin-mobile md:px-6 pb-4">
                <div class="relative z-10 flex items-end justify-between gap-3 -mt-10 md:-mt-12">
                    <x-avatar :user="$seller" size="w-20 h-20 md:w-24 md:h-24 text-[30px]" class="ring-4 ring-surface-container-lowest" />
                    <p class="pb-1 text-[13px] text-on-surface-variant"><span class="font-extrabold text-on-surface">{{ $listings->total() }}</span> annonce{{ $listings->total() > 1 ? 's' : '' }} en ligne</p>
                </div>

                <h1 class="mt-2 text-[22px] leading-7 md:text-headline-lg font-extrabold text-on-surface flex flex-wrap items-center gap-x-2 gap-y-1">
                    {{ $seller->name }}
                    @if($seller->is_verified)
                        <x-icon name="verified" class="text-[20px] text-secondary" />
                    @endif
                </h1>
                <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                    <span class="rounded-md bg-surface-container-low px-2 py-0.5 text-[12px] font-semibold text-on-surface">{{ $seller->is_pro ? 'Professionnel' : 'Particulier' }}</span>
                    @if($seller->is_verified)
                        <span class="inline-flex items-center gap-1 rounded-md bg-secondary-container text-on-secondary-container px-2 py-0.5 text-[12px] font-bold">Vendeur vérifié</span>
                    @endif
                    @if($seller->delivers)
                        <span class="inline-flex items-center gap-1 rounded-md bg-primary-fixed text-on-primary-fixed-variant px-2 py-0.5 text-[12px] font-bold"><x-icon name="local_shipping" class="text-[13px]" /> Livraison</span>
                    @endif
                </div>

                @if($seller->bio)
                    <div class="mt-3 max-w-3xl" x-data="{ more: false }">
                        <p class="text-[14px] leading-6 text-on-surface whitespace-pre-line break-words" :class="more ? '' : 'line-clamp-3'">{{ $seller->bio }}</p>
                        @if(mb_strlen($seller->bio) > 180)
                            <button type="button" @click="more = !more" class="mt-1 text-[13px] font-semibold text-secondary" x-text="more ? 'Voir moins' : 'Voir plus'">Voir plus</button>
                        @endif
                    </div>
                @endif

                {{-- Contact: uses the number already public on the seller's listings --}}
                @if($contactPhone && ! $isMe)
                    <div class="mt-4 grid grid-cols-[1fr_1fr_auto] gap-2 max-w-md">
                        <a href="{{ Phone::whatsappUrl($contactPhone, 'Bonjour '.$seller->name.', je vous contacte depuis votre boutique AkibaShop : '.$shareUrl) }}" target="_blank" rel="noopener" class="btn btn-whatsapp"><x-icon name="whatsapp" class="text-[18px]" /> WhatsApp</a>
                        <a href="{{ Phone::telUrl($contactPhone) }}" class="btn btn-outline"><x-icon name="phone" class="text-[18px]" /> Appeler</a>
                        <button type="button" x-data @click="navigator.share ? navigator.share({ title: @js($seller->name), url: @js($shareUrl) }) : navigator.clipboard.writeText(@js($shareUrl))" class="btn btn-outline !px-3" aria-label="Partager la boutique"><x-icon name="share" class="text-[18px]" /></button>
                    </div>
                @endif
            </div>

            {{-- Practical details --}}
            <dl class="grid sm:grid-cols-2 lg:grid-cols-4 border-t border-surface-variant">
                @foreach($details as [$icon, $label, $value])
                    <div class="flex items-start gap-3 px-margin-mobile md:px-6 py-3 border-b sm:border-b-0 sm:border-r last:border-0 border-surface-variant">
                        <span class="w-9 h-9 shrink-0 rounded-xl bg-surface-container-low text-primary flex items-center justify-center"><x-icon :name="$icon" class="text-[18px]" /></span>
                        <div class="min-w-0">
                            <dt class="text-[12px] text-on-surface-variant">{{ $label }}</dt>
                            <dd class="text-[14px] font-semibold text-on-surface break-words">{{ $value }}</dd>
                        </div>
                    </div>
                @endforeach
            </dl>
        </section>

        {{-- Listings --}}
        <section class="px-margin-mobile md:px-0 pt-5">
            <h2 class="text-[18px] font-extrabold text-on-surface mb-3">Annonces de la boutique</h2>
            @if($listings->isEmpty())
                <x-empty-state icon="listings" title="Aucune annonce en ligne" :text="$isMe ? 'Publiez votre première annonce pour remplir votre boutique.' : 'Cette boutique n’a pas d’annonce visible pour le moment.'">
                    @if($isMe)
                        <a href="{{ route('listings.create') }}" class="btn btn-accent">Déposer une annonce</a>
                    @endif
                </x-empty-state>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5 md:gap-4">
                    @foreach($listings as $listing)
                        <x-listing-card :listing="$listing" />
                    @endforeach
                </div>
                <div class="mt-8 flex justify-center">{{ $listings->onEachSide(1)->links('pagination.akiba') }}</div>
            @endif
        </section>
    </div>
</x-layouts.app>
