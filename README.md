# Application de Transfert d'Argent

Une application Laravel moderne avec interface Vue.js/Inertia pour la gestion des transferts d'argent entre utilisateurs.

## Fonctionnalités

### Pour les Utilisateurs
- 📊 **Dashboard** : Vue d'ensemble des transferts et statistiques personnelles
- 💸 **Transferts** : Envoi et réception d'argent avec support multi-devises
- 👤 **Profil** : Gestion du compte et authentification à deux facteurs (2FA)
- 🔐 **Sécurité** : Authentification sécurisée avec support 2FA

### Pour les Administrateurs
- 👥 **Gestion des utilisateurs** : Liste, recherche, promotion et blocage d'utilisateurs
- 📋 **Détails utilisateur** : Vue complète du profil et historique des échanges
- 💱 **Gestion des échanges** : Vue d'ensemble de tous les transferts de la plateforme
- 🔍 **Recherche avancée** : Filtrage et recherche dans les utilisateurs et échanges

## Technologies Utilisées

- **Backend** : Laravel 12
- **Frontend** : Vue.js 3 + TypeScript + Inertia.js
- **UI** : Shadcn/UI + Tailwind CSS
- **Base de données** : SQLite (configurable)
- **Authentification** : Laravel Fortify avec 2FA

## Installation

### Prérequis

- PHP 8.4+
- Composer
- Node.js 18+
- NPM ou Yarn

### Installation Automatique (Recommandée)

**Option 1 : Avec Makefile (recommandé)**
```bash
git clone https://github.com/juniorconseiltaker-technicaltest/sallak_imrane.git
cd technical-test
make setup  # Installation et configuration complète
make dev    # Lancement du serveur
```

**Option 2 : Installation manuelle**

### Étapes d'installation

1. **Cloner le projet**
   ```bash
   git clone https://github.com/juniorconseiltaker-technicaltest/sallak_imrane.git
   cd technical-test
   ```

2. **Installer les dépendances PHP**
   ```bash
   composer install
   ```

3. **Installer les dépendances JavaScript**
   ```bash
   npm install
   ```

4. **Configuration de l'environnement**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   **Important** : Pour les taux de change en temps réel, vous devez obtenir une clé API gratuite :

   1. Créez un compte sur [Fixer.io](https://fixer.io/)
   2. Récupérez votre clé API gratuite
   3. Ajoutez-la dans le fichier `.env` :
      ```env
      FIXER_API_KEY=votre_cle_api_ici
      ```

   *Note : Sans cette clé, l'application utilisera des taux de change fixes.*

5. **Configuration de la base de données**

   Le projet est configuré pour SQLite par défaut. Pour modifier :
   ```bash
   # Éditer le fichier .env
   DB_CONNECTION=sqlite
   DB_DATABASE=/chemin/vers/database.sqlite
   ```

6. **Créer la base de données et exécuter les migrations**
   ```bash
   touch database/database.sqlite
   php artisan migrate:fresh --seed
   ```

7. **Compiler les assets**
   ```bash
   npm run build
   # ou pour le développement
   npm run dev
   ```

8. **Démarrer le serveur**
   ```bash
   composer run dev
   ```

L'application sera accessible sur `http://localhost:8000`

## Commandes Makefile

Le projet inclut un Makefile pour simplifier les tâches courantes :

```bash
make help     # Affiche toutes les commandes disponibles
make setup    # Installation et configuration complète
make dev      # Lance le serveur de développement
make up       # Alias pour 'make dev'
make fresh    # Réinitialise la DB avec données de test
make clean    # Nettoie tous les caches
make test     # Lance les tests
make build    # Compile les assets pour production
make logs     # Affiche les logs en temps réel
```

## Comptes de Test

Après le seeding, plusieurs comptes de test sont disponibles :

### Administrateurs
- **Super Admin**
  - Email : `admin@example.com`
  - Mot de passe : `password`
  - Solde : 50,000.00 €

- **Admin avec 2FA**
  - Email : `claire.admin.secure@example.com`
  - Mot de passe : `password`
  - Solde : 30,000.00 €

### Utilisateurs Normaux
- **Utilisateur riche**
  - Email : `marie.riche@example.com`
  - Mot de passe : `password`
  - Solde : 10,000+ €

- **Utilisateur standard**
  - Email : `pierre.user@example.com`
  - Mot de passe : `password`
  - Solde : 1,500.50 €

- **Utilisateur avec 2FA**
  - Email : `sophie.secure@example.com`
  - Mot de passe : `password`
  - Solde : 3,000.00 €

- **Utilisateur bloqué**
  - Email : `martin.blocked@example.com`
  - Mot de passe : `password`
  - Statut : Inactif (ne peut pas se connecter)

## Navigation et Utilisation

### Interface Utilisateur

1. **Connexion**
   - Accédez à la page de connexion
   - Utilisez un des comptes de test ci-dessus
   - Support 2FA pour les comptes configurés

2. **Dashboard**
   - Vue d'ensemble des transferts personnels
   - Graphiques des transferts par mois
   - Liste des transferts récents
   - Statistiques du solde

3. **Transferts**
   - Créer un nouveau transfert
   - Sélectionner le destinataire
   - Choisir le montant et la devise
   - Ajouter un message optionnel

4. **Profil**
   - Modifier les informations personnelles
   - Configurer l'authentification 2FA
   - Voir l'historique du compte

### Interface Administrateur

Les administrateurs ont accès à des fonctionnalités supplémentaires :

1. **Gestion des Utilisateurs**
   - Liste paginée de tous les utilisateurs
   - Recherche par nom ou email
   - Statistiques générales (admins, utilisateurs actifs, etc.)

2. **Actions sur les Utilisateurs**
   - Promouvoir un utilisateur en administrateur
   - Bloquer/débloquer un utilisateur
   - Voir les détails complets d'un utilisateur

3. **Détails Utilisateur**
   - Informations complètes du profil
   - Historique des échanges (envoyés et reçus)
   - Statistiques personnalisées
   - Actions administratives

4. **Gestion des Échanges**
   - Vue d'ensemble de tous les transferts
   - Recherche et filtrage avancés
   - Détails complets de chaque échange

### Fonctionnalités de Sécurité

1. **Authentification à Deux Facteurs (2FA)**
   - Configuration via QR code
   - Codes de récupération
   - Validation obligatoire après activation

2. **Système de Blocage**
   - Les utilisateurs bloqués sont automatiquement déconnectés
   - Impossibilité de se reconnecter
   - Protection contre l'auto-blocage pour les administrateurs

3. **Contrôle d'Accès**
   - Middleware de vérification d'activité
   - Séparation des rôles utilisateur/administrateur
   - Protection des routes sensibles

## Structure du Projet

```
technical-test/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php      # Gestion admin
│   │   │   ├── DashboardController.php  # Dashboard utilisateur
│   │   │   └── ProfileController.php    # Gestion profil
│   │   └── Middleware/
│   │       ├── AdminMiddleware.php      # Contrôle accès admin
│   │       └── EnsureUserIsActive.php   # Vérification utilisateur actif
│   └── Models/
│       ├── User.php                     # Modèle utilisateur
│       └── Exchange.php                 # Modèle échange
├── database/
│   ├── factories/
│   │   ├── UserFactory.php              # Factory utilisateurs
│   │   └── ExchangeFactory.php          # Factory échanges
│   ├── seeders/
│   │   ├── UserSeeder.php               # Seeder utilisateurs
│   │   └── ExchangeSeeder.php           # Seeder échanges
│   └── migrations/                      # Migrations DB
└── resources/
    ├── js/
    │   ├── pages/
    │   │   ├── Admin/                   # Pages administrateur
    │   │   ├── Dashboard.vue            # Dashboard utilisateur
    │   │   └── Profile/                 # Pages profil
    │   └── components/                  # Composants Vue
    └── css/
        └── app.css                      # Styles globaux
```

## Données de Test

Le système génère automatiquement :
- **50 utilisateurs** avec différents profils (admin, riche, pauvre, 2FA, bloqué, etc.)
- **200+ échanges** avec montants variés et différentes devises
- **Données réalistes** avec IBAN français et taux de change

### Types d'Utilisateurs Créés
- 3 administrateurs (dont 1 avec 2FA)
- 47 utilisateurs normaux variés
- 7 utilisateurs avec 2FA activé
- 7 utilisateurs non vérifiés
- 1 utilisateur bloqué

### Types d'Échanges Créés
- Échanges entre utilisateurs actifs uniquement
- Montants de 1€ à 10,000€
- 6 devises supportées (EUR, USD, GBP, JPY, CAD, CHF)
- Échanges avec et sans messages
- Historique s'étalant sur 6 mois

## Commandes Utiles

```bash
# Réinitialiser la base de données avec données de test
php artisan migrate:fresh --seed

# Créer un nouvel utilisateur administrateur
php artisan tinker
User::factory()->admin()->create(['email' => 'admin@test.com'])

# Compiler les assets en mode développement
npm run dev

# Compiler les assets pour la production
npm run build

# Lancer les tests
php artisan test

# Vider le cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Support et Développement

### Mode Développement
```bash
composer run dev
```

### Production
```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Licence

Ce projet est développé dans le cadre d'un test technique.
