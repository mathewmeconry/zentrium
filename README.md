# Zentrium

A versatile management tool for large operations.

## Installation

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Setup schema
bin/console doctrine:migrations:migrate

# Dump client-side translations
bin/console bazinga:js-translation:dump

# Install assets
bin/console assets:install --relative

# Pack assets
node_modules/.bin/encore production
```

## System Requirements

 * PHP ≥ 7.0
 * php-intl
 * NPM
 * MySQL

## License

Proprietary
