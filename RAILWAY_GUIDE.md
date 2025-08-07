# Guide de Déploiement Railway - Étape par Étape

## 🚀 Déploiement de Planète Petit Pays (Cameroun) sur Railway

### Étape 1 : Préparation du Repository GitHub

1. **Créer un repository GitHub**
   ```bash
   # Si vous n'avez pas encore de repository
   git init
   git add .
   git commit -m "Initial commit - Planète Petit Pays"
   git branch -M main
   git remote add origin https://github.com/votre-username/planete-petit-pays.git
   git push -u origin main
   ```

2. **Vérifier que tous les fichiers sont présents**
   - `railway.json` ✅
   - `nixpacks.toml` ✅
   - `composer.json` ✅
   - `init.php` ✅
   - `health.php` ✅
   - `.gitignore` ✅

### Étape 2 : Configuration Railway

1. **Aller sur Railway.app**
   - Visiter [https://railway.app](https://railway.app)
   - Se connecter avec votre compte GitHub

2. **Créer un nouveau projet**
   - Cliquer sur "New Project"
   - Sélectionner "Deploy from GitHub repo"
   - Choisir votre repository `planete-petit-pays`

3. **Attendre le premier déploiement**
   - Railway va automatiquement détecter que c'est un projet PHP
   - Le build va se lancer automatiquement
   - Attendre que le déploiement soit terminé

### Étape 3 : Configuration de la Base de Données

1. **Ajouter une base MySQL**
   - Dans votre projet Railway
   - Cliquer sur "New"
   - Sélectionner "Database" → "MySQL"
   - Attendre que la base soit créée

2. **Récupérer les informations de connexion**
   - Cliquer sur votre base MySQL
   - Aller dans l'onglet "Connect"
   - Noter :
     - Host
     - Database name
     - Username
     - Password

### Étape 4 : Configuration des Variables d'Environnement

1. **Aller dans l'onglet "Variables"**
   - Dans votre projet Railway
   - Cliquer sur l'onglet "Variables"

2. **Ajouter les variables suivantes**
   ```bash
   DB_HOST=votre-host-mysql
   DB_NAME=votre-nom-base
   DB_USER=votre-utilisateur
   DB_PASS=votre-mot-de-passe
   SITE_URL=https://votre-app.railway.app
   ```

3. **Remplacer les valeurs**
   - Utiliser les informations de votre base MySQL
   - Pour `SITE_URL`, utiliser l'URL fournie par Railway

### Étape 5 : Import du Schéma de Base de Données

#### Option A : Via phpMyAdmin (si disponible)
1. Aller dans votre base MySQL Railway
2. Cliquer sur "Connect" → "phpMyAdmin"
3. Importer le fichier `database/schema.sql`

#### Option B : Via commande SQL
1. Aller dans votre base MySQL Railway
2. Cliquer sur "Connect" → "Query"
3. Copier-coller le contenu de `database/schema.sql`
4. Exécuter les requêtes

### Étape 6 : Vérification du Déploiement

1. **Vérifier l'URL de l'application**
   - Aller dans l'onglet "Deployments"
   - Cliquer sur l'URL fournie par Railway

2. **Tester l'endpoint de santé**
   - Visiter `https://votre-app.railway.app/health.php`
   - Vérifier que la réponse est `{"status":"healthy"}`

3. **Tester l'application**
   - Visiter l'URL principale
   - Vérifier que la page d'accueil s'affiche
   - Tester la connexion admin

### Étape 7 : Configuration du Domaine Personnalisé (Optionnel)

1. **Aller dans l'onglet "Settings"**
2. **Cliquer sur "Custom Domain"**
3. **Ajouter votre domaine**
4. **Configurer les DNS**

## 🔧 Dépannage

### Problème : Erreur de connexion à la base de données
**Solution :**
- Vérifier les variables d'environnement
- S'assurer que la base MySQL est active
- Vérifier les permissions utilisateur

### Problème : Erreur 500
**Solution :**
- Vérifier les logs Railway
- S'assurer que tous les fichiers sont présents
- Vérifier les permissions des dossiers uploads

### Problème : Build échoue
**Solution :**
- Vérifier que `composer.json` est correct
- S'assurer que tous les fichiers de configuration sont présents
- Vérifier les logs de build

### Problème : Uploads ne fonctionnent pas
**Solution :**
- Vérifier que les dossiers uploads existent
- S'assurer que les permissions sont correctes
- Vérifier la taille maximale d'upload

## 📊 Monitoring

### Logs Railway
- Aller dans l'onglet "Deployments"
- Cliquer sur un déploiement
- Voir les logs en temps réel

### Métriques
- Aller dans l'onglet "Metrics"
- Surveiller l'utilisation des ressources

## 🔄 Mises à jour

### Déploiement automatique
- Chaque push sur GitHub déclenche un nouveau déploiement
- Railway détecte automatiquement les changements

### Déploiement manuel
- Aller dans l'onglet "Deployments"
- Cliquer sur "Deploy"

## 📞 Support

### Documentation Railway
- [Railway Documentation](https://docs.railway.app/)
- [Railway Discord](https://discord.gg/railway)

### Support du projet
- Consulter les fichiers de documentation
- Vérifier les logs Railway
- Contacter le support Railway si nécessaire

## 🎉 Félicitations !

Votre application Planète Petit Pays est maintenant déployée sur Railway !

**URL de votre application :** `https://votre-app.railway.app`

**Compte admin par défaut :**
- Email : admin@planete-petit-pays.com
- Mot de passe : password 