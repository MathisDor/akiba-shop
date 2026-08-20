# Déploiement d'AkibaShop sur CloudStore Africa (hébergement cPanel mutualisé)

Ce guide part de ce qui a déjà été fait pour InfinityFree (`deploy-infinityfree/`) et l'adapte
à un hébergement **payant** avec cPanel. La grosse différence : CloudStore Africa devrait
vous laisser changer le **document root** vers `public/` et activer l'accès **SSH**, deux choses
qu'InfinityFree interdisait sur son offre gratuite. Vérifiez ces deux points avec leur support
si ce n'est pas visible dans cPanel — ça change beaucoup la façon de déployer.

## 0. Avant de commander / juste après

- Vérifiez dans cPanel (**MultiPHP Manager**) que PHP **8.3** est disponible (requis par `composer.json`).
- Demandez au support CloudStore Africa d'activer l'**accès SSH** sur votre compte si l'icône
  "SSH Access" n'apparaît pas dans cPanel. C'est souvent activé sur demande même en mutualisé payant.
- Notez le nom d'hôte SSH/cPanel, et si un port SSH non standard est utilisé.

## 1. Configuration cPanel de base

1. **MultiPHP Manager** → sélectionner PHP 8.3 pour le domaine, puis dans **MultiPHP INI Editor**
   vérifiez que ces extensions sont actives : `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`,
   `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `curl`, `gd`.
2. **MySQL Databases** → créer la base + un utilisateur dédié, lui donner tous les privilèges.
   Notez `DB_HOST` (en mutualisé cPanel c'est presque toujours `localhost`), `DB_DATABASE`,
   `DB_USERNAME`, `DB_PASSWORD`.
3. **Domains** → repérer où pointe le document root actuel.

## 2. Document root : la vraie amélioration vs InfinityFree

Sur InfinityFree, `public/` ne pouvait pas être la racine web du domaine principal, d'où le
bricolage dans `.htaccess` (routage `public/$1` + blocage de `app/`, `config/`, etc. à la racine).

Sur CloudStore Africa, essayez plutôt l'approche standard Laravel, plus propre et plus sûre :

- Uploadez tout le projet **en dehors** du webroot, ex. `/home/<user>/akibashop/`.
- Dans cPanel → **Domains**, éditez le domaine et changez le **Document Root** vers
  `/home/<user>/akibashop/public`.
- Résultat : seul `public/` est accessible depuis le web, `app/`, `config/`, `.env`, etc. sont
  physiquement hors de portée du navigateur — plus besoin des règles de blocage dans `.htaccess`.

Si l'offre ne permet pas de changer le document root, gardez le `.htaccess` actuel tel quel
(il continue de fonctionner) et déployez à la racine comme pour InfinityFree.

## 3. Préparer le code en local

```
npm run build
composer install --no-dev --optimize-autoloader
```

Copiez `.env.cloudstore.example` vers `.env` et remplissez avec les vraies valeurs (domaine,
identifiants MySQL cPanel, `APP_KEY`). Générez une clé si besoin :

```
php artisan key:generate --show
```

## 4. Transfert des fichiers

**Si SSH est actif (recommandé) :**

```
ssh <user>@<host> -p <port>
cd ~/akibashop
git clone <votre-repo> .        # ou git pull si déjà cloné
composer install --no-dev --optimize-autoloader
php artisan key:generate        # si pas déjà fait localement
php artisan migrate --force
php artisan storage:link        # symlink() fonctionne normalement sur un vrai serveur Linux
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Sans SSH (File Manager / FTP uniquement) :**

- Zippez le projet en local en excluant `node_modules/`, `.git/`, `tests/`, le contenu de
  `storage/logs/` — mais **incluez `vendor/`** (généré par `composer install` en local) car
  vous ne pourrez pas exécuter Composer sur le serveur.
- Uploadez et décompressez via cPanel File Manager.
- Pour les commandes artisan (migrate, storage:link…), réutilisez le principe de
  `deploy-infinityfree/deploy-tasks.php` : changez `SECRET_KEY`, uploadez-le dans `public/`,
  appelez `?key=...&action=migrate`, **puis supprimez-le du serveur**. Sur CloudStore, le vrai
  `storage:link` (symlink) devrait fonctionner — donc l'action `storage-link` par copie n'est
  normalement plus nécessaire, mais laissez-la en secours si `symlink()` est aussi désactivé ici.

## 5. Permissions

```
chmod -R 775 storage bootstrap/cache
```

(normalement déjà correct si c'est votre utilisateur cPanel qui a uploadé les fichiers)

## 6. Cron (tâches planifiées)

cPanel → **Cron Jobs**, ajoutez :

```
* * * * * cd /home/<user>/akibashop && php artisan schedule:run >> /dev/null 2>&1
```

Aucune tâche planifiée n'est encore définie dans `routes/console.php`, mais c'est la ligne
standard à avoir en place dès maintenant pour le jour où vous en ajouterez (rappels, purge, etc.).
Comme `QUEUE_CONNECTION=database`, si vous dispatchez des jobs plus tard, ajoutez aussi une ligne
cron pour les traiter puisque le mutualisé ne permet pas de worker permanent (Supervisor) :

```
* * * * * cd /home/<user>/akibashop && php artisan queue:work --stop-when-empty >> /dev/null 2>&1
```

## 7. SSL

cPanel → **SSL/TLS Status** → activez AutoSSL (Let's Encrypt gratuit) pour le domaine et le
sous-domaine `www`. Activez ensuite la redirection forcée HTTPS (option cPanel "Force HTTPS
Redirect", ou une règle dans `.htaccess` si l'option n'existe pas).

## 8. Nettoyage avant mise en ligne définitive

- Supprimez `public/test.php` (fichier de test présent dans le repo actuel).
- Supprimez tout script `deploy-tasks.php`/équivalent du serveur une fois les migrations faites.
- Vérifiez `APP_ENV=production` et `APP_DEBUG=false` dans le `.env` réel.
- `.env.infinityfree` contient un mot de passe MySQL en clair. Ne l'uploadez jamais tel quel sur
  CloudStore et évitez de le partager — servez-vous en seulement de modèle de structure.

## 9. Vérification finale

- `https://votre-domaine/up` doit répondre 200 (route de health check définie dans
  `bootstrap/app.php`).
- En cas d'erreur, consultez `storage/logs/laravel.log` (via SSH `tail -f` ou via l'action
  `tail-log`/`last-error` du script de secours si pas de SSH).
