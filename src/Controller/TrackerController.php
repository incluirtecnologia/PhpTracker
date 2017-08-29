<?php


namespace Intec\Tracker\Controller;


use Intec\Tracker\Model\DummyTracker;
use Intec\Router\Request;
use Intec\Session\Session;

class TracerController
{
  public static function dummyTracker()
  {
    $dTracker = new DummyTracker();
    $dTracker->log('Hello from TrackerController!');
  }

  public static function mouseMoveTracker(Request $request)
  {
    $params = $request->getPostParams();
    // if() {
    //
    // }
    $mTracker = new MouseMoveTracker(  $params['x'], $params['y'], $params['element']);
    $mTracker->save();
  }
}
