# AI Solution Website

AI Solution is a university product development web project for an AI services company. It includes a modern public website, an enquiry form, a PHP/MySQL backend, and a protected admin panel for managing customer enquiries.

The project is built with:

- HTML
- CSS
- JavaScript
- PHP
- MySQL
- XAMPP/phpMyAdmin

## Main Features

- Modern responsive AI business website
- Homepage with hero section, about content, testimonials, events, gallery, articles and contact routes
- Enquiry form for potential customers
- Server-side enquiry validation with PHP
- MySQL database storage for enquiries
- Password-protected admin login
- Admin dashboard with enquiry statistics
- Enquiry management table
- Status update and delete controls
- CSRF protection for admin actions
- Hashed admin password stored in the database
- PDO prepared statements for database queries
- Public user pages separated from admin pages

## Project Structure

```text
AI-Solutions E-Commerce Website with Enquiry and Admin Management System/
+-- admin/
|   +-- dashboard.php
|   +-- enquiries.php
|   +-- login.php
|   +-- logout.php
+-- assets/
|   +-- ai-solution-logo.svg
+-- documentation-diagrams/
|   +-- AI_Solution_Use_Case_Diagram.png
|   +-- AI_Solution_DFD_Level_0_Context.png
|   +-- AI_Solution_DFD_Level_1_User_Enquiry.png
|   +-- AI_Solution_DFD_Level_1_Admin_Management.png
|   +-- database and flowchart diagrams
+-- includes/
|   +-- auth.php
|   +-- config.php
|   +-- db.php
+-- articles.html
+-- client-testimonial.html
+-- contact.html
+-- database.sql
+-- enquiry.html
+-- events.html
+-- gallery.html
+-- index.html
+-- navigation-buttons.js
+-- script.js
+-- solutions.html
+-- styles.css
+-- submit-enquiry.php
```

## Public Website Pages

| Page | Purpose |
| --- | --- |
| `index.html` | Main homepage for AI Solution |
| `solutions.html` | AI services and solution cards |
| `client-testimonial.html` | Client testimonial content |
| `articles.html` | AI/business articles |
| `events.html` | Upcoming and past event content |
| `gallery.html` | Gallery images and project visuals |
| `contact.html` | Contact information and enquiry route |
| `enquiry.html` | Main customer enquiry form |

## Backend and Admin Pages

| File | Purpose |
| --- | --- |
| `submit-enquiry.php` | Validates and saves customer enquiries |
| `includes/config.php` | Database and app configuration |
| `includes/db.php` | PDO database connection |
| `includes/auth.php` | Session, login, logout and CSRF helpers |
| `admin/login.php` | Admin login page |
| `admin/dashboard.php` | Admin overview and enquiry statistics |
| `admin/enquiries.php` | Enquiry management table |
| `admin/logout.php` | Secure admin logout |

## Database Design

The project uses one database:

```text
ai_solution_db
```

It contains two main tables:

```text
admins
------
id
username
password_hash
created_at
last_login_at

enquiries
---------
id
name
email
phone
company
country
job_title
service
timeline
message
status
created_at
updated_at
```

The database can be created from:

```text
database.sql
```

## How to Run the Project Locally

### 1. Install XAMPP

Install XAMPP and start:

- Apache
- MySQL

### 2. Copy or Open the Project

Place the project folder inside your XAMPP `htdocs` folder, or run it using a PHP local server.

Example XAMPP path:

```text
C:\xampp\htdocs\ai-solution
```

### 3. Import the Database

Open phpMyAdmin:

```text
http://localhost/phpmyadmin
```

Then import:

```text
database.sql
```

This creates the database, admin table and enquiries table.

### 4. Configure the Database

The local database settings are in:

```text
includes/config.php
```

Default XAMPP settings:

```php
const DB_HOST = '127.0.0.1';
const DB_PORT = '3306';
const DB_NAME = 'ai_solution_db';
const DB_USER = 'root';
const DB_PASS = '';
```

For production, do not use the root database account. Create a restricted database user and keep credentials outside public web files.

### 5. Open the Website

If the project is inside `htdocs`, open:

```text
http://localhost/ai-solution/index.html
```

If using PHP's built-in server from the project folder:

```bash
php -S 127.0.0.1:8767
```

Then open:

```text
http://127.0.0.1:8767/index.html
```

## Admin Login

Admin login page:

```text
http://127.0.0.1:8767/admin/login.php
```

Default university demonstration account:

```text
Username: admin
Password: admin123
```

The password is not stored in plain text in the database. The database stores a `password_hash`, and PHP verifies login using `password_verify()`.

## Enquiry Workflow

```text
Visitor opens enquiry/contact form
        |
        v
Visitor submits name, email, service, timeline and message
        |
        v
submit-enquiry.php validates the data
        |
        v
PDO prepared statement saves the enquiry
        |
        v
MySQL stores the enquiry in the enquiries table
        |
        v
Admin logs in and manages the enquiry
```

## Security Features

This project includes basic security controls suitable for a PHP/MySQL university prototype:

- Server-side validation in PHP
- PDO prepared statements
- Hashed admin password
- Session-based admin protection
- CSRF tokens for admin forms
- Admin pages separated from public navigation
- Honeypot field in the enquiry form
- Output escaping with `htmlspecialchars`

Recommended production improvements:

- HTTPS/TLS
- Restricted database user
- Environment-based credentials
- Rate limiting for admin login
- Stronger audit logging
- Regular database backups
- Password reset workflow

## Testing Checklist

Before demonstrating the project, test these flows:

1. Homepage opens correctly.
2. Navigation links work.
3. Solutions, testimonials, articles, events and gallery pages load.
4. Enquiry form shows validation errors for invalid input.
5. Valid enquiry submission shows success.
6. Submitted enquiry appears in phpMyAdmin.
7. Admin login rejects wrong credentials.
8. Admin login accepts valid credentials.
9. Admin dashboard displays enquiry statistics.
10. Admin can view, update and delete enquiries.
11. Public navigation does not expose admin access.
12. Website layout works on mobile width.

## Academic Purpose

This project was created for the CET333 Product Development module. It demonstrates requirement analysis, planning, UI design, backend implementation, database design, testing, deployment preparation and reflection.

## Author

Developed by Manish Sah.
