# Railway Deployment Guide - XobiyaHR Leave Management System

## Overview
This guide covers deploying your Laravel 12 application with Blade templates to Railway.app

**Tech Stack:**
- PHP 8.2+
- Laravel 12
- MySQL 8.0
- Node.js 18+ (for Vite/Tailwind asset compilation)
- Blade templates with React components

---

## Prerequisites

### Local Development
- PHP 8.2+
- Composer
- Node.js 18+
- npm/yarn
- Git

### Railway Setup
1. GitHub account (linked to your repo)
2. Railway account (https://railway.app)
3. Credit card for Railway (free tier available with $5 credit)

---

## Step 1: Prepare Your Repository

### 1.1 Ensure All Files Are Committed
```bash
git status
git add .
git commit -m "Prepare for Railway deployment"
git push origin main
```

### 1.2 Verify Required Files Exist
```bash
✓ composer.json       - PHP dependencies
✓ package.json        - Node.js dependencies
✓ Procfile            - Process types
✓ railway.toml        - Railway configuration
✓ .env.example        - Environment template
✓ database/migrations - Schema
```

### 1.3 Run Health Check (Optional)
```bash
chmod +x scripts/railway-health-check.sh
bash scripts/railway-health-check.sh
```

---

## Step 2: Create Railway Project

### 2.1 Visit Railway Dashboard
1. Go to https://railway.app
2. Click "New Project"
3. Select "Deploy from GitHub repo"
4. Authorize Railway to access your GitHub account

### 2.2 Select Repository
- Search for `Leave-management-system`
- Select `feleabo02-dotcom/Leave-management-system`
- Click "Deploy"

Railway will auto-detect the framework and start building!

---

## Step 3: Add MySQL Database Service

### 3.1 In Railway Dashboard
1. Click the "+" button in your project
2. Select "Add Service" → "Database"
3. Choose "MySQL"
4. Select version `8.0` (or latest)
5. Click "Create"

### 3.2 Verify Database Connection
Railway automatically injects `DATABASE_URL` environment variable. Your app will use it automatically.

---

## Step 4: Configure Environment Variables

### 4.1 Required Variables
Add these in Railway Dashboard → "Variables" section:

```env
# App Configuration
APP_NAME=XobiyaHR
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-railway-domain.railway.app
APP_LOCALE=en
APP_FALLBACK_LOCALE=en

# Generate a secure key locally:
# php artisan key:generate --show
APP_KEY=base64:YOUR_GENERATED_KEY_HERE

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=warning

# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

# Queue Configuration
QUEUE_CONNECTION=database

# Cache Configuration
CACHE_STORE=database

# Mail Configuration (use log for testing)
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"

# Asset Configuration
VITE_APP_NAME="${APP_NAME}"
```

### 4.2 Generate APP_KEY

If you don't have one:
```bash
php artisan key:generate --show
```

Copy the output (without `base64:` prefix) to `APP_KEY` in Railway.

### 4.3 Database Variables
Railway automatically sets:
- `DATABASE_URL`
- `DB_HOST`
- `DB_PORT`
- `DB_USERNAME`
- `DB_PASSWORD`
- `DB_DATABASE`

✅ These are auto-injected—don't set them manually!

---

## Step 5: Deployment Process

### 5.1 Automatic Deployment
Every `git push` to `main` triggers:

1. **Build Phase** (5-10 min):
   - PHP 8.2 environment prepared
   - Composer installs dependencies
   - npm installs & builds assets
   
2. **Deploy Phase** (2-3 min):
   - Database migrations run
   - Config cache generated
   - Routes cache generated
   - App starts serving

3. **Running**:
   - Web process: Apache serving on port 8000
   - Queue worker: Running in background

### 5.2 Manual Deployment
1. Push changes: `git push origin main`
2. Railway automatically redeploys
3. Monitor logs in dashboard

### 5.3 Deployment Logs
View in Railway Dashboard:
- Click your app service
- Go to "Logs" tab
- Watch build and runtime output

---

## Step 6: Database Migrations

### 6.1 First Deployment
Migrations run automatically via `railway.toml`:
```toml
startCommand = "php artisan migrate --force && ..."
```

### 6.2 Manual Migration (if needed)
1. In Railway Dashboard, click "Terminal"
2. Run migrations:
   ```bash
   php artisan migrate
   ```

### 6.3 Seed Database (optional)
```bash
# In Railway Terminal
php artisan db:seed
```

---

## Step 7: Verify Deployment

### 7.1 Check Application Status
1. Railway Dashboard shows your custom domain
2. Click the domain or "View Logs"
3. You should see:
   ```
   ✓ Migration batch completed
   ✓ Server started on port 8000
   ```

### 7.2 Test Your App
```bash
curl https://your-app.railway.app
```

### 7.3 Check Features
- ✓ Login page loads
- ✓ Database connects (check user login)
- ✓ Asset files load (CSS/JS)
- ✓ File uploads work

---

## Environment Variables Reference

| Variable | Example | Purpose |
|----------|---------|---------|
| `APP_NAME` | XobiyaHR | Application name |
| `APP_KEY` | base64:abc... | Encryption key |
| `APP_URL` | https://app.railway.app | Production URL |
| `DB_CONNECTION` | mysql | Database driver |
| `QUEUE_CONNECTION` | database | Queue driver |
| `CACHE_STORE` | database | Cache backend |
| `SESSION_DRIVER` | database | Session storage |

---

## Common Issues & Solutions

### Issue: "Class not found" Error
**Solution:**
```bash
# In Railway Terminal
php artisan config:clear
php artisan cache:clear
php artisan queue:restart
```

### Issue: Assets Not Loading (CSS/JS)
**Solution:**
1. Verify Vite build completed in logs
2. Check `APP_URL` matches domain
3. Clear browser cache (Ctrl+Shift+Delete)

### Issue: Database Connection Fails
**Solution:**
- Verify MySQL service is running (green dot in Railway)
- Check `DATABASE_URL` auto-injected
- View MySQL logs in Railway Dashboard

### Issue: File Uploads Not Working
**Solution:**
- Configure S3/cloud storage (if needed)
- For local: ensure `storage/app/public` writable
- Or add Railway filesystem plugin

### Issue: CORS Errors
**Solution:**
Update `config/cors.php`:
```php
'allowed_origins' => ['https://your-railway-domain.railway.app'],
```

### Issue: Long Deployment Time
**Solution:**
- First deployment takes longer (full build)
- Subsequent deploys are faster (incremental)
- Check "Optimizer" in Railway settings

---

## Performance Optimization

### 1. Enable Caching
```env
CACHE_STORE=database  # or redis if available
```

### 2. Optimize Assets
Already configured in `railway.toml`:
- Config cache: `php artisan config:cache`
- Route cache: `php artisan route:cache`
- Class map optimization: Enabled

### 3. Database Indexing
Ensure critical tables have indexes:
```bash
php artisan tinker
DB::statement('ALTER TABLE users ADD INDEX idx_email (email)');
```

### 4. Queue Processing
Monitor queue in Railway Terminal:
```bash
php artisan queue:failed
php artisan queue:work --tries=3
```

---

## Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] `APP_ENV=production`
- [ ] Unique `APP_KEY` generated
- [ ] `SESSION_SECURE_COOKIE=true`
- [ ] `SESSION_SAME_SITE=lax` or `strict`
- [ ] HTTPS enforced (Railway auto)
- [ ] Environment variables not in code
- [ ] `.env` not committed to git
- [ ] Database backups configured
- [ ] Rate limiting enabled
- [ ] CSRF protection active (Laravel default)
- [ ] SQL injection prevention (Eloquent ORM default)

---

## Monitoring & Maintenance

### View Live Logs
Railway Dashboard → Your App → "Logs" tab

### Restart Application
```bash
# In Railway Terminal
php artisan restart
```

### Clear All Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan optimize:clear
```

### Monitor Database Size
1. Railway Dashboard → MySQL service
2. Check "Storage" tab

### View Queue Status
```bash
php artisan queue:failed
php artisan queue:work
```

---

## Updating Application

### Deploy New Version
1. Make changes locally
2. Test: `npm run build && composer install`
3. Commit: `git add . && git commit -m "description"`
4. Push: `git push origin main`
5. Railway auto-deploys within 1-2 minutes

### Database Schema Changes
1. Create migration: `php artisan make:migration create_table`
2. Update migration file
3. Commit & push
4. Migrations run automatically on deploy

---

## Custom Domain Setup

### Use Custom Domain
1. Railway Dashboard → Your App → "Domain" tab
2. Click "Add Domain"
3. Add your domain (e.g., `app.yourcompany.com`)
4. Update DNS records per Railway instructions
5. SSL certificate auto-issued by Railway

---

## Backup & Recovery

### Database Backup
Railway offers automated backups:
1. Dashboard → MySQL service → "Backups" tab
2. Configure backup frequency
3. Download backups as needed

### Manual Backup
```bash
php artisan db:backup  # if using package
# Or MySQL command:
mysqldump -u user -p database > backup.sql
```

---

## Support & Resources

- **Railway Docs**: https://docs.railway.app
- **Laravel Docs**: https://laravel.com/docs
- **Railway Dashboard**: https://railway.app/dashboard
- **GitHub Issues**: Report bugs in repository

### Contact Railway Support
- Visit https://railway.app/support
- Check status: https://status.railway.app

---

## Troubleshooting Checklist

Before reaching out for support:

- [ ] Check Railway logs for errors
- [ ] Verify all environment variables set
- [ ] Run health check: `bash scripts/railway-health-check.sh`
- [ ] Test locally: `npm run build && php artisan serve`
- [ ] Check git commit with `git log`
- [ ] Verify database is running
- [ ] Clear all caches
- [ ] Check disk space in Railway

---

## Next Steps

1. ✅ Complete "Step 2: Create Railway Project"
2. ✅ Complete "Step 3: Add MySQL Database"
3. ✅ Complete "Step 4: Configure Environment Variables"
4. ✅ Monitor deployment logs
5. ✅ Test your application
6. ✅ Set up custom domain (optional)

**Estimated total time:** 15-20 minutes ⏱️

---

**Deployment Status:**
- 🟢 Ready to Deploy
- 📝 Configuration: `/railway.toml`, `/Procfile`
- 🗄️ Database: MySQL 8.0
- 🎨 Assets: Vite + Tailwind + React
- 📧 Email: Log driver (change in production)
- 🔒 Security: HTTPS auto-enabled

Good luck with your deployment! 🚀
