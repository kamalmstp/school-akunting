# School Accounting App

Requirements :

1. PHP ^8.1
2. Laravel ^10.0
3. Bootstrap 5.3

Install the dependencies:

```bash
$ composer install
```

Import the SQL File to MySQL, you can find a file inside database directory

Change .env.example to .env and add Database Credential:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fresh_accounting_db # Change to your DB Name
DB_USERNAME=username # Change to your DB Username
DB_PASSWORD=password # Change to your DB Password
```

Use the command below to start the project:

```bash
$ php artisan serve
```

Login Credential:

```bash

# Super Admin
EMAIL=superadmin@example.com
PASSWORD=password123

# Admin Monitor
EMAIL=admin@example.com
PASSWORD=password123
```

Tambahakan:
- folder images/qrcode
- penambahan dependency baru (composer & )
