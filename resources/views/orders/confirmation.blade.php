<x-layouts.app title="Commande confirmée — AkibaShop" :hide-footer="true">
    <div class="flex items-center justify-center px-margin-mobile md:px-margin-desktop py-12">
        <main class="w-full max-w-2xl bg-surface-container-lowest rounded-xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.05)] p-8 md:p-12 flex flex-col items-center text-center relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-20 -mr-20 w-64 h-64 bg-primary-container opacity-20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="w-24 h-24 rounded-full bg-surface-container-high flex items-center justify-center mb-8 z-10">
<x-icon name="check_circle" class="text-primary" style="font-size: 48px;" :filled="true" />
            </div>

            <h1 class="font-headline-md text-headline-md md:font-headline-lg md:text-headline-lg text-primary mb-4 z-10">
                Merci pour votre commande
            </h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant mb-10 z-10">
                Votre commande <span class="font-bold text-primary">#{{ $order->order_number }}</span> a bien été confirmée et est en cours de préparation.
            </p>

            <div class="w-full max-w-md text-left z-10 mb-12">
                <h2 class="font-headline-md text-headline-md text-primary mb-6 text-center">Prochaines étapes</h2>
                <div class="relative border-l border-outline-variant ml-4 md:ml-6 space-y-8">
                    @php
                        $steps = [
                            ['title' => 'En préparation', 'desc' => 'Nous préparons avec soin vos articles dans notre atelier.'],
                            ['title' => 'Expédition', 'desc' => 'Votre colis sera remis à notre transporteur partenaire.'],
                            ['title' => 'Livraison', 'desc' => 'Réception prévue à l\'adresse indiquée sous 7 à 14 jours.'],
                        ];
                    @endphp
                    @foreach($steps as $i => $step)
                        <div class="relative pl-8 {{ $i > 0 ? 'opacity-60' : '' }}">
                            @if($i === 0)
                                <div class="absolute left-[-11px] top-1 w-5 h-5 rounded-full bg-primary flex items-center justify-center">
                                    <div class="w-2 h-2 rounded-full bg-surface-container-lowest"></div>
                                </div>
                            @else
                                <div class="absolute left-[-9px] top-1 w-4 h-4 rounded-full bg-surface-container-high border-2 border-outline-variant"></div>
                            @endif
                            <h3 class="font-label-sm text-label-sm text-primary uppercase tracking-widest mb-1">{{ $step['title'] }}</h3>
                            <p class="font-body-md text-body-md text-on-surface-variant">{{ $step['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-col sm:flex-row w-full max-w-md gap-4 z-10">
                <a href="{{ route('account.orders.show', $order) }}" class="flex-1 bg-primary text-on-primary font-label-sm text-label-sm uppercase tracking-widest py-4 rounded-full hover:bg-surface-tint transition-colors duration-300 text-center">
                    Suivre ma commande
                </a>
                <a href="{{ route('home') }}" class="flex-1 bg-transparent border border-outline text-primary font-label-sm text-label-sm uppercase tracking-widest py-4 rounded-full hover:bg-surface-container-low transition-colors duration-300 text-center">
                    Retour à l'accueil
                </a>
            </div>

            <p class="font-label-sm text-label-sm text-on-surface-variant mt-8 text-center max-w-sm mx-auto z-10">
                Un e-mail de confirmation contenant les détails de votre commande vous a été envoyé.
            </p>
        </main>
    </div>
</x-layouts.app>
