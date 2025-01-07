#!/bin/bash

# Démarrer les conteneurs
docker compose up -d

# Attendre que MySQL soit prêt
echo "Attente du démarrage de MySQL..."
sleep 10

# Installation des dépendances PHP
docker compose exec php composer install

# Installation des dépendances Node.js
npm install

# Construction des assets
npm run build

# Création de la base de données
docker compose exec php php bin/console doctrine:database:create --if-not-exists

# Exécution des migrations
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

# Chargement des fixtures
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction

echo "Installation du projet terminée!"