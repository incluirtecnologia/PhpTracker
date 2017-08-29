<?php


namespace Intec\Tracker\Model;


use Intec\Session\Session;

class MouseMoveTracker extends AbstractTracker
{

  private $client_info_id;
  private $x;
  private $y;
  private $element;
  private $screen;
  private $width;
  private $height;

  public function __construct($xPosition, $yPosition, $element, $screen, $width, $height)
  {

    $this->createConnection();
    $se = Session::getInstance();
    $this->client_info_id = $se->get(ClientTracker::DEFAULT_SESSION_KEY);
    $this->x = $xPosition;
    $this->y = $yPosition;
    $this->element = $element;
    $this->screen = $screen;
    $this->width = $width;
    $this->height = $height;
  }


  public function save()
  {

    try {

      $stmt = $this->conn->prepare('INSERT INTO mouse_move (client_info_id, x, y, element) values(?, ?, ?, ?, ?, ?, ?)');
      $stmt->execute([
        $this->client_info_id,
        $this->x,
        $this->y,
        $this->element,
        $this->screen,
        $this->width,
        $this->height
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
