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

  public static function getPages($serverName, $screenSize)
  {
    $conn = DbConnection::createDbConnection();


    $testScreenSize = "";

    switch($screenSize) {
      case Screen::SIZE_EXTRA_SMALL:
        $testScreenSize = " and m.width < " . Screen::SCREEN_SM_MIN;
        break;
      case Screen::SIZE_SMALL:
        $testScreenSize = " and m.width > " . Screen::SCREEN_XS_MAX .
        " and m.width < " . Screen::SCREEN_MD_MIN;
        break;
      case Screen::SIZE_MEDIUM:
      $testScreenSize = " and m.width > " . Screen::SCREEN_SM_MAX .
      " and m.width < " . Screen::SCREEN_LG_MIN;
        break;
      case Screen::SIZE_LARGE:
      $testScreenSize = " and m.width > " . Screen::SCREEN_MD_MAX;
        break;
      default:
        throw new \InvalidArgumentException('Valor inválido para screenSize');
    }
    $sql = "select m.height, m.width, m.x, m.y, m.pathname from mouse_move m join client_info c
    on c.session_id = m.session_id where c.server_name='$serverName'
    $testScreenSize
    group by m.pathname";
    $stmt = $conn->query($sql);
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
