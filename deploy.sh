#!/bin/bash
set -e

echo "================================================="
echo " Deploying Institute SaaS to Hostinger Production"
echo "================================================="

# 1. Install production composer dependencies
echo "Installing Composer dependencies..."
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

# 2. Run Database Migrations and Seeders
echo "Running Database Migrations..."
php artisan migrate --force --seed

# 3. Create Storage Symlink if not already present
echo "Linking storage..."
php artisan storage:link --quiet || true

# 4. Cache Config, Routes, Views, and Filament Components
echo "Optimizing caches for production speed..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:cache-components

# 5. Fix permissions for storage and bootstrap/cache
echo "Setting directory permissions..."
chmod -R 775 storage bootstrap/cache || true

echo "================================================="
echo " Deployment Complete! Application is Live."
echo "================================================="
