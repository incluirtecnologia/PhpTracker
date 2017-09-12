<?php


namespace Intec\Tracker\Controller;


use Intec\Tracker\Model\DummyTracker;
use Intec\Tracker\Model\MouseMoveTracker;
use Intec\Router\Request;
use Intec\Session\Session;

class TrackerController
{
  public static function dummyTracker()
  {
    $dTracker = new DummyTracker();
    $dTracker->log('Hello from TrackerController!');
  }

  public static function mouseMoveTracker(Request $request)
  {
    $params = $request->getPostParams();

    $mTracker = new MouseMoveTracker($params['x'], $params['y'],
      $params['element'], $params['screen'], $params['width'],
      $params['height'], $params['pathname'], $params['contentId']);

    $mTracker->save();
  }

  public static function getPages(Request $request)
  {
    $params = $request->getQueryParams();
    $pages = MouseMoveTracker::getPages($params['serverName'], $params['screenSize']);
    echo json_encode($pages);
  }

  public static function getPageVersions(Request $request)
  {
    $pages = MouseMoveTracker::getPageVersions($request->getQueryParams()['selectedPage']);
    echo json_encode($pages);
  }

  public static function getMouseMoveData(Request $req)
  {
    $params = $req->getQueryParams();
    $data = [];
    $ver = $params['pageVersion'];
    if($params['endDate']) {
      $data = MouseMoveTracker::getMouseMoveData($ver, $params['startDate'], $params['endDate']);
    } else {
      $data = MouseMoveTracker::getMouseMoveData($ver, $params['startDate']);
    }

    echo json_encode($data);
  }

}
