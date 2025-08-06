<?php
/**
 * Session Timeout Handler for Coin System
 * Automatically restores coins from abandoned cart sessions
 */

class SessionTimeoutHandler {
    private $conn;
    private $coinSystem;
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->coinSystem = new CoinSystem($conn);
    }
    
    /**
     * Check and handle session timeouts
     */
    public function handleSessionTimeout() {
        if (!isset($_SESSION['user_id'])) {
            return;
        }
        
        $user_id = $_SESSION['user_id'];
        $current_time = time();
        
        // Check if coins were applied more than 30 minutes ago
        if (isset($_SESSION['coins_applied_time']) && isset($_SESSION['coins_applied'])) {
            $coins_applied_time = $_SESSION['coins_applied_time'];
            $time_diff = $current_time - $coins_applied_time;
            
            // If more than 30 minutes have passed, restore coins
            if ($time_diff > 1800) { // 30 minutes = 1800 seconds
                $coins_to_restore = $_SESSION['coins_applied'];
                if ($coins_to_restore > 0) {
                    $this->coinSystem->addCoins($user_id, $coins_to_restore, "Session timeout - restore coins");
                    unset($_SESSION['coins_applied']);
                    unset($_SESSION['coins_applied_time']);
                }
            }
        }
    }
    
    /**
     * Update session timestamp when coins are applied
     */
    public function updateCoinApplicationTime() {
        $_SESSION['coins_applied_time'] = time();
    }
    
    /**
     * Clear session timeout data
     */
    public function clearSessionTimeout() {
        unset($_SESSION['coins_applied_time']);
    }
}
?>