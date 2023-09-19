<?php 
session_start();

$con = new mysqli('localhost', 'root', '', 'project1');

if ($con->connect_errno) {
	echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
	exit();
}
?>