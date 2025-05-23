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

    public static function transfer($dbConnection, $amount, $sourceID, $dest_uName) {
        try {
            $dbConnection->begin_transaction();

            //subtract from source
            $query1 = "UPDATE users SET money = money - ? WHERE id = ? AND money >= ?";
            $stmt1 = $dbConnection->prepare($query1);

            if (!$stmt1) {
                throw new DatabaseException("Preparing 1st statement failed: " . $dbConnection->error);
            }

            $stmt1->bind_param("did", $amount, $sourceID, $amount);

            if (!$stmt1->execute()) {
                throw new DatabaseException("1st Query execution failed: " . $stmt1->error);
            }

            if ($stmt1->affected_rows === 0) {
                throw new DatabaseException("Insufficient balance.");
            }

            //add to destination
            $query2 = "UPDATE users SET money = money + ? WHERE username = ?";
            $stmt2 = $dbConnection->prepare($query2);
            if (!$stmt2) {
                throw new DatabaseException("Preparing 2nd statement failed: ". $dbConnection->error);
            }

            $stmt2->bind_param("ds", $amount, $dest_uName);

            if (!$stmt2->execute()) {
                throw new DatabaseException("2nd Query execution failed: " . $stmt2->error);
            }

            if ($stmt2->affected_rows === 0) {
                throw new DatabaseException("Destination user not found.");
            }

            $dbConnection->commit();
            $stmt1->close();
            $stmt2->close();
        } catch (UserException $e) {
            $dbConnection->rollback();
            if (isset($stmt1)) $stmt1->close();
            if (isset($stmt2)) $stmt2->close();
            error_log("Transfer error: " . $e->getMessage());
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