// Add after updating user balance
// Process referral progress
if (isset($_SESSION['user_id'])) {
    require_once __DIR__.'/referral_handler.php';
    handleReferralProgress($_SESSION['user_id'], $rewards['cash']);
}
