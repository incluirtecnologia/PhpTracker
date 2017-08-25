<?php


namespace Intec\Tracker\Middleware;


use Intec\Tracker\Model\DummyTracker;
use Intec\Tracker\Model\UriTracker;
use Intec\Router\Request;

use Intec\Tracker\Model\ClientTracker;

class TrackerMiddleware
{

  const DB_HANDLER = 'TRACKER_HANDLER';

  /**
  * Used in routes to get information
  **/
  public function __construct()
  {

  }

  public static function dummyTracker(Request $request)
  {
    $dTracker = new DummyTracker();
    $dTracker->log('Hello from TrackerMiddleware!');
  }

  public static function userTracker($request, $sessionValues = [])
  {

    $clientTracker = new ClientTracker();
    $clientTracker->save();

    // post params
    // $postTracker = self::postTracker($request->getPostParams());
    //
    // session session
    // $sessionTracker = self::sessionTracker($sessionValues);
    //
    // client info: ip, user agent, ...
    // $clientTracker = self::clientTracker();
  }
}
