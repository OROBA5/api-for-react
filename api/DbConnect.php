<?php

class DbConnect {
    private $server = 'localhost';
    private $dbname = 'todolist';
    private $user = 'root';
    private $pass = '';
    private $conn;

    public function connect() {
        try {
            $this->conn = new PDO('mysql:host=' . $this->server . ';dbname=' . $this->dbname, $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (\Exception $e) {
            throw new \Exception("Database Error: " . $e->getMessage());
        }
    }
    
}
?>


<?php



?>
