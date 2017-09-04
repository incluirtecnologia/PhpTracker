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
  private $pathname;

  public function __construct($xPosition, $yPosition, $element, $screen, $width, $height, $pathname)
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
    $this->pathname = $pathname;
  }


  public function save()
  {

    try {

      $stmt = $this->conn->prepare('INSERT INTO mouse_move (session_id, x,
          y, element, screen, width, height, pathname) values(?, ?, ?, ?, ?, ?, ?, ?)');
      $stmt->execute([
        $this->client_info_id,
        $this->x,
        $this->y,
        $this->element,
        $this->screen,
        $this->width,
        $this->height,
        $this->pathname
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
