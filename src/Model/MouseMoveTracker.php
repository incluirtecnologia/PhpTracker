<?php


namespace Intec\Tracker\Model;


class MouseMoveTracker extends AbstractTracker
{

  private $client_info_id;
  private $x;
  private $y;
  private $element;

  public function __construct($id, $xPosition, $yPosition, $element)
  {

    $this->createConnection();
    $this->client_info_id = $id;
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
