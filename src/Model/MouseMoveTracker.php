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

  public function __construct($xPosition, $yPosition, $element, $screen, $width, $height, $pathname, $contentId)
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
    $this->contentId = $contentId;
  }

  public function save()
  {

    try {

      $stmt = $this->conn->prepare('INSERT INTO mouse_move (session_id, x,
          y, element, screen, width, height, pathname, contentId) values(?, ?, ?, ?, ?, ?, ?, ?, ?)');
      $stmt->execute([
        $this->client_info_id,
        $this->x,
        $this->y,
        $this->element,
        $this->screen,
        $this->width,
        $this->height,
        $this->pathname,
        $this->contentId
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

  public static function getPages($serverName)
  {
    $conn = DbConnection::createDbConnection();
    $stmt = $conn->query("select m.* from mouse_move m join client_info c
    on c.session_id = m.session_id where c.server_name='$serverName'
    group by m.pathname");
    return $stmt->fetchAll();
  }

  public static function getPageVersions($page)
  {
    $conn = DbConnection::createDbConnection();
    $stmt = $conn->query("select m.* from mouse_move m where m.pathname='$page'
    group by m.contentId");
    return $stmt->fetchAll();
  }

}
