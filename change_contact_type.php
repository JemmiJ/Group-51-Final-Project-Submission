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

// Check if contact_id and new_type are provided in the request
if (isset($_POST['contact_id'], $_POST['new_type']) && !empty($_POST['contact_id']) && !empty($_POST['new_type'])) {
    $contact_id = $_POST['contact_id'];
    $new_type = $_POST['new_type'];

    // Update the type field of the contact
    $stmt = $conn->prepare("UPDATE Contacts SET type = ? WHERE id = ?");
    $stmt->execute([$new_type, $contact_id]);

    // Check if the update was successful
    if ($stmt->rowCount() > 0) {
        echo "Contact type changed successfully.";
    } else {
        echo "Failed to change contact type.";
    }
} else {
    echo "Contact ID or new type not provided.";
}
?>
