<?php
$servername = "sql206.infinityfree.com"; // Update with your InfinityFree hostname
$username = "if0_37370618";              // Update with your InfinityFree username
$password = "jfAmtPIM6m2q";             // Update with your InfinityFree password
$dbname = "if0_37370618_DailyDeals";    // Update with your InfinityFree database name

// Create connection
$conn = new mysqli("sql206.infinityfree.com", "if0_37370618", "jfAmtPIM6m2q", "if0_37370618_DailyDeals");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully!";
?>
