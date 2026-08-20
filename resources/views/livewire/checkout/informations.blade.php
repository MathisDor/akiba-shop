<div class="pt-4">
    <div class="mb-10 text-center md:text-left">
        <h1 class="font-headline-lg-mobile text-headline-lg-mobile md:font-headline-lg md:text-headline-lg text-primary mb-2">
            Informations Personnelles
        </h1>
        <p class="font-body-md text-body-md text-on-surface-variant">
            Veuillez saisir vos coordonnées pour la livraison de votre commande.
        </p>
    </div>

    <form wire:submit="continue" class="flex flex-col gap-8 w-full">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-6">
            <div class="relative-input-group">
                <input wire:model="firstName" class="input-underline font-body-md text-body-md text-primary h-10" placeholder=" " type="text" />
                <label class="font-body-md">Prénom</label>
            </div>
            <div class="relative-input-group">
                <input wire:model="lastName" class="input-underline font-body-md text-body-md text-primary h-10" placeholder=" " type="text" />
                <label class="font-body-md">Nom</label>
            </div>
        </div>
        @error('firstName') <p class="-mt-6 text-error font-label-sm text-label-sm">{{ $message }}</p> @enderror
        @error('lastName') <p class="-mt-6 text-error font-label-sm text-label-sm">{{ $message }}</p> @enderror

        <div class="relative-input-group">
            <input wire:model="email" class="input-underline font-body-md text-body-md text-primary h-10" placeholder=" " type="email" />
            <label class="font-body-md">Adresse Email</label>
        </div>
        @error('email') <p class="-mt-4 text-error font-label-sm text-label-sm">{{ $message }}</p> @enderror

        <div class="relative-input-group flex items-end">
            <div class="flex items-center border-b border-outline-variant pb-2 mr-4 text-on-surface-variant font-body-md h-10">
                <span class="mr-1 mt-1">🇬🇦</span>
                <span>+241</span>
            </div>
            <div class="flex-grow relative">
                <input wire:model="phone" class="input-underline font-body-md text-body-md text-primary h-10" placeholder=" " type="tel" />
                <label class="font-body-md">Numéro de téléphone</label>
            </div>
        </div>
        @error('phone') <p class="-mt-4 text-error font-label-sm text-label-sm">{{ $message }}</p> @enderror

        <div class="mt-4 flex items-start">
            <div class="flex items-center h-5">
                <input wire:model="newsletter" type="checkbox" class="w-5 h-5 rounded-sm border-outline-variant text-primary focus:ring-primary bg-transparent" />
            </div>
            <div class="ml-3">
                <label class="font-body-md text-body-md text-on-surface-variant cursor-pointer">
                    M'inscrire aux exclusivités et nouveautés AkibaShop
                </label>
            </div>
        </div>

        <div class="hidden md:flex justify-end mt-12">
            <button type="submit" class="bg-primary text-on-primary h-12 px-8 rounded-full font-label-sm text-label-sm uppercase tracking-widest hover:bg-tertiary transition-colors duration-300 flex items-center justify-center min-w-[240px]">
                Continuer vers la livraison
<x-icon name="arrow_forward" class="ml-2 text-[18px]" />
            </button>
        </div>

        <div class="md:hidden fixed bottom-0 left-0 w-full bg-surface/95 backdrop-blur-md border-t border-surface-container-high p-4 z-40 shadow-[0_-10px_40px_rgba(0,0,0,0.05)]">
            <button type="submit" class="w-full bg-primary text-on-primary h-[56px] rounded-full font-label-sm text-label-sm uppercase tracking-widest hover:opacity-90 active:scale-[0.98] transition-all duration-200 flex items-center justify-center">
                Continuer vers la livraison
            </button>
        </div>
    </form>
</div>
