<?php
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkAuth();
    
    $userId = $_SESSION['user_id'];
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    $coins = filter_input(INPUT_POST, 'coins', FILTER_VALIDATE_INT);
    $method = filter_input(INPUT_POST, 'method', FILTER_SANITIZE_STRING);
    
    try {
        $db->beginTransaction();
        
        // Check balance
        $stmt = $db->prepare("SELECT balance, bonus_coins FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $balance = $stmt->fetch();
        
        if ($balance['balance'] >= $amount && $balance['bonus_coins'] >= $coins) {
            // Deduct balances
            $stmt = $db->prepare("UPDATE users 
                SET balance = balance - ?,
                    bonus_coins = bonus_coins - ?
                WHERE id = ?");
            $stmt->execute([$amount, $coins, $userId]);
            
            // Create withdrawal request
            $stmt = $db->prepare("INSERT INTO withdrawals 
                (user_id, amount, coins, method)
                VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $amount, $coins, $method]);
            
            $db->commit();
            $_SESSION['success'] = "Withdrawal request submitted!";
        } else {
            $_SESSION['error'] = "Insufficient balance for withdrawal";
        }
        
        header("Location: /dashboard.php");
        exit();
        
    } catch(Exception $e) {
        $db->rollBack();
        die("Withdrawal error: ".$e->getMessage());
    }
}
?>
