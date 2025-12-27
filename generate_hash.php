<?php
/**
 * Password Hash Generator
 * Run this file to generate a bcrypt hash for 'admin123'
 */

$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: " . $password . "\n";
echo "Hash: " . $hash . "\n";
echo "\nCopy this hash to database.sql file\n";
?>
