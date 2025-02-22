<?php


// Check if user is logged in as admin
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: customer_login.php");
    exit();
}

include 'retailerdb_connect.php';

$email = $_SESSION['email'];

try {
    $stmt = $conn->prepare("SELECT firstname, profilepicture FROM admins WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $firstname = $user['firstname'];
        $profilepicture = $user['profilepicture']; // Assuming this is the relative path or filename
        $_SESSION['profilepicture'] = $profilepicture; // Set the session variable for profile picture
    } else {
        echo "User not found.";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="style/customer_dashboard.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex: 1;
        }
        .navbar-nav .nav-link {
            color: white !important; /* Change text color to white */
        }
        .navbar-nav .nav-link.dropdown-toggle {
        color: black !important; /* Change dropdown toggle text color to black */
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="bg-dark border-bottom py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="#" class="navbar-brand"><img src="automoto.png" alt="Carousell Logo"></a>
            <nav class="d-flex align-items-center">
                    <img src="storage/profile_pictures/<?php echo $_SESSION['profilepicture']; ?>" alt="Profile Picture" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                    <a class="nav-link" href="customer_logout.php">Logout</a>
                <a class="nav-link" href="customer_view_cart.php"><i class="fas fa-shopping-cart"></i></a>
            </nav>
        </div>
    </header>

