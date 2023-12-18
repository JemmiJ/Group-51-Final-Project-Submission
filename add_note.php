<?php
// Your database connection credentials
$host = "localhost";
$username = "dbadmin";
$password = "password";
$dbname = "dolphin_crm";

// Establish a connection to the database
$conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if contact_id, comment, and created_by are provided in the request
if (isset($_POST['contact_id'], $_POST['comment'], $_POST['created_by']) && !empty($_POST['contact_id']) && !empty($_POST['comment']) && !empty($_POST['created_by'])) {
    $contact_id = $_POST['contact_id'];
    $comment = $_POST['comment'];
    $created_by = $_POST['created_by'];

    // Add a new note for the contact
    $stmt = $conn->prepare("INSERT INTO Notes (contact_id, comment, created_by) VALUES (?, ?, ?)");
    $stmt->execute([$contact_id, $comment, $created_by]);

    // Check if the insertion was successful
    if ($stmt->rowCount() > 0) {
        echo "Note added successfully.";
    } else {
        echo "Failed to add note.";
    }
} else {
    echo "Contact ID, comment, or creator ID not provided.";
}
?>
