<?php
session_name('system2_session');
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: customer_login.php");
    exit();
}

include 'retailerdb_connect.php';

try {
    $user_email = $_SESSION['email'];
    $stmt = $conn->prepare("
        SELECT t.*, c.id as cart_id, c.amount as cart_amount
        FROM cart c
        JOIN tires t ON c.tire_id = t.id
        WHERE c.user_email = :user_email
    ");
    $stmt->bindParam(':user_email', $user_email);
    $stmt->execute();
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $response = [
        'success' => false,
        'message' => 'Error fetching cart items: ' . $e->getMessage(),
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Handle updating cart quantity (AJAX handling)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_cart'])) {
    $cart_id = $_POST['cart_id'];
    $new_amount = $_POST['amount'];

    try {
        $stmt = $conn->prepare("UPDATE cart SET amount = :amount WHERE id = :cart_id");
        $stmt->bindParam(':amount', $new_amount);
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->execute();

        // Fetch updated item details to calculate new total
        $stmt = $conn->prepare("
            SELECT t.price, c.amount
            FROM cart c
            JOIN tires t ON c.tire_id = t.id
            WHERE c.id = :cart_id
        ");
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->execute();
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        // Calculate new total
        $new_total = $item['price'] * $item['amount'];

        // Prepare JSON response
        $response = [
            'success' => true,
            'new_total' => $new_total,
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    } catch (PDOException $e) {
        // Prepare JSON response for error
        $response = [
            'success' => false,
            'message' => 'Error updating cart: ' . $e->getMessage(),
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar (Include as needed) -->
<?php include 'customer_header.php'; ?>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="customer_dashboard.php">Home</a>
            <a class="navbar-brand" href="customer_all_tires.php">All Tires</a>
            <a class="navbar-brand" href="customer_my_order_logs.php">My Order Logs</a>
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
                            <a class="dropdown-item" href="customer_tire_type_PCT.php">PCT</a>
                            <a class="dropdown-item" href="customer_tire_type_TSUVT.php">TSUVT</a>
                            <a class="dropdown-item" href="customer_tire_type_PT.php">PT</a>
                            <a class="dropdown-item" href="customer_tire_type_ST.php">ST</a>
                            <a class="dropdown-item" href="customer_tire_type_CHDT.php">CHDT</a>
                            <a class="dropdown-item" href="customer_tire_type_ORAT.php">ORAT</a>
                        </div>
                    </li>
                </ul>
            </div>
            
            <form class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2" type="search" placeholder="Search for anything" aria-label="Search">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </form>
        </div>
    </nav>

<div class="container mt-4">
    <h2>Your Cart</h2>
    <div class="row">
        <?php if (count($cart_items) > 0): ?>
        <?php foreach ($cart_items as $item): ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <img class="card-img-top" src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                <div class="card-body">
                    <h4 class="card-title"><?php echo htmlspecialchars($item['item_name']); ?></h4>
                    <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($item['brand']); ?></h6>
                    <p class="card-text"><?php echo htmlspecialchars($item['description']); ?></p>
                    <h5 class="card-text">Price: $<?php echo htmlspecialchars($item['price']); ?></h5>
                    <form id="update_form_<?php echo $item['cart_id']; ?>" method="post" action="">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <button class="btn btn-outline-secondary" type="button" onclick="decreaseAmount(<?php echo $item['cart_id']; ?>)">-</button>
                            </div>
                            <input type="number" name="amount" id="amount_<?php echo htmlspecialchars($item['cart_id']); ?>" class="form-control text-center" value="<?php echo htmlspecialchars($item['cart_amount']); ?>" min="1" max="<?php echo htmlspecialchars($item['amount']); ?>">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" onclick="increaseAmount(<?php echo $item['cart_id']; ?>)">+</button>
                            </div>
                            <input type="hidden" name="cart_id" value="<?php echo htmlspecialchars($item['cart_id']); ?>">
                        </div>
                    </form>
                    <p class="card-text">Total: $<span id="total_<?php echo $item['cart_id']; ?>"><?php echo htmlspecialchars($item['price'] * $item['cart_amount']); ?></span></p>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-danger" onclick="showDeleteModal(<?php echo $item['cart_id']; ?>)">Remove</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>
    <div class="mt-4">
        <a href="<?php echo isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'customer_all_tires.php'; ?>" class="btn btn-secondary">Back to Tires</a>
        <button type="button" class="btn btn-primary" id="checkoutBtn">Proceed to Checkout</button>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to remove this item from your cart?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Remove</button>
            </div>
        </div>
    </div>
</div>

<!-- Checkout Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="checkoutModalLabel">Confirm Checkout</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to proceed to checkout?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmCheckoutBtn">Proceed</button>
            </div>
        </div>
    </div>
</div>

<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
function showDeleteModal(cartId) {
    $('#deleteModal').data('cart-id', cartId).modal('show');
}

function decreaseAmount(cartId) {
    let amountInput = document.getElementById('amount_' + cartId);
    let currentAmount = parseInt(amountInput.value);
    if (currentAmount > 1) {
        amountInput.value = currentAmount - 1;
        updateCart(cartId);
    }
}

function increaseAmount(cartId) {
    let amountInput = document.getElementById('amount_' + cartId);
    let currentAmount = parseInt(amountInput.value);
    amountInput.value = currentAmount + 1;
    updateCart(cartId);
}

function updateCart(cartId) {
    let form = document.getElementById('update_form_' + cartId);
    let formData = new FormData(form);

    fetch('customer_view_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('total_' + cartId).textContent = data.new_total.toFixed(2);
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
    let cartId = $('#deleteModal').data('cart-id');

    fetch('customer_remove_product.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({ 'cart_id': cartId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
});

document.getElementById('checkoutBtn').addEventListener('click', function() {
    $('#checkoutModal').modal('show');
});

document.getElementById('confirmCheckoutBtn').addEventListener('click', function() {
    // Perform any additional checks or operations here before redirecting
    window.location.href = 'checkout.php'; // Redirect to checkout page
});
</script>

</body>
</html>
