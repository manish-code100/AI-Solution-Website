# AI Solution Backend Setup

This project now includes the completed PHP, MySQL, enquiry, and admin management requirements.

## 1. Start XAMPP

Start these services in XAMPP:

- Apache or PHP local server
- MySQL

## 2. Import Database

Open XAMPP Shell or a terminal and run:

```bash
mysql -u root < database.sql
```

This creates:

- Database: `ai_solution_db`
- Table: `admins`
- Table: `enquiries`

## 3. Admin Login

Admin URL:

```text
http://127.0.0.1:8767/admin/login.php
```

Default login for university demonstration:

```text
Username: admin
Password: admin123
```

The password is stored as a hashed value in MySQL, not as plain text in browser JavaScript.

## 4. Public User Flow

Users submit enquiries from:

```text
http://127.0.0.1:8767/enquiry.html
```

The enquiry is processed by:

```text
submit-enquiry.php
```

Then it is stored in the MySQL `enquiries` table.

## 5. Admin Flow

After login, admin can:

- View dashboard statistics
- View recent enquiries
- Search and filter enquiries
- Update enquiry status
- Delete enquiries

## 6. Security Features Included

- PHP server-side validation
- JavaScript client-side validation
- PDO prepared statements
- Hashed admin password
- Session-based admin protection
- CSRF tokens on admin actions
- User and admin areas separated
