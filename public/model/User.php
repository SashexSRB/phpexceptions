<?php
class User implements Iterator {
    private $id;
    private $username;
    private $email;
    private $attributes = [];
    private $position = 0;
    private $money;
    private $password;

    public function __construct($username, $email, $money, $password = '') {
        $this->username = $username;
        $this->email = $email;
        $this->money = $money;
        $this->password = $password;
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
            $query = "INSERT INTO users (username, email, money, password) VALUES (?,?,?,?)";
            $stmt = $dbConnection->prepare($query);
            
            if (!$stmt) {
                throw new DatabaseException("MySQL prepare statement failed: " . $dbConnection->error);
            }

            $stmt->bind_param("ssis", $this->username, $this->email, $this->money, $this->password);
            
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

    public static function getUserForLogin($dbConnection, $email, $inputPassword) {
        try {
            $query = "SELECT id, username, password, money FROM users WHERE email = ?";
            $stmt = $dbConnection->prepare($query);

            if (!$stmt) {
                throw new DatabaseException("Prepare statement failed: " . $dbConnection->error);
            }

            $stmt->bind_param("s", $email);

            if (!$stmt->execute()) {
                throw new DatabaseException("Query execution failed: " . $stmt->error);
            }

            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                return null;
            }

            $row = $result->fetch_assoc();
            $id = $row['id'];
            $username = $row['username'];
            $storedPassword = $row['password'];

            if (hash('sha256',$inputPassword) === $storedPassword) {
                session_regenerate_id(true);
                $_SESSION['accountLoggedIn'] = true;
                $_SESSION['account_name'] = $username;
                $_SESSION['account_id'] = $id;
                return new User($username, $email, $row['money'] ?? 0, $storedPassword);
            } else {
                return null;
            }
        } catch (DatabaseException $e) {
            error_log("Database error: " . $e->getMessage());
            throw $e;
        } finally {
            if (isset($stmt)) {
                $stmt->close();
            }
        }
    }

    public static function deposit($dbConnection, $amount, $uID) {
        try {
            $query = "UPDATE users SET money = money + ? WHERE id = ?";
            $stmt = $dbConnection->prepare($query);

            if (!$stmt) {
                throw new DatabaseException("Prepare statement failed: " . $dbConnection->error);
            }

            $stmt->bind_param("di", $amount, $uID);

            if (!$stmt->execute()) {
                throw new DatabaseException("Query execution failed: " . $stmt->error);
            }

            $stmt->close();
        } catch (UserException $e) {
            error_log("Deposit error: " . $e->getMessage());
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