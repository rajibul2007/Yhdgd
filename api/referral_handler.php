<?php
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../includes/auth.php';

function handleReferralProgress($userId, $taskEarnings) {
    global $db;
    
    // Check if user was referred
    $stmt = $db->prepare("SELECT referrer_id FROM referrals WHERE referred_id = ?");
    $stmt->execute([$userId]);
    $referral = $stmt->fetch();
    
    if ($referral) {
        $referrerId = $referral['referrer_id'];
        
        // Update referral progress
        $db->beginTransaction();
        try {
            // Update completed tasks and total earnings
            $stmt = $db->prepare("UPDATE referrals 
                SET completed_tasks = completed_tasks + 1,
                    total_earnings = total_earnings + ?
                WHERE referred_id = ?");
            $stmt->execute([$taskEarnings, $userId]);
            
            // Check if reached reward threshold
            $stmt = $db->prepare("SELECT completed_tasks FROM referrals WHERE referred_id = ?");
            $stmt->execute([$userId]);
            $progress = $stmt->fetch();
            
            // Get current reward settings
            $rewardStmt = $db->query("SELECT * FROM referral_rewards WHERE is_active = 1 ORDER BY required_tasks DESC");
            $rewards = $rewardStmt->fetchAll();
            
            foreach ($rewards as $reward) {
                if ($progress['completed_tasks'] >= $reward['required_tasks']) {
                    // Apply rewards
                    $stmt = $db->prepare("UPDATE users 
                        SET balance = balance + ?,
                            bonus_coins = bonus_coins + ?
                        WHERE id IN (?, ?)");
                    $stmt->execute([
                        $reward['cash_reward'],
                        $reward['coin_reward'],
                        $userId,        // Referred user
                        $referrerId     // Referrer
                    ]);
                    break;
                }
            }
            
            // Apply lifetime 10% coin reward
            $coinReward = $taskEarnings * 0.10;
            $stmt = $db->prepare("UPDATE users 
                SET bonus_coins = bonus_coins + ?
                WHERE id = ?");
            $stmt->execute([$coinReward, $referrerId]);
            
            $db->commit();
        } catch(Exception $e) {
            $db->rollBack();
            error_log("Referral error: ".$e->getMessage());
        }
    }
}
?>
