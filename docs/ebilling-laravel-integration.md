# Intégrer E-Billing (Digitech Africa) dans un projet Laravel

Guide pratique pour brancher **E-Billing** — l'agrégateur de paiement mobile money gabonais
(Airtel Money, Moov Money, Visa/Mastercard via Orabank) — sur un projet Laravel. Basé sur le
guide officiel *"E-Billing — Guide d'Intégration PAYIN & PAYOUT" v1.0 (Digitech Africa, mars
2025)* et sur une intégration réelle (PAYIN uniquement, flux USSD push) faite dans ce dépôt.

Fichiers de référence dans ce projet :

| Rôle | Fichier |
|---|---|
| Interface du gateway | [`app/Services/Payments/PaymentGatewayInterface.php`](../app/Services/Payments/PaymentGatewayInterface.php) |
| Implémentation E-Billing | [`app/Services/Payments/EbillingPaymentGateway.php`](../app/Services/Payments/EbillingPaymentGateway.php) |
| Résultat de paiement | [`app/Services/Payments/PaymentResult.php`](../app/Services/Payments/PaymentResult.php) |
| Webhook de confirmation | [`app/Http/Controllers/Webhooks/EbillingCallbackController.php`](../app/Http/Controllers/Webhooks/EbillingCallbackController.php) |
| Création de commande | [`app/Actions/PlaceOrderAction.php`](../app/Actions/PlaceOrderAction.php) |
| Page d'attente (polling) | [`app/Livewire/Checkout/PaymentPending.php`](../app/Livewire/Checkout/PaymentPending.php) |
| Config | [`config/services.php`](../config/services.php) |
| Tests | [`tests/Feature/EbillingPaymentTest.php`](../tests/Feature/EbillingPaymentTest.php) |

---

## 1. Vue d'ensemble

E-Billing **n'est pas un portefeuille mobile** et **ne gère pas le flux de la transaction** —
c'est l'opérateur (Airtel, Moov, Orabank) qui valide le paiement. E-Billing assure juste la
traçabilité entre le client, l'opérateur et le marchand.

Deux API distinctes, deux authentifications distinctes :

- **PAYIN** (encaissement) — Basic Auth, synchrone à la création, **asynchrone** pour la
  confirmation du paiement (webhook).
- **PAYOUT** (décaissement, ex. remboursements) — OAuth2 Bearer token, appelée **SHAP**, une
  API séparée.

Opérateurs disponibles :

- **Airtel Money** — numéros commençant par `07` (9 chiffres)
- **Moov Money** — numéros commençant par `06` (9 chiffres)
- **Visa / Mastercard** — via le portail bancaire Orabank

## 2. Environnements & URLs

### 2.1 E-Billing (PAYIN)

| Environnement | URL |
|---|---|
| LAB | `https://lab.billing-easy.net` |
| LAB API | `https://lab.billing-easy.net/api/v1/merchant/` |
| LAB Portail | `https://test.billing-easy.net` |
| PROD | `https://stg.billing-easy.com` |
| PROD API | `https://stg.billing-easy.com/api/v1/merchant/` |
| Simulateur (test du callback) | `https://simu.billing-easy.net` |

### 2.2 SHAP (PAYOUT)

| Environnement | URL |
|---|---|
| LAB | `https://test.billing-easy.net/shap/api/v1/merchant/` |
| PROD | `https://staging.billing-easy.net/shap/api/v1/merchant/` |
| LAB Profil | `https://test.billing-easy.net/manage/profile` |
| PROD Profil | `https://staging.billing-easy.net/manage/profile` |

> Le LAB est fonctionnellement identique à la PROD. Le passage en PROD consiste **uniquement**
> à changer les URLs et les credentials (créés et fournis par Digitech Africa une fois le LAB
> validé de bout en bout).

## 3. Prérequis

1. Créer un compte marchand LAB : `https://lab.billing-easy.net/merchant/registrations/new`
2. Récupérer dans le profil : `Username`, `Password`, `SharedKey`.
3. Prévoir une table (ou réutiliser une table `orders`/`payments` existante) qui trace le cycle
   de vie du paiement :

   | Colonne | Type | Description |
   |---|---|---|
   | `reference_id` / `order_number` | string, unique | Votre référence — devient `external_reference` |
   | `status` | enum/int | `created` → `pending` → `paid` (ou `failed`) |
   | `ebilling_id` (`bill_id`) | string | ID de la facture côté E-Billing |
   | `operator` | string | Opérateur ayant validé (`airtelmoney`, `moovmoney4`...) |
   | `transaction_id` | string | ID de transaction côté opérateur |
   | `paid_at` | timestamp nullable | Date de confirmation |

## 4. Configuration Laravel

**`.env`**

```env
EBILLING_BASE_URL=https://lab.billing-easy.net/api/v1/merchant/
EBILLING_USERNAME=
EBILLING_SHARED_KEY=
EBILLING_EXPIRY_MINUTES=60
```

**`config/services.php`**

```php
'ebilling' => [
    'base_url' => env('EBILLING_BASE_URL', 'https://lab.billing-easy.net/api/v1/merchant/'),
    'username' => env('EBILLING_USERNAME'),
    'shared_key' => env('EBILLING_SHARED_KEY'),
    'expiry_minutes' => env('EBILLING_EXPIRY_MINUTES', 60),
],
```

Ne jamais committer `Username`/`SharedKey` réels — même en LAB. Gardez `.env` hors de git et ne
mettez que des placeholders vides dans `.env.example`.

### Abstraction derrière une interface

Isoler l'appel réseau derrière une interface permet de développer/tester le reste du checkout
avec un gateway simulé (`ManualPaymentGateway`), et de basculer vers le vrai fournisseur sans
toucher au flux de commande :

```php
interface PaymentGatewayInterface
{
    public function charge(Order $order, ?string $payerPhone = null): PaymentResult;
}
```

```php
// PaymentResult — le `pending` est ce qui distingue un flux mobile money (async)
// d'un flux synchrone classique (carte simulée, etc.)
final class PaymentResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $reference = null,
        public readonly array $raw = [],
        public readonly bool $pending = false,
    ) {}
}
```

Binding conditionnel dans `AppServiceProvider` — le vrai gateway ne s'active que si les
identifiants sont configurés, donc un déploiement sans `.env` rempli reste inerte au lieu de
planter :

```php
$this->app->bind(PaymentGatewayInterface::class, function ($app) {
    $configured = config('services.ebilling.username') && config('services.ebilling.shared_key');

    return $configured
        ? $app->make(EbillingPaymentGateway::class)
        : $app->make(ManualPaymentGateway::class);
});
```

## 5. PAYIN — Créer la facture (Étape 1)

**Endpoint**

```
POST {base_url}e_bills
Authorization: Basic Base64(Username:SharedKey)
Content-Type: application/json
```

**Paramètres**

| Paramètre | Type | Requis | Description |
|---|---|---|---|
| `amount` | double | ✅ | Montant à collecter (XAF) |
| `short_description` | string | ✅ | Description courte |
| `payer_email` | string | ✅ | Email du client |
| `payer_msisdn` | string | ✅ | Téléphone du client, ex. `077123456` (9 chiffres, sans indicatif) |
| `payer_name` | string | ✅ | Nom complet du client |
| `external_reference` | string | ✅ | Référence unique dans votre système |
| `expiry_period` | integer | non | Durée de validité en minutes (0 = pas d'expiration) |

**Réponse (HTTP 201)**

```json
{
  "e_bill": {
    "bill_id": "5550051928",
    "amount": 15000,
    "currency": "XAF",
    "state": "ready",
    "external_reference": "INV-2025-042",
    "created_at": "2025-03-19T10:30:00+01:00",
    "expiry_period": 60
  }
}
```

**Implémentation Laravel** (`Illuminate\Support\Facades\Http`) :

```php
$response = Http::withBasicAuth($username, $sharedKey)
    ->acceptJson()
    ->timeout(20)
    ->post($baseUrl.'e_bills', [
        'amount' => (int) $order->total,
        'short_description' => "Commande {$order->order_number}",
        'payer_email' => $order->user->email,
        'payer_msisdn' => $payerPhone,
        'payer_name' => $order->address_snapshot['full_name'] ?? $order->user->name,
        'external_reference' => $order->order_number,
        'expiry_period' => 60,
    ]);

if ($response->status() !== 201) {
    throw new RuntimeException($response->json('message') ?? 'Échec de la création de la facture.');
}

$billId = $response->json('e_bill.bill_id');
```

## 6. PAYIN — Déclencher le paiement (Étape 2)

Deux options, à choisir selon le contexte :

### Option A — Redirection vers le portail E-Billing

Convient à un site web classique. Le client est redirigé, choisit son mode de paiement
(y compris carte bancaire) sur une page hébergée par E-Billing.

```
GET https://test.billing-easy.net?invoice={bill_id}&redirect_url={url_succes_marchand}

# Redirection directe vers Visa/Mastercard (Orabank) :
GET https://test.billing-easy.net?invoice={bill_id}&operator=ORABANK_NG&redirect=1&redirect_url={url_succes}
```

C'est la **seule option pour la carte bancaire** — le push USSD ne fonctionne que pour
Airtel/Moov.

### Option B — USSD Push (Airtel Money / Moov Money uniquement)

Pas de redirection : le client reçoit directement un push USSD sur son téléphone pour valider
le paiement. Recommandé si vous avez déjà collecté le numéro mobile money au checkout.

```
POST {base_url}e_bills/{bill_id}/ussd_push
{
  "payer_msisdn": "066123456",
  "payment_system_name": "moovmoney4"   // ou "airtelmoney"
}
```

Réponse succès : `{ "message": "Accepted", "url": null }`

```php
$response = Http::withBasicAuth($username, $sharedKey)
    ->acceptJson()
    ->post($baseUrl."e_bills/{$billId}/ussd_push", [
        'payer_msisdn' => $payerPhone,
        'payment_system_name' => $paymentSystem, // 'airtelmoney' | 'moovmoney4'
    ]);
```

## 7. PAYIN — Callback (Étape 3) — la partie critique

E-Billing appelle votre backend en **HTTP POST** quand l'opérateur confirme le paiement. Le
callback peut arriver **avant ou après** que le client ne revienne sur votre site — ne vous
fiez jamais uniquement à la redirection, la source de vérité est toujours ce callback (+ une
vérification en base au moment d'afficher la confirmation).

**Enregistrement de l'URL** : dans le profil marchand E-Billing → Edit → « Notification URL »,
cochez les champs à recevoir : `transactionid`, `reference`, `paymentsystem`, `amount`,
`billingid`.

**Route** — placez-la dans `routes/api.php` (groupe `api`, sans CSRF, puisque c'est un appel
serveur-à-serveur externe qui ne peut pas fournir de jeton CSRF) :

```php
// routes/api.php
Route::post('/webhooks/ebilling/callback', EbillingCallbackController::class);
```

**Contrôleur** — validez systématiquement la référence ET le montant avant de faire confiance
au callback (aucune signature n'est fournie par l'API, c'est votre seule protection) :

```php
class EbillingCallbackController extends Controller
{
    public function __invoke(Request $request, UpdateOrderStatusAction $updateOrderStatus): JsonResponse
    {
        $reference = $request->input('reference');
        $amount = $request->input('amount');

        if (! $reference) {
            return response()->json(['error' => 'missing reference'], 400);
        }

        $order = Order::where('order_number', $reference)->first();

        if (! $order) {
            return response()->json(['error' => 'order not found'], 404);
        }

        if ($amount !== null && (int) $amount !== (int) $order->total) {
            return response()->json(['error' => 'amount mismatch'], 400);
        }

        // ... marquer le paiement "paid", passer la commande à "payee" ...

        return response()->json(['success' => true], 200); // OBLIGATOIRE en cas de succès
    }
}
```

> **Retournez impérativement un code HTTP 2xx** si le traitement a réussi, 4xx sinon.
> E-Billing s'en sert pour marquer la notification "Processed" et décider de réessayer ou non.
> Un callback non acquitté (pas de 2xx) peut être **retenté** par E-Billing.

**Ne jamais** exposer cette URL de callback dans le frontend ou à l'utilisateur final.

**Tester isolément** : `https://simu.billing-easy.net/callback-url` — renseignez votre URL de
callback, sélectionnez les paramètres, cliquez "Tester l'URL de rappel". Un HTTP 200 confirme
que l'endpoint fonctionne, indépendamment de tout vrai paiement.

## 8. PAYIN — Page de succès (Étape 4)

Après confirmation, E-Billing redirige le client vers `redirect_url`. Cette page doit :

- **Interroger votre propre base de données** pour le statut réel (jamais se fier uniquement à
  la redirection — le callback peut arriver après).
- Afficher montant, opérateur, numéro de transaction, date.

Si vous utilisez le push USSD (Option B, pas de redirection), affichez plutôt un écran
d'attente qui **poll** le statut de la commande (ex. Livewire `wire:poll`) jusqu'à confirmation,
avec un message invitant le client à valider le code USSD reçu sur son téléphone.

## 9. Mobile natif (iOS / Android)

- L'app ne doit **jamais** interroger E-Billing directement.
- Exposez depuis votre backend : `POST /checkout` (retourne `bill_id`) et
  `GET /payment/status/{reference}` (statut lu depuis votre DB, pas depuis E-Billing).
- Pour ramener l'utilisateur dans l'app après un paiement via portail, passez un URI scheme
  personnalisé (`monapp-success://payment-confirmed`) comme `redirect_url`.

## 10. PAYOUT — Décaissement (SHAP)

API séparée, authentification OAuth2. Utile pour remboursements, cashback, paiement de
prestataires.

**Obtenir un token**

```
POST {shap_base}/auth
{ "api_id": "...", "api_secret": "..." }

→ { "access_token": "...", "token_type": "bearer", "expires_in": 7200 }
```

Le token expire après 2h (7200s) — une requête avec un token expiré renvoie `SP0004`,
régénérez-le.

**Créer un payout**

```
POST {shap_base}/payout
Authorization: Bearer {access_token}

{
  "payment_system_name": "airtelmoney",      // airtelmoney | moovmoney4 | INTOUCH_BF_MM | INTOUCH_BF_OM
  "payout": {
    "payee_msisdn": "077123456",
    "amount": 5000,                          // minimum 100 XAF
    "external_reference": "PAYOUT-2025-001",
    "payout_type": "cashback"                // refund | cashback | withdrawal
  }
}
```

Réponse synchrone pour la plupart des opérateurs. Pour les opérateurs **INTOUCH**, le payout
est asynchrone : configurez un « Payout Callback URL » dans le profil SHAP (payload :
`shap_reference`, `external_reference`, `payment_system_name`, `transaction_id`, `amount`,
`status`).

**Autres endpoints utiles**

- `GET {shap_base}/balance` — solde par opérateur.
- `GET {shap_base}/payout?payee_msisdn=...&external_reference=...` — consulter un payout.

## 11. Codes d'erreur

### PAYIN (E-Billing)

| Code | Description |
|---|---|
| 400 | Paramètre manquant ou invalide |
| 401 | Authentification Basic incorrecte (Username/SharedKey) |
| 404 | `bill_id` inexistant |
| 422 | Données métier invalides |
| 500 | Erreur serveur E-Billing |

### PAYOUT (SHAP)

| Code | Description |
|---|---|
| SP0001 – SP0004 | Problème d'authentification (`api_id`/`api_secret`/token) |
| SP0005 – SP0015 | Champ manquant ou invalide dans la requête payout |
| SP0016 | Solde insuffisant |
| SP0018 – SP0020 | Erreur côté création de facture / envoi USSD |
| SP0025 – SP0027 | Ressource introuvable (facture, MSISDN invalide, payout) |

## 12. Sécurité — points non négociables

- **HTTPS uniquement**, jamais d'appel en HTTP simple vers l'API.
- `SharedKey` / `api_secret` en variables d'environnement, jamais en dur dans le code ni dans
  un fichier committé (même `.env.example` doit rester vide).
- Le callback n'a **pas de signature cryptographique** dans cette API — la seule protection est
  la validation métier : référence connue + montant qui correspond exactement à la commande.
  Ne validez jamais un paiement sur la seule présence d'un `reference` qui matche.
- Ne journalisez jamais le `SharedKey`/`api_secret`/`access_token` dans vos logs applicatifs.
- Un ancien script webshell-like du type `deploy-tasks.php` (exécution d'actions artisan via un
  paramètre GET `?key=...`) ne doit **jamais** rester en ligne après un déploiement — supprimez-le
  du serveur immédiatement après usage.

## 13. Tests (exemple avec `Http::fake`)

```php
Http::fake([
    'lab.billing-easy.net/api/v1/merchant/e_bills' => Http::response([
        'e_bill' => ['bill_id' => '5550051928', 'amount' => 80000, 'state' => 'ready'],
    ], 201),
    'lab.billing-easy.net/api/v1/merchant/e_bills/5550051928/ussd_push' => Http::response([
        'message' => 'Accepted', 'url' => null,
    ], 200),
]);

// ... déclenchez le charge() ...

Http::assertSent(fn ($request) => str_contains($request->url(), 'ussd_push')
    && $request['payment_system_name'] === 'airtelmoney');
```

Et pour le callback, un simple `postJson('/api/webhooks/ebilling/callback', [...])` en testant
les cas : succès, référence inconnue (404), montant qui ne correspond pas (400). Voir
[`tests/Feature/EbillingPaymentTest.php`](../tests/Feature/EbillingPaymentTest.php) pour un
exemple complet.

## 14. Checklist mise en production

**PAYIN — Validation LAB**

- [ ] Compte marchand LAB créé, credentials récupérés
- [ ] Table de suivi des transactions en place
- [ ] Création de facture fonctionnelle (retourne `bill_id`)
- [ ] Redirection portail **ou** push USSD testé avec succès
- [ ] URL de callback enregistrée dans le profil marchand LAB
- [ ] Callback testé isolément via `https://simu.billing-easy.net`
- [ ] Test de bout en bout réussi (simulateur → callback → statut confirmé)
- [ ] Codes HTTP 200/4xx correctement renvoyés par le callback

**PAYOUT — Validation LAB** (si utilisé)

- [ ] Compte SHAP LAB créé, `api_id`/`api_secret` générés
- [ ] Génération du Bearer Token fonctionnelle
- [ ] Solde suffisant sur le compte LAB
- [ ] Payout créé et confirmé
- [ ] Callback PAYOUT configuré et testé (opérateurs INTOUCH)

**Passage en PROD**

1. Remplacer les URLs LAB par les URLs PROD.
2. Remplacer les credentials LAB par les credentials PROD (fournis par Digitech Africa après
   validation du LAB).
3. Enregistrer l'URL de callback PROD dans le profil marchand PROD.
4. Effectuer un test de bout en bout avec un paiement réel de montant minimal.

## 15. Support & Contacts

- Support technique : `support@digitech-africa.com`
- Support général : `contact@digitech-africa.com`
- Documentation LAB : `https://lab.billing-easy.net`
- Simulateur : `https://simu.billing-easy.net`

---

*Ce document est un résumé pratique orienté Laravel, basé sur le guide officiel Digitech
Africa v1.0 (mars 2025). En cas de doute ou de divergence, le PDF fourni par Digitech Africa
lors de l'inscription du compte marchand fait foi.*
