@php use App\Enums\ListingStatus; @endphp

<x-layouts.app active="account" title="Mes annonces — AkibaShop">
    <div class="max-w-4xl mx-auto px-margin-mobile md:px-margin-desktop pt-6">
        <div class="flex items-center justify-between gap-3 mb-4">
            <h1 class="text-headline-lg text-on-surface">Mes annonces</h1>
            <a href="{{ route('listings.create') }}" class="btn btn-accent btn-sm"><x-icon name="add" class="text-[18px]" /> Nouvelle</a>
        </div>

        @include('account.partials.nav', ['current' => 'listings'])

        @if($listings->isEmpty())
            <x-empty-state icon="megaphone" title="Vous n’avez pas encore d’annonce" text="Publiez votre première annonce gratuitement en 2 minutes.">
                <a href="{{ route('listings.create') }}" class="btn btn-accent">Déposer une annonce</a>
            </x-empty-state>
        @else
            <ul class="flex flex-col gap-3">
                @foreach($listings as $listing)
                    @php($cover = $listing->coverImage())
                    <li class="card p-3 md:p-4 flex gap-3 md:gap-4">
                        <a href="{{ route('listings.show', $listing) }}" class="w-24 h-24 md:w-32 md:h-28 shrink-0 rounded-xl overflow-hidden bg-surface-container-low flex items-center justify-center">
                            @if($cover)
                                <img src="{{ $cover->url() }}" alt="" loading="lazy" class="w-full h-full object-cover" />
                            @else
                                <x-icon name="image" class="text-[28px] text-outline" />
                            @endif
                        </a>
                        <div class="flex-1 min-w-0 flex flex-col gap-1">
                            <div class="flex flex-wrap items-center gap-2">
                                @if($listing->status === ListingStatus::Publiee && $listing->isExpired())
                                    <span class="rounded-md bg-surface-container-high text-on-surface-variant px-2 py-0.5 text-[11px] font-bold">Expirée</span>
                                @else
                                    <span class="rounded-md px-2 py-0.5 text-[11px] font-bold {{ $listing->status->badgeClasses() }}">{{ $listing->status->label() }}</span>
                                @endif
                                @if($listing->isBoosted())
                                    <span class="rounded-md bg-tertiary text-on-tertiary px-2 py-0.5 text-[11px] font-bold">À la une</span>
                                @endif
                            </div>
                            <a href="{{ route('listings.show', $listing) }}" class="text-[15px] font-bold text-on-surface hover:text-primary truncate">{{ $listing->title }}</a>
                            <p class="text-[14px] font-extrabold text-on-surface">{{ $listing->formattedPrice() }}</p>
                            <p class="text-[12px] text-on-surface-variant">
                                {{ $listing->city->name }} · {{ number_format($listing->views, 0, ',', ' ') }} vue{{ $listing->views > 1 ? 's' : '' }}
                                @if($listing->status === ListingStatus::Publiee && $listing->expires_at)
                                    · {{ $listing->isExpired() ? 'expirée' : 'expire' }} {{ $listing->expires_at->diffForHumans() }}
                                @endif
                            </p>
                            @if($listing->status === ListingStatus::Refusee && $listing->rejection_reason)
                                <p class="text-[12px] text-error">Motif : {{ $listing->rejection_reason }}</p>
                            @endif

                            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-2 text-[13px] font-semibold">
                                @if($listing->status !== ListingStatus::Vendue)
                                    <a href="{{ route('account.listings.edit', $listing) }}" class="text-secondary hover:underline">Modifier</a>
                                @endif
                                @if($listing->isRenewable())
                                    <form method="POST" action="{{ route('account.listings.renew', $listing) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-primary hover:underline">Renouveler</button>
                                    </form>
                                @endif
                                @if($listing->status === ListingStatus::Publiee)
                                    <form method="POST" action="{{ route('account.listings.sold', $listing) }}" onsubmit="return confirm('Marquer cette annonce comme vendue ? Elle ne sera plus visible.')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-on-surface hover:underline">Marquer vendue</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('account.listings.destroy', $listing) }}" onsubmit="return confirm('Supprimer définitivement cette annonce ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-error hover:underline">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="mt-6 flex justify-center">{{ $listings->links('pagination.akiba') }}</div>
        @endif
    </div>
</x-layouts.app>
