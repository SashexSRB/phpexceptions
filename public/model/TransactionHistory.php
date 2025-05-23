<?php
class TransactionHistory {
    private $id;
    private $uid;
    private $source;
    private $destination;
    private $amount;
    private $tstamp;

    public function __construct($source, $destination, $amount, $tstamp, $uid='') {
        $this->source = $source;
        $this->destination = $destination;
        $this->amount = $amount;
        $this->tstamp = $tstamp;
        $this->uid = $uid;
    }

    public static function getTransactions($dbConnection, $uID) {
        try {
            $query = "SELECT source, destination, amount, tstamp FROM ts_hist WHERE id = ?";
            $stmt = $dbConnection->prepare($query);

            if (!$stmt) {
                throw new DatabaseException("Prepare statement failed: " . $dbConnection->error);
            }

            $stmt->bind_param("i", $uID);
            
            if (!$stmt->execute()) {
                throw new DatabaseException("Query execution failed: " . $stmt->error);
            }

            $result = $stmt->get_result();

            if (!$result) {
                throw new DatabaseException("MySQL get query failed: " . $dbConnection->error);
            }

            $transactions = [];
            while ($row = $result->fetch_assoc()) {
                $transaction = new TransactionHistory($row['source'], $row['destination'], $row['amount'], $row['tstamp']);
                $transactions[] = $transaction;
            }

            $result->free();
            return $transactions;

        } catch (DatabaseException $e) {
            error_log("MySQL error: " . $e->getMessage());
            throw $e;
        }
    }

    public function addTransaction($dbConnection) {
        try {
            $query = "INSERT INTO ts_hist (source, destination, amount, tstamp, uid) VALUES (?,?,?, CURRENT_TIMESTAMP ,?)";
            $stmt = $dbConnection->prepare($query);

            if (!$stmt) {
                throw new DatabaseException("Prepare statement failed: " . $dbConnection->error);
            }

            $stmt->bind_param("ssii", $this->source, $this->destination, $this->amount, $this->uid);

            if (!$stmt->execute()) {
                throw new DatabaseException("MySQL insert failed: " . $stmt->error);
            }

            $this->id = $dbConnection->insert_id;
        } catch (Throwable $e) {
            throw new DatabaseException("Adding transaction failed: " . $e->getMessage());
        }
    }

    public function getSource() {
        return $this->source;
    }

    public function getDestination() {
        return $this->destination;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function getTimestamp() {
        return $this->tstamp;
    }
}