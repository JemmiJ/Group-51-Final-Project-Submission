<?php
session_start();

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check if contact ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Contact ID not provided.";
    exit;
}

$contact_id = $_GET['id'];

// Database connection
$host = "localhost";
$username = "dbadmin";
$password = "password";
$dbname = "dolphin_crm";

$conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Check if the contact ID is provided in the URL
if (isset($_GET['contact_id']) && !empty($_GET['contact_id'])) {
    $contact_id = $_GET['contact_id'];

    // Fetch contact details from the database
    $stmt = $conn->prepare("SELECT c.title, c.firstname, c.lastname, c.email, c.company, c.created_at AS contact_created_at, 
                            CONCAT(u.firstname, ' ', u.lastname) AS created_by, c.updated_at AS contact_updated_at, 
                            CONCAT(u2.firstname, ' ', u2.lastname) AS updated_by, CONCAT(u3.firstname, ' ', u3.lastname) AS assigned_to
                            FROM Contacts c
                            LEFT JOIN Users u ON c.created_by = u.id
                            LEFT JOIN Users u2 ON c.updated_by = u2.id
                            LEFT JOIN Users u3 ON c.assigned_to = u3.id
                            WHERE c.id = ?");
    $stmt->execute([$contact_id]);
    $contact = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the contact exists
    if (!$contact) {
        echo "Contact not found.";
        exit;
    }
} else {
    echo "Contact ID not provided.";
    exit;
}
// Fetch notes for the contact
$stmt_notes = $conn->prepare("SELECT * FROM Notes WHERE contact_id = ?");
$stmt_notes->bindParam(1, $contact_id);
$stmt_notes->execute();
$notes = $stmt_notes->fetchAll(PDO::FETCH_ASSOC);

// Add note functionality (add_note.php)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_note'])) {
        // Retrieve new note content from POST
        $new_note = $_POST['new_note'];

        // Insert new note into the database for this contact
        $stmt_insert_note = $conn->prepare("INSERT INTO Notes (contact_id, comment, created_by, created_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP)");
        $stmt_insert_note->execute([$contact_id, $new_note, $_SESSION['user_id']]);

        // Redirect back to contact details page after adding note
        header("location: contact_details.php?id=$contact_id");
        exit;
    }
}

// Display contact details and functionalities (assign, change type, view notes, add note, etc.)
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Details</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header>
        <h1>Contacts<h1>
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
        <!-- Display contact details -->
        <div class="contact-details">
            <p><strong>Title:</strong> <?php echo $contact['title']; ?></p>
            <p><strong>Name:</strong> <?php echo $contact['title'] . ' ' . $contact['firstname'] . ' ' . $contact['lastname']; ?></p>
            <p><strong>Email:</strong> <?php echo $contact['email']; ?></p>
            <p><strong>Company:</strong> <?php echo $contact['company']; ?></p>
            <p><strong>Created at:</strong> <?php echo $contact['contact_created_at']; ?> by <?php echo $contact['created_by']; ?></p>
            <p><strong>Last updated at:</strong> <?php echo $contact['contact_updated_at']; ?> by <?php echo $contact['updated_by']; ?></p>
            <p><strong>Assigned to:</strong> <?php echo $contact['assigned_to']; ?></p>

            <!-- Assign to me button -->
            <form action="assign.php" method="post">
                <input type="hidden" name="contact_id" value="<?php echo $contact_id; ?>">
                <input type="submit" name="assign_me" value="Assign to Me">
            </form>

            <!-- Change contact type form -->
            <form action="change_contact_type.php" method="post">
                <input type="hidden" name="contact_id" value="<?php echo $contact_id; ?>">
                <input type="submit" name="change_type" value="Change Type">
            </form>

            <!-- Display notes -->
            <div class="notes-section">
                <h2>Notes</h2>
                <?php foreach ($notes as $note) : ?>
                    <div class="note">
                        <p><strong>Created by:</strong> <?php echo $note['created_by']; ?></p>
                        <p><strong>Comment:</strong> <?php echo $note['comment']; ?></p>
                        <p><strong>Created at:</strong> <?php echo $note['created_at']; ?></p>
                    </div>
                <?php endforeach; ?>

                <!-- Form to add a new note -->
                <form action="add_note.php" method="post">
                    <input type="hidden" name="contact_id" value="<?php echo $contact_id; ?>">
                    <textarea name="new_note" placeholder="Add a new note"></textarea>
                    <input type="submit" name="add_note" value="Add Note">
                </form>
            </div>
        </div>
    </div>
    <script>
    // Function to fetch contact details using Fetch API
        function fetchContactDetails(contactId) {
            fetch(`fetch_contact_details.php?id=${contactId}`)
                .then(response => response.json())
                .then(data => {
                    // Update the contact details section with the fetched data
                    document.querySelector('.contact-details').innerHTML = `
                        <!-- Display contact details based on the fetched data -->
                    `;
                })
                .catch(error => console.error('Error fetching contact details:', error));
        }

        // Function to add a new note using Fetch API
        function addNewNote(contactId, newNote) {
            fetch('process_add_note.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    contact_id: contactId,
                    new_note: newNote
                })
            })
            .then(response => response.json())
            .then(data => {
                // Fetch and update contact details after adding the note
                fetchContactDetails(contactId);
            })
            .catch(error => console.error('Error adding new note:', error));
        }

        // Example usage: Fetch contact details on page load
        const contactId = <?php echo $contact_id; ?>;
        fetchContactDetails(contactId);

        // Example: Adding a new note on form submission
        document.querySelector('form.add-note-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const newNote = document.querySelector('#new-note').value.trim();
            addNewNote(contactId, newNote);
        });
    </script>
    <footer>
        Copyright @ 2022 Dolphin CRM
    </footer>
</body>

</html>
