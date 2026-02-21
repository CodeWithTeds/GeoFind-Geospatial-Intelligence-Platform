#!/bin/bash

# Exit immediately if a command exits with a non-zero status
set -e

echo "🚀 Starting deployment..."

# 1. Install Composer Dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# 1.5 Install NPM Dependencies and Build Assets
echo "🎨 Building frontend assets..."
# Unset NPM_CONFIG_PRODUCTION to avoid "config production" warning and ensure devDependencies are installed
unset NPM_CONFIG_PRODUCTION
npm ci
npm run build

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
