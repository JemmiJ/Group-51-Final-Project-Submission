<?php
$host = "localhost";
$username = "dbadmin";
$password = "unlock";
$dbname = "dolphin_crm";

$conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM Users";
$result = $conn->query($sql);

if ($result->rowCount() > 0) {
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['firstname'] . "</td>";
        echo "<td>" . $row['lastname'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['role'] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>No users found</td></tr>";
}

$conn = null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List - Dolphin CRM</title>
    <link rel="stylesheet" href="listusers.css">
</head>

<body>
    <header>
        <h1>Users</h1>
        <div class="sidebar">
        <ul>
            <li><a href="Dashboard.php"> Home</a></li>
            <li><a href="add_contact.html"> New Contacts</a></li>
            <li><a href="ListUsers.php"> Users</a></li>
            <li><a href="logout.php">Logout</a></li>

        </ul>
    </header>
    <div class="content">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
                <?php include 'fetch_ListUsers.php'; ?>
            </tbody>
        </table>
    </div>
</body>

</html>