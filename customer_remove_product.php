<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cart_id'])) {
    include 'retailerdb_connect.php';

    $cart_id = $_POST['cart_id'];

    try {
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = :cart_id");
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->execute();

        // Prepare JSON response
        $response = [
            'success' => true,
            'message' => 'Item removed successfully.'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
    } catch (PDOException $e) {
        // Prepare JSON response for error
        $response = [
            'success' => false,
            'message' => 'Error removing item: ' . $e->getMessage(),
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    exit();
} else {
    // Redirect to cart page if not a POST request
    header("Location: customer_view_cart.php");
    exit();
}
?>