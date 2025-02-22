<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
        }
        .form-container {
            width: 400px;
            padding: 30px;
            background: #f0f0f0;
            border-radius: 5px;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
        }
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
            background-color: black;
            border-radius: 5px;
        }
        .logo-container img {
            max-width: 100px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="logo-container">
            <img src="logo.png" alt="Your Logo">
        </div>
        <h2 class="text-center">Login</h2>
        <!--redirects to admin_login_action to handle the action -->
        <form action="admin_login_action.php" method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        <p class="mt-3 text-center">
            <!-- redirects to admin_register-->
            <a href="admin_register.php">Don't have an account?</a><br>
            <!-- redirects to admin_forgotpassword-->
            <a href="admin_forgotpassword.php">Forgot your password?</a>
        </p>
    </div>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
