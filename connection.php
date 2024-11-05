<?php
$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'p_002';
$dbPort = '3306';

try {
  $dbConnect = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName, $dbPort);
  
  if ($dbConnect->connect_error) {
  throw new Exception("Connection failed: " . $dbConnect->connect_error);
}
} catch (Exception $e) {
  die("We're currently experiencing some technical difficulties. Please try again later.");
}
?>