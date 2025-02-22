<?php
include 'retailerdb_connect.php';

session_name('system2_session');

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role']; // Assuming you have a role column
        if ($user['role'] == 'admin') {
            header("Location: customer_dashboard.php");
        } else {
            header("Location: customer_login.php");
        }
        exit();
    } else {
        echo "Invalid email or password.";
    }
}
?>
