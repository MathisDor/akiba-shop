@php use App\Enums\ListingStatus; @endphp

<x-layouts.admin active="listings" title="Modération">
    <a href="{{ route('admin.listings.index', ['statut' => $listing->status->value]) }}" class="inline-flex items-center gap-1 text-[13px] font-semibold text-secondary hover:underline mb-4">
        <x-icon name="arrow_back" class="text-[16px]" /> Retour à la liste
    </a>

    @session('status')
        <div class="mb-4 rounded-xl bg-primary-fixed text-on-primary-fixed px-4 py-3 text-[14px]">{{ $value }}</div>
    @endsession

    <div class="grid lg:grid-cols-[1fr_340px] gap-4 md:gap-6 items-start">
        <div class="flex flex-col gap-4 min-w-0">
            <section class="card p-5 flex flex-col gap-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-md px-2 py-0.5 text-[12px] font-bold {{ $listing->status->badgeClasses() }}">{{ $listing->status->label() }}</span>
                    <span class="rounded-md bg-surface-container-low px-2 py-0.5 text-[12px] font-semibold">{{ $listing->category->parent?->name ? $listing->category->parent->name.' › ' : '' }}{{ $listing->category->name }}</span>
                    @if($listing->condition)
                        <span class="rounded-md bg-surface-container-low px-2 py-0.5 text-[12px] font-semibold">{{ $listing->condition->label() }}</span>
                    @endif
                    @if($listing->isVisible())
                        <a href="{{ route('listings.show', $listing) }}" target="_blank" class="text-[12px] font-semibold text-secondary hover:underline">Voir sur le site ↗</a>
                    @endif
                </div>
                <h2 class="text-headline-md text-on-surface break-words">{{ $listing->title }}</h2>
                <p class="text-[24px] font-extrabold text-primary">{{ $listing->formattedPrice() }} @if($listing->is_negotiable)<span class="text-[13px] font-semibold text-on-surface-variant">négociable</span>@endif</p>
                <p class="text-[13px] text-on-surface-variant">{{ $listing->location() }} · déposée {{ $listing->created_at->diffForHumans() }} · {{ $listing->views }} vues @if($listing->expires_at) · expire le {{ $listing->expires_at->format('d/m/Y') }} @endif</p>
                @if($listing->rejection_reason)
                    <p class="text-[13px] text-error">Motif du refus : {{ $listing->rejection_reason }}</p>
                @endif
            </section>

            @if($listing->images->isNotEmpty())
                <section class="card p-3 grid grid-cols-2 sm:grid-cols-4 gap-2">
                    @foreach($listing->images as $image)
                        <a href="{{ $image->url() }}" target="_blank" class="aspect-square rounded-lg overflow-hidden bg-surface-container-low">
                            <img src="{{ $image->url() }}" alt="" loading="lazy" class="w-full h-full object-cover" />
                        </a>
                    @endforeach
                </section>
            @endif

            @if($specs = $listing->specList())
                <section class="card p-5">
                    <dl class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($specs as $label => $value)
                            <div><dt class="text-[12px] text-on-surface-variant">{{ $label }}</dt><dd class="text-[14px] font-bold">{{ $value }}</dd></div>
                        @endforeach
                    </dl>
                </section>
            @endif

            <section class="card p-5">
                <h3 class="text-[15px] font-extrabold mb-2">Description</h3>
                <div class="text-[14px] leading-6 whitespace-pre-line break-words">{{ $listing->description }}</div>
            </section>

            @if($listing->reports->isNotEmpty())
                <section class="card p-5">
                    <h3 class="text-[15px] font-extrabold mb-3">Signalements ({{ $listing->reports->count() }})</h3>
                    <ul class="flex flex-col gap-2 text-[14px]">
                        @foreach($listing->reports as $report)
                            <li class="rounded-lg bg-surface-container-low p-3">
                                <p class="font-semibold">{{ $report->reason->label() }} <span class="font-normal text-on-surface-variant">— {{ $report->status->label() }}, {{ $report->created_at->diffForHumans() }} par {{ $report->user?->name ?? 'un visiteur' }}</span></p>
                                @if($report->details)<p class="mt-1">{{ $report->details }}</p>@endif
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
        </div>

        <aside class="flex flex-col gap-4 lg:sticky lg:top-24">
            <section class="card p-5 flex flex-col gap-3">
                <h3 class="text-[15px] font-extrabold">Décision</h3>
                @if($listing->status !== ListingStatus::Publiee)
                    <form method="POST" action="{{ route('admin.listings.approve', $listing) }}">
                        @csrf @method('PATCH')
                        <button class="btn btn-primary w-full"><x-icon name="check" class="text-[18px]" /> Approuver et publier</button>
                    </form>
                @endif

                @if($listing->status !== ListingStatus::Refusee)
                    <form method="POST" action="{{ route('admin.listings.reject', $listing) }}" class="flex flex-col gap-2" x-data="{ reason: @js(old('rejection_reason', '')) }">
                        @csrf @method('PATCH')
                        <label for="rejection_reason" class="field-label !mb-0">Motif du refus (visible par le vendeur)</label>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach(['Photos ne correspondant pas à l’article', 'Description insuffisante', 'Article interdit', 'Annonce en double', 'Mauvaise catégorie', 'Prix incohérent'] as $preset)
                                <button type="button" @click="reason = @js($preset)" class="rounded-full bg-surface-container-low px-2.5 py-1 text-[12px] hover:bg-surface-container">{{ $preset }}</button>
                            @endforeach
                        </div>
                        <input id="rejection_reason" name="rejection_reason" x-model="reason" maxlength="255" class="field" required />
                        @error('rejection_reason') <p class="field-error">{{ $message }}</p> @enderror
                        <button class="btn btn-outline w-full !text-error">Refuser</button>
                    </form>
                @endif

                @if($listing->isVisible())
                    <form method="POST" action="{{ route('admin.listings.boost', $listing) }}">
                        @csrf @method('PATCH')
                        <button class="btn btn-accent w-full"><x-icon name="boost" class="text-[18px]" />
                            {{ $listing->isBoosted() ? 'Retirer de la une (jusqu’au '.$listing->boosted_until->format('d/m').')' : 'Mettre à la une '.config('marketplace.boost_days').' jours' }}
                        </button>
                    </form>
                @endif

                <form method="POST" action="{{ route('admin.listings.destroy', $listing) }}" onsubmit="return confirm('Supprimer définitivement cette annonce et ses photos ?')" class="pt-2 border-t border-surface-variant">
                    @csrf @method('DELETE')
                    <button class="text-[13px] font-semibold text-error hover:underline">Supprimer définitivement</button>
                </form>
            </section>

            <section class="card p-5 flex flex-col gap-1 text-[14px]">
                <h3 class="text-[15px] font-extrabold mb-1">Vendeur</h3>
                <a href="{{ route('admin.users.show', $listing->user) }}" class="font-bold text-secondary hover:underline">{{ $listing->user->name }}</a>
                <p class="text-on-surface-variant">{{ $listing->user->email }}</p>
                <p>Contact annonce : <a href="{{ $listing->telUrl() }}" class="font-semibold">{{ $listing->formattedPhone() }}</a> {{ $listing->accepts_whatsapp ? '(WhatsApp)' : '' }}</p>
                <p class="text-on-surface-variant">{{ $listing->user->listings_count }} annonce(s) · inscrit {{ $listing->user->created_at->diffForHumans() }}</p>
                <p class="flex gap-2 mt-1">
                    @if($listing->user->is_pro)<span class="rounded bg-secondary-container text-on-secondary-container px-1.5 text-[11px] font-bold">Pro</span>@endif
                    @if($listing->user->is_verified)<span class="rounded bg-secondary-container text-on-secondary-container px-1.5 text-[11px] font-bold">Vérifié</span>@endif
                    @unless($listing->user->is_active)<span class="rounded bg-error-container text-on-error-container px-1.5 text-[11px] font-bold">Suspendu</span>@endunless
                </p>
            </section>
        </aside>
    </div>
</x-layouts.admin>
