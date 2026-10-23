<?php
// Update these values to match your local MySQL configuration.
$mysqli = new mysqli('localhost', 'root', '', 'studenthub');
if ($mysqli->connect_error) {
    http_response_code(500);
    exit('Database connection failed. Check config/mysqli.php and import database/studenthub.sql.');
}
$mysqli->set_charset('utf8mb4');
