# School Accounting App

Requirements :

1. PHP ^8.1
2. Laravel ^10.0
3. Bootstrap 5.3

Install the dependencies:

```bash
$ composer install
```

```bash
$ npm install
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


Change Akun Bank ke masing masing kas:
- School = 15
- Acc Piutang SPP = 484
- UKS = 485

- Query Student Receivables, deskripsi pembayaran diambil dari sini
SELECT student_receivables.id, account_id, accounts.code, accounts.name, student_receivables.school_id, students.name, student_receivables.amount, paid_amount, total_discount, due_date, status, student_receivable_details.description, student_receivable_details.amount, student_receivable_details.period
FROM `student_receivables`
JOIN student_receivable_details ON student_receivables.id = student_receivable_details.student_receivable_id
JOIN accounts ON student_receivables.account_id = accounts.id
JOIN students ON student_receivables.student_id = students.id
WHERE student_receivables.school_id = 15
AND student_receivables.account_id = 484
ORDER BY student_receivables.updated_at DESC

SELECT student_receivables.id, student_receivables.account_id, accounts.code, accounts.name, student_receivables.school_id, students.name, paid_amount, status, student_receivable_details.description
FROM `student_receivables`
JOIN student_receivable_details ON student_receivables.id = student_receivable_details.student_receivable_id
JOIN accounts ON student_receivables.account_id = accounts.id
JOIN students ON student_receivables.student_id = students.id
WHERE student_receivables.school_id = 15
AND accounts.name LIKE 'Piutang SPP'
ORDER BY student_receivables.updated_at DESC

Query Transaksi (478 = Kas Bank), Filter berdasarkan id reference
SELECT transactions.id, transactions.school_id, accounts.id, accounts.code, accounts.name, date, description, debit, credit, reference_id
FROM `transactions`
JOIN accounts ON transactions.account_id = accounts.id
WHERE transactions.school_id = 15
AND transactions.account_id = 478
ORDER BY transactions.created_at DESC

