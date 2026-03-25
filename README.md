# Application-symphonie

Projet Symfony 8 pour la gestion de personnages (races, classes, competences, groupes, etc.).

# Prerequis

- PHP 8.4+
- Composer
- Git
- Bash (Linux/macOS, ou Git Bash sous Windows)

## Initialiser le projet (commandes Bash)

# 1) Cloner le depot
Si vous voulez forcer SQLite en local dans `.env.local`:


echo 'DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_dev.db"' >> .env.local

# 4) Creer la base et appliquer les migrations

php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate -n

# 5) Installer DoctrineFixturesBundle

composer require doctrine/doctrine-fixtures-bundle

# 6) Charger les donnees de demo (fixtures)

php bin/console doctrine:fixtures:load -n

# Option A: serveur PHP integre

php -S 127.0.0.1:8000 -t public

# Option B: Symfony CLI (si installee)

symfony open:local