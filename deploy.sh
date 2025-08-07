#!/bin/bash

# Script de déploiement pour Railway
# Usage: ./deploy.sh

echo "🚀 Déploiement de Planète Petit Pays sur Railway"
echo "================================================"

# Vérifier que Git est configuré
if ! command -v git &> /dev/null; then
    echo "❌ Git n'est pas installé"
    exit 1
fi

# Vérifier le statut Git
echo "📋 Vérification du statut Git..."
if [[ -n $(git status --porcelain) ]]; then
    echo "⚠️  Il y a des modifications non commitées"
    read -p "Voulez-vous les committer ? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        git add .
        git commit -m "Déploiement automatique - $(date)"
    fi
fi

# Pousser vers GitHub
echo "📤 Push vers GitHub..."
git push origin main

echo "✅ Déploiement initié !"
echo ""
echo "📋 Prochaines étapes :"
echo "1. Aller sur Railway.app"
echo "2. Connecter votre repository GitHub"
echo "3. Configurer les variables d'environnement :"
echo "   - DB_HOST"
echo "   - DB_NAME" 
echo "   - DB_USER"
echo "   - DB_PASS"
echo "   - SITE_URL"
echo "4. Ajouter une base de données MySQL"
echo "5. Importer le schéma database/schema.sql"
echo ""
echo "📖 Consultez DEPLOYMENT.md pour plus de détails" 