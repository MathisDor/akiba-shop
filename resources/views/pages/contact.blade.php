@php
    $email = config('marketplace.contact_email');
    $whatsapp = \App\Support\GabonPhone::normalize(config('marketplace.contact_whatsapp'));
@endphp

<x-page title="Contact" intro="Une question, un problème avec une annonce ou un compte ? Notre équipe vous répond.">
    @if($email || $whatsapp)
        <div class="flex flex-wrap gap-3">
            @if($whatsapp)
                <a href="{{ \App\Support\GabonPhone::whatsappUrl($whatsapp, 'Bonjour AkibaShop, ') }}" target="_blank" rel="noopener" class="btn btn-whatsapp !no-underline !text-on-whatsapp"><x-icon name="whatsapp" class="text-[18px]" /> WhatsApp</a>
            @endif
            @if($email)
                <a href="mailto:{{ $email }}" class="btn btn-outline !no-underline !text-on-surface"><x-icon name="mail" class="text-[18px]" /> {{ $email }}</a>
            @endif
        </div>
    @endif

    <h2>Une annonce vous semble suspecte ?</h2>
    <p>Utilisez le bouton « Signaler cette annonce » en bas de sa page : votre signalement arrive directement à l’équipe de modération.</p>

    <h2>Gérer vos annonces</h2>
    <p>Modifiez, renouvelez, marquez comme vendue ou supprimez vos annonces depuis <a href="{{ route('account.listings') }}">Mes annonces</a>.</p>
</x-page>
