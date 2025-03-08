<div class="withdrawal-section">
    <h2>Withdraw Earnings</h2>
    <form method="POST" action="/api/withdraw.php">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        
        <div class="form-group">
            <label>Amount:</label>
            <input type="number" name="amount" step="0.01" min="<?= $minWithdrawal ?>" required>
        </div>
        
        <div class="form-group">
            <label>Coins to Convert:</label>
            <input type="number" name="coins" min="0" max="<?= $_SESSION['bonus_coins'] ?>">
        </div>
        
        <div class="form-group">
            <label>Payment Method:</label>
            <select name="method" required>
                <option value="paypal">PayPal</option>
                <option value="bank">Bank Transfer</option>
                <option value="upi">UPI</option>
            </select>
        </div>
        
        <button type="submit">Request Withdrawal</button>
    </form>
    
    <h3>Withdrawal History</h3>
    <?php
    $stmt = $db->prepare("SELECT * FROM withdrawals 
        WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    while ($withdrawal = $stmt->fetch()):
    ?>
    <div class="withdrawal-item">
        <?= $withdrawal['amount'] ?> USD - 
        <?= $withdrawal['coins'] ?> Coins - 
        <?= $withdrawal['status'] ?>
    </div>
    <?php endwhile; ?>
</div>
