<?php
include 'retailerdb_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW()");
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    $reset_request = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($reset_request) {
        $email = $reset_request['email'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE admins SET password = :password WHERE email = :email");
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':email', $email);
        if ($stmt->execute()) {
            // Delete the reset token after successful password reset
            $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            echo "Password has been reset successfully. <a href='customer_login.php'>Login</a>";
        } else {
            echo "Failed to reset password.";
        }
    } else {
        echo "Invalid or expired token.";
    }
}
?>
