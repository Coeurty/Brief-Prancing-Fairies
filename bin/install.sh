#!/bin/bash

# Installation des dépendances
docker compose exec php composer install

# Création de la base de données
docker compose exec php php bin/console doctrine:database:create --if-not-exists

# Exécution des migrations
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

# Chargement des fixtures
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction

echo "Installation du projet terminée!"