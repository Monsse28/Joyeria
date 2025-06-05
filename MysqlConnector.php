<?php
class MysqlConnector {
    private $server;
    private $connUser;
    private $connPassword;
    private $connDb;
    private $connection;

    function __construct() {
        $this->server = "bdatos";  
        $this->connUser = "root";
        $this->connPassword = "root";
        $this->connDb = "Joyeria";  
    }

    public function connect() {
        $this->connection = new mysqli(
            $this->server,
            $this->connUser,
            $this->connPassword,
            $this->connDb
        );

        if ($this->connection->connect_error) {
            die("Error de conexión: " . $this->connection->connect_error);
        }

        return $this->connection;
    }

    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}
?>
