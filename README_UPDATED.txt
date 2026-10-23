WDF Practicals 4-14 (Updated package)

General setup for PHP/MySQL practicals (7-14):
1. Copy the Practical 7-14 folders into your XAMPP htdocs folder.
2. Start Apache and MySQL in XAMPP.
3. Import the relevant practical's database/studenthub.sql in phpMyAdmin.
4. Check config/db.php and config/mysqli.php if your MySQL credentials differ from the default root / blank password.
5. Open each practical using http://localhost/<folder-name>/... (not by double-clicking PHP files).
6. For Practical 9/10/14, register a user first. To make it an admin, run this in phpMyAdmin SQL tab:
   UPDATE users SET role='admin' WHERE email='your-registered-email@example.com';
7. For Practical 12/14 poster uploads, use JPG/PNG/WEBP under 2 MB. Upload directories are included.

Notes:
- Practical 10 now includes local registration, student records, event list, event management and profile pages used by its links.
- Practical 11 SQL includes 20 sample student records as required by the practical list.
- Practical 14 now includes the local login/logout, student CRUD, event CRUD and AJAX CRUD pages referenced by its dashboard menu.
- Each practical is a separate exercise and may use its own copy of the StudentHub database schema. Import the SQL file for the practical you are running.
- The remember-me option requires the remember_tokens table included in Practical 10's SQL file.
