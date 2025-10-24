# Volt/Livewire Component Discovery Troubleshooting Guide

## Problem Statement

When accessing the Pizza Voting app at `http://pizza-votes.test`, you may encounter:
```
ComponentNotFoundException: Unable to find component: [pages.index]
```

This guide explains why this happens and how to fix it.

## Understanding Volt Component Discovery

### What is Volt?

Volt is Livewire 3's single-file component system that allows you to write reactive components in a single `.php` file with inline PHP classes and Blade templates.

### Component File Structure

```
resources/views/livewire/
├── pages/
│   └── index.php                    # Component: pages.index
├── dashboard.php                    # Component: dashboard
└── restaurants/
    ├── list.php                     # Component: restaurants.list
    ├── show.php                     # Component: restaurants.show
    ├── create.php                   # Component: restaurants.create
    ├── edit.php                     # Component: restaurants.edit
    └── vote.php                     # Component: restaurants.vote
```

### Component Naming Convention

Volt converts file paths to component names using dots:
- `pages/index.php` → `pages.index`
- `restaurants/list.php` → `restaurants.list`
- `dashboard.php` → `dashboard`

## Common Issues and Solutions

### 1. **Configuration Not Found**

**Symptom:** Component class not registered

**Cause:** Missing or incomplete `config/livewire.php`

**Solution:**
```bash
# Publish Livewire's configuration
php artisan livewire:publish

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### 2. **Component Paths Not Mounted**

**Symptom:** Volt can't find components in `resources/views/livewire`

**Cause:** `VoltServiceProvider` not mounting the correct path

**Solution:** Ensure your `app/Providers/VoltServiceProvider.php` contains:
```php
public function boot(): void
{
    Volt::mount(resource_path('views/livewire'));
}
```

### 3. **Cache Preventing Component Discovery**

**Symptom:** Components work in tests but not in browser

**Cause:** Laravel caching old component registry

**Solution:**
```bash
# Clear all possible caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

### 4. **Missing Livewire Assets**

**Symptom:** Components don't render with interactivity

**Cause:** Livewire JavaScript assets not published

**Solution:**
```bash
php artisan livewire:publish
```

This publishes:
- Livewire configuration
- Pagination views
- JavaScript assets

## Step-by-Step Troubleshooting

Follow these steps if you're still seeing component not found errors:

### Step 1: Verify Files Exist
```bash
# All these files should exist
ls resources/views/livewire/pages/index.php
ls resources/views/livewire/dashboard.php
ls resources/views/livewire/restaurants/list.php
```

### Step 2: Check Provider is Registered
```bash
# Verify in bootstrap/providers.php:
cat bootstrap/providers.php | grep VoltServiceProvider
# Should output: App\Providers\VoltServiceProvider::class,
```

### Step 3: Verify Service Provider Mounts Components
```bash
# Check app/Providers/VoltServiceProvider.php
cat app/Providers/VoltServiceProvider.php
# Should contain: Volt::mount(resource_path('views/livewire'));
```

### Step 4: Clear Everything
```bash
# Comprehensive cache clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# Optional: clear composer autoloader
composer dump-autoload
```

### Step 5: Publish Livewire Assets
```bash
php artisan livewire:publish
```

### Step 6: Test in Browser
```bash
# Start fresh development server
php artisan serve

# Visit http://localhost:8000
```

### Step 7: Run Tests (Verification)
```bash
# If tests pass, components should work
php artisan test tests/Feature/SmokeTest.php --env=testing
```

## Environment-Specific Issues

### Development Server vs. Production

**Development (php artisan serve):**
- Uses PHP's built-in server
- Reloads components on each request
- Requires Volt to re-discover components

**Production:**
- Pre-compiled components
- Requires: `php artisan event:cache`
- May need: `php artisan config:cache`

### Vite/Build Tool Issues

If using Vite for assets:
```bash
# Make sure Livewire scripts are included in main layout
npm run dev
```

## Checking Component Registration at Runtime

### Test Environment (What Works)
```bash
php artisan test --env=testing
```

Outputs:
```
Tests: 21 passed (30 assertions)
```

If tests pass, your components are registering correctly in the test environment.

### Browser Environment (What May Fail)
- Different PHP configuration
- Different autoloader behavior
- Different cache state
- Different environment variables

## Key Files to Check

| File | Purpose | Status |
|------|---------|--------|
| `bootstrap/providers.php` | Registers VoltServiceProvider | ✓ Must exist |
| `app/Providers/VoltServiceProvider.php` | Mounts Volt components | ✓ Must call Volt::mount() |
| `config/livewire.php` | Livewire configuration | ✓ Created by livewire:publish |
| `resources/views/livewire/**/*.php` | Volt components | ✓ Must exist |
| `config/app.php` | Laravel config | ✓ Standard |

## Debug Mode

To debug component discovery:

```php
// In your route or controller
use Livewire\Volt\Volt;

dd(Volt::find('pages.index'));  // Should return component class
```

## Full Reset Procedure

If nothing else works, do a complete reset:

```bash
# 1. Remove all caches
rm -rf bootstrap/cache/*
rm -rf storage/framework/cache/*
rm -rf storage/framework/views/*

# 2. Clear all compiled cache
php artisan optimize:clear

# 3. Republish Livewire
php artisan livewire:publish --force

# 4. Clear caches again
php artisan cache:clear
php artisan config:clear

# 5. Verify
php artisan test tests/Feature/SmokeTest.php --env=testing
```

## Verification Checklist

- [ ] `resources/views/livewire/pages/index.php` exists and is readable
- [ ] `app/Providers/VoltServiceProvider.php` calls `Volt::mount()`
- [ ] `config/livewire.php` exists with correct `view_path`
- [ ] All cache directories are writable
- [ ] `bootstrap/providers.php` includes `VoltServiceProvider`
- [ ] All caches have been cleared
- [ ] `php artisan livewire:publish` has been run
- [ ] Tests pass: `php artisan test tests/Feature/SmokeTest.php`

## Still Not Working?

If you've followed all steps and components still don't load:

1. **Check Laravel logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Enable debug mode:**
   ```
   APP_DEBUG=true
   ```

3. **Check PHP version:**
   ```bash
   php -v  # Should be 8.2+
   ```

4. **Check Livewire version:**
   ```bash
   composer show | grep livewire
   ```

5. **Verify database connection:**
   ```bash
   php artisan migrate
   ```

## Success Indicators

When component discovery is working:
- ✅ `php artisan test` runs without component errors
- ✅ Browser loads routes without 500 errors
- ✅ Livewire interactivity works (button clicks, form submissions)
- ✅ All 21 smoke tests pass
- ✅ Routes resolve to correct components

## Related Documentation

- [Volt Documentation](https://livewire.laravel.com/docs/volt)
- [Livewire v3 Docs](https://livewire.laravel.com/docs/quickstart)
- [Laravel Service Providers](https://laravel.com/docs/providers)
