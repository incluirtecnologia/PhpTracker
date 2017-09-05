<?php


namespace Intec\Tracker\Model;


class TrackerRouteContainer
{

  private static $routes = [
    [
      'pattern' => '/tracker/mouse',
      'callback' => function($request) {
          TrackerController::mouseMoveTracker($request);
      }
    ],
    [
      'pattern' => '/tracker/getPages',
      'callback' => function($request) {
          TrackerController::getPages($request);
      }
    ],
    [
      'pattern' => '/tracker/getPageVersions',
      'callback' => function($request) {
          TrackerController::getPageVersions($request);
      }
    ],
  ];

  public static function getRoutes()
  {
    return self::$routes;
  }

}
