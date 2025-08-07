# Variables d'Environnement - Planète Petit Pays

## Configuration requise pour Railway

### Variables obligatoires

```bash
# Base de données MySQL
DB_HOST=votre-host-mysql
DB_NAME=votre-nom-base
DB_USER=votre-utilisateur
DB_PASS=votre-mot-de-passe

# URL de l'application
SITE_URL=https://votre-app.railway.app
```

### Variables optionnelles

```bash
# Configuration des paiements (valeurs par défaut)
PRIX_UNITAIRE=5.00
PRIX_ABONNEMENT=500.00

# Configuration de sécurité (valeurs par défaut)
SESSION_LIFETIME=3600
DOWNLOAD_TOKEN_LIFETIME=300
```

## Configuration locale

Pour le développement local, vous pouvez créer un fichier `.env` avec ces variables :

```bash
# Copier ce contenu dans un fichier .env
DB_HOST=localhost
DB_NAME=planete_petit_pays
DB_USER=root
DB_PASS=

SITE_URL=http://localhost/planete-petit-pays
```

## Obtention des variables Railway

### 1. Base de données Railway MySQL

1. Dans votre projet Railway
2. Cliquer sur "New" → "Database" → "MySQL"
3. Noter les informations de connexion :
   - Host
   - Database name
   - Username
   - Password

### 2. URL de l'application

1. Après le déploiement, Railway fournit une URL
2. Utiliser cette URL comme `SITE_URL`

## Vérification

Pour vérifier que vos variables sont correctement configurées :

1. Aller dans l'onglet "Variables" de Railway
2. Vérifier que toutes les variables sont présentes
3. Tester la connexion à la base de données

## Dépannage

### Erreur de connexion à la base de données
- Vérifier `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
- S'assurer que la base MySQL est active
- Vérifier les permissions utilisateur

### Erreur d'URL
- Vérifier que `SITE_URL` pointe vers l'URL Railway
- S'assurer que l'URL commence par `https://`

### Variables manquantes
- Vérifier que toutes les variables obligatoires sont définies
- Redémarrer l'application après modification des variables 