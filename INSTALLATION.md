# Guide d'Installation - Planète Petit Pays

## 🚀 Installation

### Prérequis
- WAMP Server (Apache + MySQL + PHP)
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur

### Étapes d'installation

1. **Cloner/Extraire le projet**
   ```bash
   # Placer le projet dans le dossier www de WAMP
   C:\wamp64\www\planete-petit-pays\
   ```

2. **Créer la base de données**
   - Ouvrir phpMyAdmin (http://localhost/phpmyadmin)
   - Créer une nouvelle base de données : `planete_petit_pays`
   - Importer le fichier `database/schema.sql`

3. **Configurer la base de données**
   - Ouvrir `includes/config.php`
   - Modifier les paramètres de connexion si nécessaire :
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'planete_petit_pays');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     ```

4. **Créer les dossiers d'upload**
   ```bash
   mkdir -p assets/uploads/audio
   mkdir -p assets/uploads/video
   ```

5. **Démarrer WAMP Server**
   - Lancer WAMP Server
   - Vérifier que les services Apache et MySQL sont actifs

6. **Accéder au site**
   - Ouvrir : http://localhost/planete-petit-pays/

## 👤 Comptes de test

### Administrateur
- **Email** : admin@planete-petit-pays.com
- **Mot de passe** : password

### Utilisateur normal
- Créer un compte via la page d'inscription

## 🎵 Ajout de contenu

### Via l'interface admin (à développer)
1. Se connecter avec le compte admin
2. Aller dans l'espace admin
3. Ajouter des albums et fichiers

### Via la base de données
```sql
-- Ajouter un album
INSERT INTO albums (titre, description, date_sortie) VALUES 
('Nouvel Album', 'Description de l\'album', '2024-01-01');

-- Ajouter un fichier
INSERT INTO fichiers (album_id, titre, type, url, prix, duree) VALUES 
(1, 'Nouvelle Chanson', 'audio', 'uploads/audio/chanson.mp3', 5.00, '03:45');
```

## 🔧 Configuration

### Paiements
- Actuellement en simulation
- Pour intégrer une vraie API Mobile Money :
  1. Modifier `includes/functions.php`
  2. Remplacer `simulateMobileMoneyPayment()` par l'API réelle
  3. Configurer les clés API dans `includes/config.php`

### Sécurité
- Les mots de passe sont hachés avec `password_hash()`
- Protection CSRF intégrée
- Tokens de téléchargement temporaires
- Validation des entrées utilisateur

## 📁 Structure des fichiers

```
planete-petit-pays/
├── assets/
│   ├── uploads/
│   │   ├── audio/          # Fichiers audio
│   │   └── video/          # Fichiers vidéo
├── includes/
│   ├── config.php          # Configuration
│   ├── functions.php       # Fonctions utilitaires
│   ├── header.php          # Header commun
│   └── footer.php          # Footer commun
├── ajax/
│   ├── create_download_token.php
│   └── get_album_files.php
├── database/
│   └── schema.sql          # Schéma de la base de données
├── admin/                  # Espace administrateur (à développer)
├── index.php               # Page d'accueil
├── albums.php              # Liste des albums
├── album.php               # Détail d'un album
├── login.php               # Connexion
├── register.php            # Inscription
├── profile.php             # Profil utilisateur
├── payment.php             # Paiement
├── download.php            # Téléchargement sécurisé
└── logout.php              # Déconnexion
```

## 🎨 Personnalisation

### Couleurs
Modifier dans `includes/header.php` :
```javascript
colors: {
    'primary-red': '#DC2626',
    'primary-orange': '#EA580C',
    'primary-yellow': '#EAB308',
    'primary-blue': '#2563EB',
}
```

### Tarification
Modifier dans `includes/config.php` :
```php
define('PRIX_UNITAIRE', 5.00);      // 5 F CFA
define('PRIX_ABONNEMENT', 500.00);  // 500 F CFA
```

## 🛠️ Développement

### Ajouter une nouvelle fonctionnalité
1. Créer le fichier PHP
2. Inclure `includes/header.php` et `includes/footer.php`
3. Utiliser les fonctions de `includes/functions.php`
4. Ajouter la navigation dans `includes/header.php`

### Base de données
- Utiliser PDO pour les requêtes
- Toujours utiliser des requêtes préparées
- Valider et nettoyer les entrées utilisateur

## 🔒 Sécurité

### Bonnes pratiques
- ✅ Mots de passe hachés
- ✅ Protection contre les injections SQL
- ✅ Validation des entrées
- ✅ Tokens de téléchargement temporaires
- ✅ Sessions sécurisées

### À améliorer
- [ ] Protection CSRF complète
- [ ] Rate limiting
- [ ] Logs de sécurité
- [ ] Chiffrement des fichiers

## 📞 Support

Pour toute question ou problème :
- Email : support@planete-petit-pays.com
- Documentation : Voir le code source et les commentaires

## 🚀 Déploiement

### Production
1. Configurer un serveur web (Apache/Nginx)
2. Installer PHP et MySQL
3. Configurer SSL/HTTPS
4. Intégrer une vraie API de paiement
5. Configurer les sauvegardes
6. Mettre en place la surveillance

### Optimisations
- Activer le cache PHP
- Optimiser les images
- Compresser les fichiers CSS/JS
- Utiliser un CDN pour les assets statiques 