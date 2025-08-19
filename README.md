# Mini-Google-Drive
Online File Sharing & Storage System (Mini Google Drive)
 Project Overview
This project is a PHP + MySQL-based mini cloud storage system where registered users can upload, download, and manage files securely. It works like a simplified version of Google Drive but supports only structured data handling using relational databases.

🎯 Features
User registration & login with bcrypt password hashing

Secure session management to prevent hijacking

File upload (PDF, DOCX, Images, etc. with size limit)

File management (view, download, delete)

Access control (users can manage only their own files)

SQL-based structured data storage

🛠️ Tech Stack
Backend: PHP (Core PHP, no frameworks)

Database: MySQL

Security: Password hashing (bcrypt), SQL injection prevention (prepared statements), session hardening

📂 Database Schema
users

id (PK, AUTO_INCREMENT)

name (VARCHAR)

email (VARCHAR, UNIQUE)

password (VARCHAR, hashed)

files

id (PK, AUTO_INCREMENT)

user_id (FK → users.id)

filename (VARCHAR)

filepath (TEXT)

size (BIGINT)

uploaded_at (TIMESTAMP)

🚀 How to Run
Import the provided SQL dump into MySQL.

Update config.php with your database credentials.

Place project files in htdocs (XAMPP) or www (WAMP).

Start Apache & MySQL.

Open browser → http://localhost/mini-drive

📑 Deliverables
PHP project files

MySQL dump file (sql_schema.sql)

README file
