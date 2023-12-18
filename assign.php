<?php
// Your database connection credentials
$host = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "dolphin_crm";

// Establish a connection to the database
$conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if contact_id and user_id are provided in the request
if (isset($_POST['contact_id'], $_POST['user_id']) && !empty($_POST['contact_id']) && !empty($_POST['user_id'])) {
    $contact_id = $_POST['contact_id'];
    $user_id = $_POST['user_id'];

    // Update the assigned_to field of the contact
    $stmt = $conn->prepare("UPDATE Contacts SET assigned_to = ? WHERE id = ?");
    $stmt->execute([$user_id, $contact_id]);

    // Check if the update was successful
    if ($stmt->rowCount() > 0) {
        echo "Contact assigned successfully.";
    } else {
        echo "Failed to assign contact.";
    }
} else {
    echo "Contact ID or User ID not provided.";
}
?>