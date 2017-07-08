# Zentrium

A versatile management tool for large operations.

## Installation

```bash
# Install PHP dependencies
composer install

# Install Node.js root dependencies
npm install

# Install Node.js dependencies of bundles
grunt install

# Setup schema
bin/console doctrine:migrations:migrate

# Dump JS translations
bin/console bazinga:js-translation:dump

# Compile bundle assets
grunt

# Install assets
bin/console assets:install --relative
```

## System Requirements

 * PHP ≥ 7.0
 * php-intl
 * NPM
 * Grunt
 * MySQL

## License

Proprietary
