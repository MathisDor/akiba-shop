<footer class="block bg-surface-container-low border-t border-surface-variant mt-section-gap">
    <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-12 md:py-16 grid grid-cols-2 md:grid-cols-4 gap-8">
        <div class="col-span-2 md:col-span-1">
            <img src="{{ asset('logo/logo1.PNG') }}" alt="AkibaShop" class="h-10 w-auto object-contain rounded mb-4" />
            <p class="font-body-md text-body-md text-on-surface-variant">La mode qui vous ressemble. Livraison partout à Libreville.</p>
        </div>
        <div>
            <h4 class="font-label-lg text-label-lg uppercase text-on-surface mb-4">Boutique</h4>
            <ul class="flex flex-col gap-2 font-body-md text-body-md text-on-surface-variant">
                <li><a href="{{ route('catalog.gender', 'femme') }}" class="hover:text-primary transition-colors">Femme</a></li>
                <li><a href="{{ route('catalog.gender', 'homme') }}" class="hover:text-primary transition-colors">Homme</a></li>
                <li><a href="{{ route('catalog.index', ['sort' => 'nouveautes']) }}" class="hover:text-primary transition-colors">Nouveautés</a></li>
                <li><a href="{{ route('catalog.index', ['promo' => 1]) }}" class="hover:text-primary transition-colors">Promotions</a></li>
            </ul>
        </div>
        <div>
            <h4 class="font-label-lg text-label-lg uppercase text-on-surface mb-4">Aide</h4>
            <ul class="flex flex-col gap-2 font-body-md text-body-md text-on-surface-variant">
                <li><a href="{{ route('account.orders') }}" class="hover:text-primary transition-colors">Suivre ma commande</a></li>
                <li><span class="cursor-default">Livraison &amp; Retours</span></li>
                <li><span class="cursor-default">Contact</span></li>
            </ul>
        </div>
        <div>
            <h4 class="font-label-lg text-label-lg uppercase text-on-surface mb-4">Mon compte</h4>
            <ul class="flex flex-col gap-2 font-body-md text-body-md text-on-surface-variant">
                @auth
                    <li><a href="{{ route('account.profile') }}" class="hover:text-primary transition-colors">Mon profil</a></li>
                @else
                    <li><a href="{{ route('login') }}" class="hover:text-primary transition-colors">Se connecter</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-primary transition-colors">Créer un compte</a></li>
                @endauth
            </ul>
        </div>
    </div>
    <div class="border-t border-surface-variant py-6 flex flex-col items-center gap-2 text-center px-margin-mobile">
        <p class="font-label-sm text-[11px] text-outline uppercase tracking-widest">© {{ date('Y') }} AkibaShop Libreville. Tous droits réservés.</p>
        <p class="font-label-sm text-[11px] text-outline">
            Développé par
            <a href="https://dibundiweb.netlify.app/" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline font-semibold">DibundiWeb</a>
        </p>
    </div>
</footer>
