<?php
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../includes/auth.php';
checkAdmin(); // Implement this function in auth.php

// Handle form submission to update referral rewards
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requiredTasks = filter_input(INPUT_POST, 'required_tasks', FILTER_VALIDATE_INT);
    $cashReward = filter_input(INPUT_POST, 'cash_reward', FILTER_VALIDATE_FLOAT);
    $coinReward = filter_input(INPUT_POST, 'coin_reward', FILTER_VALIDATE_INT);
    
    $stmt = $db->prepare("INSERT INTO referral_rewards 
        (required_tasks, cash_reward, coin_reward)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE
        cash_reward = VALUES(cash_reward),
        coin_reward = VALUES(coin_reward)");
    $stmt->execute([$requiredTasks, $cashReward, $coinReward]);
    
    $_SESSION['success'] = "Reward settings updated!";
    header("Location: referral_settings.php");
    exit();
}

// Get current rewards
$rewards = $db->query("SELECT * FROM referral_rewards ORDER BY required_tasks ASC")->fetchAll();
?>
<!-- HTML form to manage referral rewards -->
