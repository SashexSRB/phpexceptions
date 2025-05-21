<?php
class User implements Iterator {
    private $id;
    private $username;
    private $email;
    private $attributes = [];
    private $position = 0;
    private $money;
    private $password;

    public function __construct($username, $email, $money, $password) {
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

    public function checkHash($dbPassword, $inputPassword){
        try {
            return $dbPassword === $inputPassword; 
        } catch (UserException $e) {
            error_log("UserAuth Error: " . $e->getMessage());
            return false;
        }
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
                $user = new User($row['username'], $row['email'], $row['money'], $row['password']);
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

    public static function getUser($dbConnection, $email, $inputPassword) {
        try {
            $query = "SELECT id, username, email, money, password FROM users WHERE email = ?";
            $stmt = $dbConnection->prepare($query);
            
            if (!$stmt) {
                throw new DatabaseException("MySQL prepare statement failed: " . $dbConnection->error);
            }

            $stmt->bind_param("s", $email);
            
            if (!$stmt->execute()) {
                throw new DatabaseException("MySQL insert failed: " . $stmt->error);
            }

            $result = $stmt->get_result();

            if (!$result) {
                throw new DatabaseException("MySQL get query failed: " . $dbConnection->error);
            }

            if ($row = $result->fetch_assoc()) {
                $user = new User($row['username'], $row['email'], $row['money'], $row['password']);
                $user->id = $row['id'];

                if (!$user->checkHash($user->password, $inputPassword)) {
                    throw new UserException(('Password incorrect'. $dbConnection->error));
                }

                return $user;
            } 
            return null;
        } catch (DatabaseException $e) {
            error_log("MySQL error: " . $e->getMessage());
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