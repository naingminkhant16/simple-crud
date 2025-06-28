# 🛠️ Laravel Simple CRUD Generator

A developer-friendly package to quickly scaffold CRUD operations in Laravel — including models, migrations, controllers, repositories, routes, and views — using a single Artisan command.
It use my another package, (naingminkhant/myrepo) which handle CRUD operations, error handling, and logging, 
---

## 🚀 Features

- ✅ Generate Eloquent Model and Migration
- ✅ Generate Repository class
- ✅ Generate API or MVC-style Controller
- ✅ Append route to `api.php` or `web.php`
- ✅ Generate empty Blade view files (`index`, `create`, `edit`, `show`)
- ✅ Automatically add `$guarded = []` to your model

---

## 📦 Installation

Require the package via Composer:

```bash
composer require naingminkhant/simple-crud
```

## 🧪 Usage

### For MVC
```bash
php artisan simple-crud {ModelName}
```
It will create model, migration, MVC style controller, repository, append routes in web.php, and generate basic view files.

### For API
```bash
php artisan simple-crud {ModelName} --api
```
It will generate all CRUD endpoints for provided model. 

Developers only need to provide columns for migration file and run
```bash
php artisan migrate
```

You can now try testing by calling endpoints.

