# laravel-model-builder
Laravel Model Builder, a poor attempt to reverse engineer a MySQL database to Laravel models.

# Usage
Add the following to your composer.json:
```javascript
"require": {
    "jimbolino/laravel-model-builder" : "dev-master"
}, 
```
	
Add to your routes.php:
```php
Route::get('/generate/models', '\\Jimbolino\\Laravel\\ModelBuilder\\ModelGenerator5@start');
```

Run the url, and your models will be created in the storage\models folder 
so you have to manually copy them to your real models folder.
Or better, use a tool like beyond compare to update your current models.

# Known Issues
 - Relation to itself (parent_id, child_id etc) will result in duplicate function names
 - Multiple foreign keys between tables will also not work
 - correct detection of $timestamps value
