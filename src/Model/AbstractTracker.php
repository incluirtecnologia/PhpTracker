<?php


namespace Intec\Tracker\Model;


use PDO;
use PDOException;

abstract class AbstractTracker
{

  protected $conn;

  abstract public function save();

  protected function createConnection()
  {
    $this->conn = DbConnection::createDbConnection();
  }
}
