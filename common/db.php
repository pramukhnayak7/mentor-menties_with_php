<?php
$host = 'mentorpagemysql-mentorwebsite.l.aivencloud.com';
$port = 24268;
$dbname = 'academics_db';
$user = 'avnadmin';
$pass = 'AVNS_C5aEDYiWtC6Lp_wTIqm';
$ssl_mode = 'REQUIRED';

// Create a new MySQLi connection
$conn = new mysqli($host, $user, $pass, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Enable SSL if needed (Aiven requires it)
$conn->ssl_set(NULL, NULL, NULL, NULL, NULL);
