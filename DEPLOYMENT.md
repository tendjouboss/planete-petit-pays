# Guide de Déploiement - Railway

## 🚀 Déploiement sur Railway

### Prérequis
- Compte GitHub
- Compte Railway
- Base de données MySQL (Railway ou externe)

### Étapes de déploiement

#### 1. Préparation du projet

1. **Pousser le code sur GitHub**
   ```bash
   git add .
   git commit -m "Préparation pour déploiement Railway"
   git push origin main
   ```

2. **Variables d'environnement à configurer dans Railway :**
   ```
   DB_HOST=votre-host-mysql
   DB_NAME=votre-nom-base
   DB_USER=votre-utilisateur
   DB_PASS=votre-mot-de-passe
   SITE_URL=https://votre-app.railway.app
   ```

#### 2. Configuration Railway

1. **Connecter GitHub à Railway**
   - Aller sur [Railway.app](https://railway.app)
   - Se connecter avec GitHub
   - Cliquer sur "New Project"
   - Sélectionner "Deploy from GitHub repo"
   - Choisir votre repository

2. **Ajouter une base de données MySQL**
   - Dans votre projet Railway
   - Cliquer sur "New"
   - Sélectionner "Database" → "MySQL"
   - Noter les informations de connexion

3. **Configurer les variables d'environnement**
   - Aller dans l'onglet "Variables"
   - Ajouter les variables listées ci-dessus
   - Utiliser les informations de votre base MySQL

#### 3. Déploiement

1. **Railway détecte automatiquement le projet PHP**
2. **Le build se lance automatiquement**
3. **L'application sera disponible sur l'URL fournie par Railway**

### Configuration de la base de données

#### Option 1 : Base Railway (Recommandée)
1. Créer une base MySQL dans Railway
2. Utiliser les variables d'environnement fournies
3. Importer le schéma via phpMyAdmin ou commande

#### Option 2 : Base externe
1. Utiliser une base MySQL externe (PlanetScale, etc.)
2. Configurer les variables d'environnement correspondantes

### Import du schéma de base de données

1. **Via phpMyAdmin (si disponible)**
   - Accéder à phpMyAdmin
   - Importer le fichier `database/schema.sql`

2. **Via commande SQL**
   ```sql
   -- Créer la base de données
   CREATE DATABASE planete_petit_pays;
   USE planete_petit_pays;
   
   -- Importer le contenu de database/schema.sql
   ```

### Vérification du déploiement

1. **Vérifier les logs Railway**
   - Aller dans l'onglet "Deployments"
   - Vérifier que le build s'est bien passé

2. **Tester l'application**
   - Accéder à l'URL fournie par Railway
   - Vérifier que la page d'accueil s'affiche
   - Tester la connexion à la base de données

### Comptes par défaut

- **Admin :** admin@planete-petit-pays.com / password
- **Utilisateur :** Créer via l'interface d'inscription

### Dépannage

#### Erreur de connexion à la base de données
- Vérifier les variables d'environnement
- S'assurer que la base MySQL est active
- Vérifier les permissions utilisateur

#### Erreur 500
- Vérifier les logs Railway
- S'assurer que tous les fichiers sont présents
- Vérifier les permissions des dossiers uploads

#### Problème d'upload
- Vérifier que les dossiers uploads existent
- S'assurer que les permissions sont correctes
- Vérifier la taille maximale d'upload

### Optimisations

1. **Cache**
   - Activer le cache PHP si nécessaire
   - Optimiser les requêtes SQL

2. **Sécurité**
   - Configurer HTTPS (automatique avec Railway)
   - Vérifier les permissions des fichiers

3. **Performance**
   - Optimiser les images
   - Compresser les fichiers CSS/JS

### Support

Pour toute question :
- Consulter les logs Railway
- Vérifier la documentation Railway
- Contacter le support Railway si nécessaire 