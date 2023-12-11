<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = "localhost";
    $username = "dbadmin";
    $password = "unlock";
    $dbname = "schema";

    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    $email = $_POST['email'];
    $stmt = $conn->prepare("SELECT 'password' FROM Users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $hashed_password_from_db = $result['password'];

        
        $pepper = get_cfg_var("pepper");
        $pwd = $_POST['password'];
        $pwd_peppered = hash_hmac("SHA256", $pwd, $pepper);

        if (password_verify($pwd_peppered, $hashed_password_from_db)) {
            session_start();
            echo "Login successful";
            // Redirect to a dashboard or another page upon successful login
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Incorrect email or password";
            header("Location: Login.php");
            exit();
        }
    } else {
        echo "User not found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolphin CRM Login</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header>
        <h1>Dolphin CRM</h1>
    </header>
    <div class="login-container">
        <form action="" method="post">
            <h2>Login</h2>
            <input type="email" name="email" placeholder="Email address">
            <input type="password" name="password" placeholder="Password">
            <input type="submit" name="submit" value="Login" class="login-btn">
        </form>
    </div>
    <footer>Copyright @ 2022 Dolphin CRM</footer>
</body>

</html>
