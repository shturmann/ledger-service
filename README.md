### Local Development Setup
```bash
# Clone the repository
git clone  ledger-service
cd ledger-service

# Copy environment file
cp .env .env.local

# Start Docker services
docker-compose up -d

# Install dependencies
docker-compose exec app composer install

# Run database migrations
docker-compose exec app php bin/console doctrine:migrations:migrate

# Run tests
docker-compose exec app php bin/phpunit
```

### Available Commands
```bash
# Generate migration
docker-compose exec app php bin/console doctrine:migrations:diff

# Create database
docker-compose exec app php bin/console doctrine:database:create

# Run PHP-CS-Fixer
docker-compose exec app vendor/bin/php-cs-fixer fix src

# Run PHPStan
docker-compose exec app vendor/bin/phpstan analyse src tests
```