<?php

session_name('system1_session');
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

include 'supplierdb_connect.php';

$email = $_SESSION['email'];

try {
    $stmt = $conn->prepare("SELECT firstname FROM admins WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $firstname = $user['firstname'];
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
    <title>Welcome, Admin <?php echo htmlspecialchars($firstname); ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom CSS for resizing carousel images */
        #propertyCarousel .carousel-item img {
            max-height: 400px; /* Adjust this value as needed */
            width: 100%;
            object-fit: cover; /* Ensure the image covers the entire slide */
        }
    </style>
</head>
<body>

<?php include 'admin_header.php'; ?>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="admin_dashboard.php">Home</a>
            <a class="navbar-brand" href="admin_all_tires.php">All Tires</a>
            <a class="navbar-brand" href="admin_add_tires.php">Add Tires</a>
            <a class="navbar-brand" href="admin_user_history_orders.php">See Customer Order Log</a>
            <a class="navbar-brand" href="admin_user_list.php">List of Customers</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Tire Types
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="admin_tire_type_PCT.php">PCT</a>
                            <a class="dropdown-item" href="admin_tire_type_TSUVT.php">TSUVT</a>
                            <a class="dropdown-item" href="admin_tire_type_PT.php">PT</a>
                            <a class="dropdown-item" href="admin_tire_type_ST.php">ST</a>
                            <a class="dropdown-item" href="admin_tire_type_CHDT.php">CHDT</a>
                            <a class="dropdown-item" href="admin_tire_type_ORAT.php">ORAT</a>
                        </div>
                    </li>
                </ul>
            </div>
        
           <!-- <form class="form-inline my-2 my-lg-0 mr-2" method="POST" action="">
                <input  class="form-control mr-sm-2" type="search" placeholder="Search for anything" aria-label="Search" name="search_query">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit" name="search">Search</button>
            </form>-->
        </div>
    </nav>

<!-- Main Content -->
<main class="container mt-4 mb-5">
    <h2>Welcome, Admin <?php echo htmlspecialchars($firstname); ?>!</h2>
    <!-- Carousel -->
    <div id="propertyCarousel" class="carousel slide mb-5" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#propertyCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#propertyCarousel" data-slide-to="1"></li>
            <li data-target="#propertyCarousel" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="storage/carousel1.png" class="d-block w-100" alt="Property Image 1">
            </div>
            <div class="carousel-item">
                <img src="storage/carousel2.png" class="d-block w-100" alt="Property Image 2">
            </div>
            <div class="carousel-item">
                <img src="storage/carousel3.png" class="d-block w-100" alt="Property Image 3">
            </div>
        </div>
        <a class="carousel-control-prev" href="#propertyCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#propertyCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</main>

<!-- Footer -->
<?php include 'footer.php'; ?>

<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
