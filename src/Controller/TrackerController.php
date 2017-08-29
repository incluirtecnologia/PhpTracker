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
      $params['element'], $params['screen'], $params['height'],
      $params['width']);

    $mTracker->save();
  }
}
