<?php
session_start();
// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Database connection details
$host = "localhost";
$username = "dbadmin";
$password = "unlock";
$dbname = "dolphin_crm";

$conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch contacts based on filters (All, Sales Lead, Support, Assigned to me)
$filter = $_GET['filter'] ?? 'All';

$stmt = null;

switch ($filter) {
    case 'Sales Lead':
        $stmt = $conn->query("SELECT * FROM Contacts WHERE type = 'Sales Lead'");
        break;
    case 'Support':
        $stmt = $conn->query("SELECT * FROM Contacts WHERE type = 'Support'");
        break;
    case 'Assigned to me':
        // Assuming you have user ID stored in session after login
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT * FROM Contacts WHERE assigned_to = ?");
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        break;
    default:
        $stmt = $conn->query("SELECT * FROM Contacts");
        break;
}

$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacts - Dolphin CRM</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header>
        <h1>Welcome to Dolphin CRM</h1>
        <nav>
            <ul>
                <li><a href="Dashboard.php"> Home</a></li>
                <li><a href="add_contact.html"> New Contacts</a></li>
                <li><a href="ListUsers.php"> Users</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <div class="content">
        <h2>Dashboard</h2>
        <div class="dashboard-widgets">
            <div class="add-contact">
                <a href="add_contact.php" class="add-contact-btn">Add New Contact</a>
            </div>
            <div class="contact-filters">
                <h3>Contact Filters</h3>
                <ul>
                    <li><a href="dashboard.php?filter=all">All Contacts</a></li>
                    <li><a href="dashboard.php?filter=sales">Sales Lead</a></li>
                    <li><a href="dashboard.php?filter=support">Support</a></li>
                    <li><a href="dashboard.php?filter=assigned">Assigned to Me</a></li>
                </ul>
            </div>
        <div class="contact-list">
            <?php if ($contacts) : ?>
                <?php foreach ($contacts as $contact) : ?>
                    <div class="contact">
                        <p>Title: <?php echo $contact['title']; ?></p>
                        <p>Name: <?php echo $contact['firstname'] . ' ' . $contact['lastname']; ?></p>
                        <p>Email: <?php echo $contact['email']; ?></p>
                        <p>Company: <?php echo $contact['company']; ?></p>
                        <p>Type: <?php echo $contact['type']; ?></p>
                        <a href="contact_details.php?id=<?php echo $contact['id']; ?>">View Details</a>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p>No contacts found.</p>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        Copyright @ 2022 Dolphin CRM
    </footer>
</body>

</html>