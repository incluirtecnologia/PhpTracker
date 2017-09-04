<?php


namespace Intec\Tracker\Model;


abstract class AbstractTracker
{

  protected $conn;

  abstract public function save();

  protected function createConnection()
  {
    $this->conn = DbConnection::createDbConnection();
  }
}
