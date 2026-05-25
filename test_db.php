<?php
$mysqli = new mysqli("127.0.0.1", "root", "", "os_tiket");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
echo "Connected successfully to " . $mysqli->host_info;
$mysqli->close();
