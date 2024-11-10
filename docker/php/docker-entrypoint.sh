set -e

# First arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
    set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
    # Install dependencies
    composer install --prefer-dist --no-progress --no-suggest --no-interaction

    # Wait for database to be ready
    until nc -z db 5432; do
        echo "Waiting for database to be ready..."
        sleep 1
    done

    # Run migrations
    php bin/console doctrine:migrations:migrate --no-interaction
fi

exec docker-php-entrypoint "$@"