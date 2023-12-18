<?php
session_start();

$host = "localhost";
$username = "dbadmin";
$password = "unlock";
$dbname = "dolphin_crm";

$conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$title = $_POST['title'];
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$email = $_POST['email'];
$company = $_POST['company'];
$type = $_POST['type'];

// Implement any necessary validation and sanitization of input here

$stmt = $conn->prepare("INSERT INTO Contacts (title, firstname, lastname, email, company, type) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bindParam(1, $title);
$stmt->bindParam(2, $firstname);
$stmt->bindParam(3, $lastname);
$stmt->bindParam(4, $email);
$stmt->bindParam(5, $company);
$stmt->bindParam(6, $type);

$stmt->execute();

echo "Contact added successfully";

$conn = null;
?>
