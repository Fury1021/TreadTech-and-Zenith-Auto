<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" type="text/css" href="style/resetpassword.css">
</head>
<body>
    <h2>Reset Password</h2>
    <form action="admin_resetpassword_action.php" method="post">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
        <label for="password">New Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
