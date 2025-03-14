<?php

class Cart
{   
    // --------- ADD TO CART/BASKIT -------- //
    public static function addToCart($userId, $productId, $quantity, $portion, $conn, $productImageUrl)
    {
        // Validate user existence
        $userQuery = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($userQuery);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $userResult = $stmt->get_result();
    
        if ($userResult->num_rows === 0) {
            return ['message' => 'User not found'];
        }
    
        // Fetch product details
        $product = Product::getProductById($productId, $conn);
        if (!$product) {
            return ['message' => 'Product not found'];
        }
    
        // Check if product already exists in the cart
        $sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ? AND product_portion = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $userId, $productId, $portion);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Update cart if product already exists
            $sql = "UPDATE cart 
                    SET product_quantity = product_quantity + ?, product_image = ? 
                    WHERE user_id = ? AND product_id = ? AND product_portion = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isiis", $quantity, $productImageUrl, $userId, $productId, $portion);
        } else {
            // Insert new product into the cart
            $sql = "INSERT INTO cart 
                      (user_id, product_id, product_name, product_price, product_quantity, product_portion, product_origin, store_id, store_name, product_image) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iisdississ", 
                $userId, 
                $productId, 
                $product['product_name'], 
                $product['product_price'], 
                $quantity, 
                $portion, 
                $product['product_origin'], 
                $product['store_id'], 
                $product['store_name'], 
                $productImageUrl
            );
        }
    
        // Execute the query
        return $stmt->execute() ? ['message' => 'Added to cart'] : ['message' => 'Failed to add to cart'];
    }
    
    // --------- GET USER CART BASICALLY KUNIN LAHAT NG LAMAN NG CART NG SPECIFIC USER -------- //
    public static function getUserCart($userId, $conn)
    {
        $sql = "SELECT product_id, product_name, product_price, product_quantity, product_portion, product_origin, store_id, store_name, product_image FROM cart WHERE user_id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $cartItems = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            
            if (empty($cartItems)) {
                return ['message' => 'Your Cart is Empty.'];
            }
            return $cartItems;
        } else {
            return ['message' => 'Failed to prepare statement.'];
        }
    }

    // --------- UPDATE CART -------- //
    public static function updateCart($userId, $productId, $quantity, $portion, $conn)
    {
        AuthMiddleware::checkAuth();
        $sql = "UPDATE cart SET product_quantity = ? WHERE user_id = ? AND product_id = ? AND product_portion = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiis", $quantity, $userId, $productId, $portion);
        return $stmt->execute() ? ['message' => 'Cart updated'] : ['message' => 'Failed to update cart'];
    }

    // --------- REMOVE PRODUCT SA CART -------- //
    public static function removeFromCart($userId, $productId, $portion, $conn)
    {
        AuthMiddleware::checkAuth();
        $sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ? AND product_portion = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $userId, $productId, $portion);
        return $stmt->execute() ? ['message' => 'Removed from cart'] : ['message' => 'Failed to remove from cart'];
    }
}
?>