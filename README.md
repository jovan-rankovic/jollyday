# Jollyday
Employee holiday management app.

## Installation

### Requirements
- Apache web server / server environment (XAMPP, WAMP, etc.)
- Database management tool (PhpMyAdmin, MySQL Workbench, etc.)

### Setup
1. Install composer dependencies
```
composer install
```

2. Install npm dependencies and compile
```
npm install
npm run dev
```

3. Run migrations and seeders
```
php artisan migrate:fresh --seed
```

Once done, you can visit your local domain.
Test credentials:
```
Email: test@test.com
Password: password
```
