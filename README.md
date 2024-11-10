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

### Endpoints:
1. Create a ledger:
```bash
curl -X POST http://localhost:8080/api/v1/ledgers \
  -H "Content-Type: application/json" \
  -d '{"currency":"USD"}'
```

2. Create transaction:
```bash
curl -X POST http://localhost:8080/api/v1/ledgers/{identifier}/transactions \
  -H "Content-Type: application/json" \
  -d '{
    "transactionId": "550e8400-e29b-41d4-a716-446655440000",
    "type": "credit",
    "amount": 100.50,
    "currency": "USD"
  }'
```

3. Get Balance:
```bash
curl http://localhost:8080/api/v1/ledgers/{identifier}/balance
```