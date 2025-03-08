// Add to existing database setup
$db->exec("CREATE TABLE IF NOT EXISTS referrals (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    referrer_id INT(11) NOT NULL,
    referred_id INT(11) NOT NULL UNIQUE,
    completed_tasks INT DEFAULT 0,
    total_earnings DECIMAL(10,2) DEFAULT 0.00,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (referrer_id) REFERENCES users(id),
    FOREIGN KEY (referred_id) REFERENCES users(id)
)");

$db->exec("CREATE TABLE IF NOT EXISTS referral_rewards (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    required_tasks INT NOT NULL,
    cash_reward DECIMAL(10,2) NOT NULL,
    coin_reward INT NOT NULL,
    is_active BOOLEAN DEFAULT 1
)");

$db->exec("CREATE TABLE IF NOT EXISTS withdrawals (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    coins INT NOT NULL,
    method VARCHAR(50) NOT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    processed_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id)
)");
