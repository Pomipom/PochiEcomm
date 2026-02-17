<?php
include 'db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // GET DATA FORM - Added null coalescing to prevent the Undefined Key warning
    $product_id = $_POST['Prod_ID'] ?? ''; 
    $quantity = $_POST['quantity'] ?? 0;
    // You must fetch these from $_POST or they will be empty
    $client_name = $_POST['client_name'] ?? 'Unknown'; 
    $client_contact = $_POST['client_contact'] ?? 'Unknown';

    // GET PRODUCT DATA FROM DATABASE
    $sql = "SELECT Prod_name, Price, Stocks, Prod_img FROM product WHERE Prod_ID = '$product_id'";
    $result = $conn->query($sql);
    $product = mysqli_fetch_assoc($result);

    if(!$product){
        die("Product not found.");
    }

    $price = $product['Price'];
    $current_stock = $product['Stocks'];
    $total_price = $price * $quantity; // This variable is used below

    // STOCK CHECK
    if($quantity > $current_stock){
        die("Order exceeds available stock");
    }

    // INSERT ORDER - Fixed array keys and variable names
    $insert = "INSERT INTO orders(order_name, order_img, quantity, total, client_name, client_contact) 
    VALUES('{$product['Prod_name']}', 
           '{$product['Prod_img']}',
           '$quantity',
           '$total_price', 
           '$client_name',
           '$client_contact')";
    
    $conn->query($insert); 

    // UPDATE STOCK
    $new_stock = $current_stock - $quantity; 
    $sql_update = " UPDATE product SET Stocks = '$new_stock' WHERE Prod_ID = '$product_id'"; 
    
    mysqli_query($conn, $sql_update);

    echo "<script>
    alert('Order placed successfully!');
    window.location.href = 'products.php';
    </script>";
}
?>