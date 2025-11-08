<?php
include 'includes/config.php';

$database = new Database();
$db = $database->getConnection();

if($db) {
    echo "Database connected successfully!";
} else {
    echo "Database connection failed!";
}
?>