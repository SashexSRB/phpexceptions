<?php
class User implements Iterator {
    private $id;
    private $username;
    private $email;
    private $attributes = [];
    private $position = 0;
    private $money;

    public function __construct($username, $email, $money) {
        $this->username = $username;
        $this->email = $email;
        $this->money = $money;
    }

    // Iterator interface methods
    public function current(): mixed {
        $keys = array_keys($this->attributes);
        return $this->attributes[$keys[$this->position]];
    }
    
    public function key(): mixed {
        $keys = array_keys($this->attributes);
        return $keys[$this->position];
    }

    public function next(): void {
        $this->position++;
    }

    public function rewind(): void {
        $this->position = 0;
    }

    public function valid(): bool {
        $keys = array_keys($this->attributes);
        return isset($keys[$this->position]);
    }

    // Database operations
    public function saveToDatabase($dbConnection) {
        try {
            $query = "INSERT INTO users (username, email, money) VALUES (?,?,?)";
            $stmt = $dbConnection->prepare($query);
            
            if (!$stmt) {
                throw new DatabaseException("MySQL prepare statement failed: " . $dbConnection->error);
            }

            $stmt->bind_param("ssi", $this->username, $this->email, $this->money);
            
            if (!$stmt->execute()) {
                throw new DatabaseException("MySQL insert failed: " . $stmt->error);
            }

            $this->id = $dbConnection->insert_id;
            $stmt->close();
            return true;

        } catch (DatabaseException $e) {
            error_log("MySQL error: " . $e->getMessage());
            throw $e;
        } catch (Exception $e) {
            error_log("Unexpected error: " . $e->getMessage());
            throw new DatabaseException("Unexpected MySQL error");
        }
    }

    public static function getAllUsers($dbConnection) {
        try {
            $query = "SELECT id, username, email, money FROM users";
            $result = $dbConnection->query($query);

            if (!$result) {
                throw new DatabaseException("MySQL get query failed: " . $dbConnection->error);
            }

            $users = [];
            while ($row = $result->fetch_assoc()) {
                $user = new User($row['username'], $row['email'], $row['money']);
                $user->id = $row['id'];
                $users[] = $user;
            }

            $result->free();
            return $users;

        } catch (DatabaseException $e) {
            error_log("MySQL error: " . $e->getMessage());
            throw $e;
        }
    }

    // API operation (mock)
    public function syncWithAPI() {
        try {
            $apiEndpoint = 'https://api.example.com/users';
            $ch = curl_init($apiEndpoint);
            if ($ch === false) {
                throw new APIException("Failed to initialize cURL");
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'username' => $this->username,
                'email' => $this->email,
                'money' => $this->money
            ]));

            $response = curl_exec($ch);
            
            if ($response === false) {
                throw new APIException("API request failed: " . curl_error($ch));
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode >= 400) {
                throw new APIException("API returned error status: $httpCode");
            }

            return json_decode($response, true);

        } catch (APIException $e) {
            error_log("API error: " . $e->getMessage());
            throw $e;
        }
    }

    // Payment operation (mock)
    public function processPayment($amount) {
        try {
            if ($amount <= 0) {
                throw new PaymentException("Invalid payment amount");
            }

            return [
                'success' => true,
                'transaction_id' => uniqid('txn_'),
                'amount' => $amount
            ];

        } catch (PaymentException $e) {
            error_log("Payment error: " . $e->getMessage());
            throw $e;
        }
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getMoney() {
        return $this->money;
    }
}