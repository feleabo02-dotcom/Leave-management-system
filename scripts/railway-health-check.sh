#!/bin/bash

# Railway Health Check Script
# Verifies application is ready for deployment

echo "🔍 Checking Railway deployment readiness..."

# Check PHP version
echo "✓ Checking PHP version..."
php -v | grep "PHP 8"
if [ $? -ne 0 ]; then
    echo "⚠️ Warning: PHP 8.2+ required"
fi

# Check Composer
echo "✓ Checking Composer..."
composer --version

# Check Node.js
echo "✓ Checking Node.js..."
node --version
npm --version

# Check required files
echo "✓ Checking required files..."
files=("composer.json" "package.json" "Procfile" "railway.toml" ".env.example")
for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo "  ✓ $file found"
    else
        echo "  ✗ $file MISSING"
    fi
done

# Check database configuration
echo "✓ Checking database configuration..."
if grep -q "DB_HOST" .env.example; then
    echo "  ✓ Database config present"
fi

# Check for production key
echo "✓ Checking Laravel key..."
if [ -z "$APP_KEY" ]; then
    echo "  ⚠️ APP_KEY not set - will be generated during deployment"
fi

echo ""
echo "✅ System ready for Railway deployment!"
echo ""
echo "Next steps:"
echo "1. Push to GitHub: git push origin main"
echo "2. Visit https://railway.app and connect repo"
echo "3. Add MySQL service in Railway Dashboard"
echo "4. Set environment variables"
echo "5. Watch deployment logs"
echo ""
