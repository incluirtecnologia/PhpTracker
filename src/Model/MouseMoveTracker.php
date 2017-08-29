<?php


namespace Intec\Tracker\Model;


use Intec\Session\Session;

class MouseMoveTracker extends AbstractTracker
{

  private $client_info_id;
  private $x;
  private $y;
  private $element;

  public function __construct($xPosition, $yPosition, $element)
  {

    $this->createConnection();
    $se = Session::getInstance();
    $this->client_info_id = $se->get(self::DEFAULT_SESSION_KEY);
    $this->x = $xPosition;
    $this->y = $yPosition;
    $this->element = $element;

  }


  public function save()
  {

    try {

      $stmt = $this->conn->prepare('INSERT INTO mouse_move (client_info_id, x, y, element) values(?, ?, ?)');
      $stmt->execute([
        $this->client_info_id,
        $this->x,
        $this->y,
        $this->element,
      ]);

      return $this->conn->lastInsertId();

    } catch(PDOException $e) {
      error_log($e->getMessage());
      if($this->conn->inTransaction()) {
          $this->conn->rollBack();
      }
    }

    return false;


  }


}
