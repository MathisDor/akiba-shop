<x-page title="Conditions d’utilisation" intro="Les règles qui s’appliquent à toute personne qui consulte AkibaShop ou y publie une annonce.">
    <h2>1. Le service</h2>
    <p>AkibaShop met en relation des particuliers et des professionnels qui souhaitent vendre, louer, acheter ou proposer des biens et services au Gabon. AkibaShop n’est pas partie aux transactions conclues entre utilisateurs.</p>

    <h2>2. Compte</h2>
    <p>La publication d’annonces nécessite un compte. Vous vous engagez à fournir des informations exactes, notamment un numéro de téléphone sur lequel vous êtes joignable, et à garder votre mot de passe confidentiel.</p>

    <h2>3. Annonces</h2>
    <ul>
        <li>Chaque annonce doit décrire un bien ou un service réel, disponible, que vous êtes en droit de proposer.</li>
        <li>Le prix est indiqué en francs CFA (FCFA). Les photos doivent correspondre à l’article proposé.</li>
        <li>Sont interdits : les produits illégaux, contrefaits ou dangereux, les annonces trompeuses, les doublons et les contenus offensants.</li>
        <li>Les annonces sont relues avant publication. AkibaShop peut refuser, retirer une annonce ou suspendre un compte qui ne respecte pas ces règles.</li>
        <li>Une annonce reste en ligne {{ config('marketplace.listing_lifetime_days') }} jours et peut être renouvelée.</li>
    </ul>

    <h2>4. Transactions</h2>
    <p>Les échanges, paiements et livraisons se font directement entre l’acheteur et le vendeur, sous leur seule responsabilité. Consultez nos <a href="{{ route('pages.safety') }}">conseils de sécurité</a>.</p>

    <h2>5. Responsabilité</h2>
    <p>AkibaShop ne garantit ni la qualité, ni la conformité, ni la légalité des biens et services proposés, ni la capacité des utilisateurs à conclure une transaction.</p>

    <h2>6. Données personnelles</h2>
    <p>Voir notre <a href="{{ route('pages.privacy') }}">politique de confidentialité</a>.</p>
</x-page>
