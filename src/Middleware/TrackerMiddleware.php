<?php


namespace Intec\Tracker\Middleware;


use Intec\Tracker\Model\DummyTracker;
use Intec\Tracker\Model\UriTracker;
use Intec\Router\Request;

class TrackerMiddleware
{
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

    // uri params
    $uriTracker = self::uriTracker();
    $uriTracker->save();

    // get params
    // $getTracker = self::getTracker($request->getQueryParams());
    //
    // // post params
    // $postTracker = self::postTracker($request->getPostParams());
    //
    // // session session
    // $sessionTracker = self::sessionTracker($sessionValues);
    //
    // // client info: ip, user agent, ...
    // $clientTracker = self::clientTracker();
  }

  private static function uriTracker()
  {
    return new UriTracker($_SERVER['REQUEST_URI']);
  }

  // private static function getTracker($get)
  // {
  //   return new GetTracker($post);
  // }
  //
  // private static function postTracker($post)
  // {
  //   return new PostTracker($post);
  // }
  //
  // private static function sessionTracker($sessionValues)
  // {
  //   return new SessionTracker($sessionValues);
  // }

  /**
  * Other Trackers
  * ...
  **/
}
