<?php


namespace Intec\Tracker\Model;


use PDO;
use PDOException;

class AbstractTracker
{

  protected $conn;

  abstract function save()
  {

  }

  protected function getConnection()
  {

    try {
      $this->conn = new PDO(
            'mysql:host='. getenv('DB_TRACKER_HOST') .
            ';dbname='. getenv('DB_TRACKER_NAME') .';charset=' .
            getenv('DB_TRACKER_CHARSET'),
            getenv('DB_TRACKER_USER'),
            getenv('DB_TRACKER_PASS'),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT => false,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
      );
    } catch(PDOException $e) {
      error_log($e->getMessage());
    }
  }
}
