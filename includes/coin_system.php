<?php
/**
 * Comprehensive Coin System for Vonia
 * Handles coin management, rewards, transactions, and security
 */

class CoinSystem
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Initialize wallet for new user
     */
    public function initializeWallet($user_id, $initial_coins = 50)
    {
        try {
            // Check if wallet already exists
            $check_stmt = $this->conn->prepare("SELECT id FROM wallet WHERE user_id = ?");
            $check_stmt->bind_param("i", $user_id);
            $check_stmt->execute();
            $result = $check_stmt->get_result();

            if ($result->num_rows == 0) {
                // Create new wallet
                $create_stmt = $this->conn->prepare("INSERT INTO wallet (user_id, coins) VALUES (?, ?)");
                $create_stmt->bind_param("ii", $user_id, $initial_coins);

                if ($create_stmt->execute()) {
                    // Log the initial coins as a reward
                    $this->addRewardLog($user_id, $initial_coins, "Welcome bonus");
                    return true;
                } else {
                    throw new Exception("Failed to create wallet");
                }
            }
            return true;
        } catch (Exception $e) {
            error_log("Coin System Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user's current coin balance
     */
    public function getCoinBalance($user_id)
    {
        try {
            $stmt = $this->conn->prepare("SELECT coins FROM wallet WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                return intval($row['coins']);
            }
            return 0;
        } catch (Exception $e) {
            error_log("Coin System Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Add coins to user's wallet (for rewards, purchases, etc.)
     */
    public function addCoins($user_id, $amount, $reason = "Reward")
    {
        try {
            // Start transaction
            $this->conn->begin_transaction();

            // Update wallet
            $update_stmt = $this->conn->prepare("UPDATE wallet SET coins = coins + ? WHERE user_id = ?");
            $update_stmt->bind_param("ii", $amount, $user_id);

            if (!$update_stmt->execute()) {
                throw new Exception("Failed to update wallet");
            }

            // Log the transaction
            $this->addRewardLog($user_id, $amount, $reason);

            // Commit transaction
            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Coin System Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deduct coins from user's wallet (for purchases, etc.)
     */
    public function deductCoins($user_id, $amount, $reason = "Purchase")
    {
        try {
            // Check if user has enough coins
            $current_balance = $this->getCoinBalance($user_id);
            if ($current_balance < $amount) {
                throw new Exception("Insufficient coins. Available: $current_balance, Required: $amount");
            }

            // Start transaction
            $this->conn->begin_transaction();

            // Update wallet
            $update_stmt = $this->conn->prepare("UPDATE wallet SET coins = coins - ? WHERE user_id = ?");
            $update_stmt->bind_param("ii", $amount, $user_id);

            if (!$update_stmt->execute()) {
                throw new Exception("Failed to update wallet");
            }

            // Log the transaction (negative amount for deduction)
            $this->addRewardLog($user_id, -$amount, $reason);

            // Commit transaction
            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Coin System Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add reward log entry
     */
    public function addRewardLog($user_id, $coins, $reason)
    {
        try {
            $stmt = $this->conn->prepare("INSERT INTO reward_logs (user_id, coins, reason) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $user_id, $coins, $reason);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Coin System Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user's reward history
     */
    public function getRewardHistory($user_id, $limit = 10)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM reward_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
            $stmt->bind_param("ii", $user_id, $limit);
            $stmt->execute();
            return $stmt->get_result();
        } catch (Exception $e) {
            error_log("Coin System Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate coin usage in shopping cart
     */
    public function validateCoinUsage($user_id, $coins_to_use)
    {
        try {
            $current_balance = $this->getCoinBalance($user_id);

            if ($coins_to_use <= 0) {
                return ["valid" => false, "message" => "Coins must be greater than 0"];
            }

            if ($coins_to_use > $current_balance) {
                return ["valid" => false, "message" => "Insufficient coins. Available: $current_balance"];
            }

            return ["valid" => true, "balance" => $current_balance];

        } catch (Exception $e) {
            error_log("Coin System Error: " . $e->getMessage());
            return ["valid" => false, "message" => "System error occurred"];
        }
    }

    /**
     * Process coin usage in order
     */
    public function processOrderCoins($user_id, $coins_used, $order_id)
    {
        try {
            if ($coins_used > 0) {
                $reason = "Order discount - Order #$order_id";
                return $this->deductCoins($user_id, $coins_used, $reason);
            }
            return true;
        } catch (Exception $e) {
            error_log("Coin System Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add welcome bonus for new users
     */
    public function addWelcomeBonus($user_id)
    {
        return $this->addCoins($user_id, 50, "Welcome bonus");
    }

    /**
     * Add purchase reward (coins for spending money)
     */
    public function addPurchaseReward($user_id, $order_amount)
    {
        // Give 1 coin for every 100 rupees spent
        $coins_earned = floor($order_amount / 100);
        if ($coins_earned > 0) {
            return $this->addCoins($user_id, $coins_earned, "Purchase reward");
        }
        return true;
    }

    /**
     * Add referral bonus
     */
    public function addReferralBonus($referrer_id, $referred_id)
    {
        // Give 25 coins to referrer
        $this->addCoins($referrer_id, 25, "Referral bonus");
        // Give 50 coins to new user
        $this->addCoins($referred_id, 50, "Referral bonus");
    }

    /**
     * Get coin statistics for admin
     */
    public function getCoinStats()
    {
        try {
            $stats = [];

            // Total coins in system
            $total_stmt = $this->conn->query("SELECT SUM(coins) as total_coins FROM wallet");
            $stats['total_coins'] = $total_stmt->fetch_assoc()['total_coins'] ?? 0;

            // Total users with wallets
            $users_stmt = $this->conn->query("SELECT COUNT(*) as total_users FROM wallet");
            $stats['total_users'] = $users_stmt->fetch_assoc()['total_users'] ?? 0;

            // Recent transactions
            $recent_stmt = $this->conn->query("SELECT * FROM reward_logs ORDER BY created_at DESC LIMIT 10");
            $stats['recent_transactions'] = $recent_stmt->fetch_all(MYSQLI_ASSOC);

            return $stats;
        } catch (Exception $e) {
            error_log("Coin System Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check and restore coins from abandoned cart sessions
     */
    public function checkAbandonedCoins($user_id)
    {
        try {
            // Check if there are applied coins in session that are older than 30 minutes
            if (isset($_SESSION['coins_applied']) && $_SESSION['coins_applied'] > 0) {
                $coins_to_restore = $_SESSION['coins_applied'];

                // Add coins back to wallet
                $this->addCoins($user_id, $coins_to_restore, "Restore abandoned cart");

                // Clear session
                unset($_SESSION['coins_applied']);

                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log("Coin System Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user's current coin balance with real-time validation
     */
    public function getRealTimeBalance($user_id)
    {
        try {
            $stmt = $this->conn->prepare("SELECT coins FROM wallet WHERE user_id = ? FOR UPDATE");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                return intval($row['coins']);
            }
            return 0;
        } catch (Exception $e) {
            error_log("Coin System Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Validate coin usage with real-time balance check
     */
  public function validateCoinUsageRealTime($user_id, $coins_to_use)
{
    try {
        // Step 1: Validate input
        if ($coins_to_use <= 0) {
            return [
                "valid" => false,
                "message" => "Coins must be greater than 0"
            ];
        }

        // Step 2: Get real-time wallet balance (excluding previously applied coins)
        $current_balance = $this->getRealTimeBalance($user_id);

        // Step 3: Check if user has sufficient coins
        if ($coins_to_use > $current_balance) {
            return [
                "valid" => false,
                "message" => "Insufficient coins. Available: $current_balance"
            ];
        }

        // Step 4: Deduct coins safely
        $updateStmt = $this->conn->prepare("UPDATE wallet SET coins = coins - ? WHERE user_id = ?");
        $updateStmt->bind_param("ii", $coins_to_use, $user_id);

        if ($updateStmt->execute()) {
            return [
                "valid" => true,
                "balance" => $current_balance - $coins_to_use,
                "message" => "Coins applied successfully"
            ];
        } else {
            return [
                "valid" => false,
                "message" => "Failed to deduct coins from wallet"
            ];
        }

    } catch (Exception $e) {
        error_log("Coin System Error: " . $e->getMessage());
        return [
            "valid" => false,
            "message" => "System error occurred while validating coins"
        ];
    }
}

}

// Initialize coin system
$coinSystem = new CoinSystem($conn);
?>