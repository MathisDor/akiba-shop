<footer class="mt-section-gap bg-inverse-surface text-inverse-on-surface">
    <div class="brand-band"></div>
    <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-10 md:py-14 grid grid-cols-2 md:grid-cols-4 gap-8">
        <div class="col-span-2 md:col-span-1 flex flex-col gap-4">
            <x-logo inverse />
            <p class="text-[14px] leading-6 text-inverse-on-surface/75">Les petites annonces gratuites entre particuliers et professionnels. Achetez, vendez et trouvez ce qu’il vous faut près de chez vous.</p>
            <a href="{{ route('listings.create') }}" class="btn btn-accent self-start">
                <x-icon name="add_circle" class="text-[20px]" />
                Déposer une annonce
            </a>
        </div>

        <div>
            <h2 class="text-[13px] font-bold uppercase tracking-wider text-inverse-on-surface/60 mb-4">Catégories</h2>
            <ul class="flex flex-col gap-2.5 text-[14px]">
                @foreach($navCategories->take(6) as $category)
                    <li><a href="{{ route('categories.show', $category) }}" class="text-inverse-on-surface/85 hover:text-brand-accent transition-colors">{{ $category->name }}</a></li>
                @endforeach
                <li><a href="{{ route('categories.index') }}" class="text-inverse-on-surface/85 hover:text-brand-accent transition-colors">Toutes les catégories</a></li>
            </ul>
        </div>

        <div>
            <h2 class="text-[13px] font-bold uppercase tracking-wider text-inverse-on-surface/60 mb-4">AkibaShop</h2>
            <ul class="flex flex-col gap-2.5 text-[14px]">
                <li><a href="{{ route('pages.about') }}" class="text-inverse-on-surface/85 hover:text-brand-accent transition-colors">À propos</a></li>
                <li><a href="{{ route('pages.safety') }}" class="text-inverse-on-surface/85 hover:text-brand-accent transition-colors">Conseils de sécurité</a></li>
                <li><a href="{{ route('pages.contact') }}" class="text-inverse-on-surface/85 hover:text-brand-accent transition-colors">Contact</a></li>
                <li><a href="{{ route('pages.terms') }}" class="text-inverse-on-surface/85 hover:text-brand-accent transition-colors">Conditions d’utilisation</a></li>
                <li><a href="{{ route('pages.privacy') }}" class="text-inverse-on-surface/85 hover:text-brand-accent transition-colors">Confidentialité</a></li>
            </ul>
        </div>

        <div>
            <h2 class="text-[13px] font-bold uppercase tracking-wider text-inverse-on-surface/60 mb-4">Mon compte</h2>
            <ul class="flex flex-col gap-2.5 text-[14px]">
                <li><a href="{{ route('listings.create') }}" class="text-inverse-on-surface/85 hover:text-brand-accent transition-colors">Déposer une annonce</a></li>
                <li><a href="{{ route('account.listings') }}" class="text-inverse-on-surface/85 hover:text-brand-accent transition-colors">Mes annonces</a></li>
                <li><a href="{{ route('account.favorites') }}" class="text-inverse-on-surface/85 hover:text-brand-accent transition-colors">Mes favoris</a></li>
                @guest
                    <li><a href="{{ route('register') }}" class="text-inverse-on-surface/85 hover:text-brand-accent transition-colors">Créer un compte</a></li>
                @endguest
            </ul>
        </div>
    </div>

    <div class="border-t border-inverse-on-surface/10">
        <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-5 flex items-center justify-center text-[12px] text-inverse-on-surface/60">
            <p class="text-center">© {{ date('Y') }} AkibaShop · Développé par <a href="https://dibundiweb.netlify.app/" target="_blank" rel="noopener noreferrer" class="text-inverse-on-surface/85 hover:text-brand-accent font-semibold">DibundiWeb</a></p>
        </div>
    </div>
</footer>
