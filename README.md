# Plateforme Musicale - Planète Petit Pays

## 🎵 Description
Plateforme web pour un artiste musical camerounais permettant la vente et le téléchargement de musiques et vidéos.

## 🚀 Fonctionnalités
- Création de compte utilisateur et connexion
- Consultation et téléchargement d'albums/musiques/vidéos
- Système de paiement (unitaire ou abonnement)
- Espace administrateur
- Historique des téléchargements

## 🛠️ Technologies
- **Frontend**: HTML, Tailwind CSS, JavaScript
- **Backend**: PHP (vanilla)
- **Base de données**: MySQL
- **Serveur local**: WAMP Server
- **Paiement**: API Mobile Money (simulation)

## 🚀 Déploiement Rapide

### Option 1 : Déploiement Railway (Recommandé)

1. **Forker ce repository sur GitHub**
2. **Aller sur [Railway.app](https://railway.app)**
3. **Connecter votre compte GitHub**
4. **Créer un nouveau projet**
5. **Sélectionner "Deploy from GitHub repo"**
6. **Choisir votre repository**
7. **Configurer les variables d'environnement** (voir `ENVIRONMENT.md`)
8. **Ajouter une base de données MySQL**
9. **Importer le schéma** `database/schema.sql`

### Option 2 : Installation locale

Voir `INSTALLATION.md` pour les instructions détaillées.

## 📁 Structure du projet
```
planete-petit-pays/
├── assets/
│   ├── css/
│   ├── js/
│   └── uploads/
├── includes/
├── admin/
├── database/
├── ajax/
├── railway.json          # Configuration Railway
├── nixpacks.toml        # Configuration build
├── composer.json         # Dépendances PHP
├── init.php             # Script d'initialisation
└── index.php
```

## 🎨 Couleurs
- Rouge, Orange, Jaune, Bleu

## 💰 Tarification
- Téléchargement unitaire : 5 F CFA
- Abonnement mensuel : 500 F CFA (illimité)

## 📚 Documentation

- **Installation locale** : `INSTALLATION.md`
- **Déploiement Railway** : `DEPLOYMENT.md`
- **Variables d'environnement** : `ENVIRONMENT.md`

## 🔧 Configuration

### Variables d'environnement requises
```bash
DB_HOST=votre-host-mysql
DB_NAME=votre-nom-base
DB_USER=votre-utilisateur
DB_PASS=votre-mot-de-passe
SITE_URL=https://votre-app.railway.app
```

## 👤 Comptes de test

### Administrateur
- **Email** : admin@planete-petit-pays.com
- **Mot de passe** : password

### Utilisateur normal
- Créer un compte via la page d'inscription

## 🛠️ Développement

### Prérequis
- PHP 7.4+
- MySQL 5.7+
- Composer (pour les dépendances)

### Installation locale
```bash
# Cloner le repository
git clone https://github.com/votre-username/planete-petit-pays.git

# Installer les dépendances
composer install

# Configurer la base de données
# Voir INSTALLATION.md pour les détails
```

## 📞 Support

Pour toute question ou problème :
- Email : support@planete-petit-pays.com
- Documentation : Voir les fichiers .md
- Issues : GitHub Issues 