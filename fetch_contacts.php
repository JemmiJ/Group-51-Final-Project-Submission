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

// Fetch contacts based on filters (All, Sales Lead, Support, Assigned to me)
$filter = $_GET['filter'];

if ($filter === "All") {
    $stmt = $conn->query("SELECT * FROM Contacts");
} elseif ($filter === "Sales Lead") {
    $stmt = $conn->query("SELECT * FROM Contacts WHERE type = 'Sales Lead'");
} elseif ($filter === "Support") {
    $stmt = $conn->query("SELECT * FROM Contacts WHERE type = 'Support'");
} elseif ($filter === "Assigned to me") {
    // Assuming you have user ID stored in session after login
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM Contacts WHERE assigned_to = ?");
    $stmt->bindParam(1, $user_id);
    $stmt->execute();
} else {
    die("Invalid filter");
}

$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($contacts) {
    foreach ($contacts as $contact) {
        echo "<p>Title: " . $contact['title'] . ", Name: " . $contact['firstname'] . " " . $contact['lastname'] . ", Email: " . $contact['email'] . ", Company: " . $contact['company'] . ", Type: " . $contact['type'] . "</p>";
        echo "<a href='contact_details.php?id=" . $contact['id'] . "'>View Details</a><br>";
    }
} else {
    echo "No contacts found.";
}

$conn = null;
?>
