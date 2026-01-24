#!/bin/bash

# Exit immediately if a command exits with a non-zero status
set -e

echo "🚀 Starting deployment..."

# 1. Install Composer Dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# 2. Run Database Migrations
echo "🗄️  Running migrations..."
php artisan migrate --force

# 3. Seed Database
echo "🌱 Seeding database..."
php artisan db:seed --force

# 4. Clear and Cache Configuration
echo "🧹 Optimizing application..."
php artisan optimize:clear
php artisan optimize

echo "✅ Deployment completed successfully!"
