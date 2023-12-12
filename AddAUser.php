<?php
$feedback = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = "localhost";
    $username = "dbadmin";
    $password = "unlock";
    $dbname = "dolphin_crm";

    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO Users (firstname, lastname, password, email, role) VALUES (?, ?, ?, ?, ?)");

    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validate password with regular expressions
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
        $feedback = "Password must contain at least one number, one letter, one capital letter, and be at least 8 characters long";
    } else {
        // Hash the password
        $pepper = get_cfg_var("pepper");
        $pwd = $_POST['password'];
        $pwd_peppered = hash_hmac("sha256", $pwd, $pepper);
        $pwd_hashed = password_hash($pwd_peppered, CRYPT_SHA256);

        // Sanitize inputs
        $firstname = htmlspecialchars($firstname);
        $lastname = htmlspecialchars($lastname);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $role = ($role === 'Admin' || $role === 'Member') ? $role : '';

        // Bind parameters and execute
        $stmt->bind_param("sssss", $firstname, $lastname, $pwd_hashed, $email, $role);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $feedback = "User added successfully";
        } else {
            $feedback = "Error adding user";
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolphin CRM</title>
    <link rel="stylesheet" href="addstyles.css">
</head>

<body>
    <header>
        <h1>Dolphin CRM</h1>
    </header>
    <div class="sidebar">
        <ul>
            <li>Home</li>
            <li>New Contact</li>
            <li>Users</li>
            <li>Logout</li>
        </ul>
    </div>
    <div class="content">
        <h2>New User</h2>
        <div class="form-container">
            <?php echo $feedback; ?>
            <form action="AddAUser.php" method="post">
                <div class="name-inputs">
                    <input type="text" name="firstname" placeholder="First Name" required>
                    <input type="text" name="lastname" placeholder="Last Name" required>
                </div>
                <div class="email-password">
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <input type="text" name="role" placeholder="Role">
                <input type="submit" value="Save" class="save-btn">
            </form>
        </div>
    </div>
</body>

</html>

