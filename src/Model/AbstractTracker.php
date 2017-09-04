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

    try {
      $this->conn = DbConnection::createDbConnection();
    } catch(PDOException $e) {
      error_log($e->getMessage());
    }
  }
}
