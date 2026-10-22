<?php
$mysqli=new mysqli('localhost','root','','studenthub');if($mysqli->connect_error){http_response_code(500);exit('Database connection failed. Import schema.sql and check db.php.');}$mysqli->set_charset('utf8mb4');
