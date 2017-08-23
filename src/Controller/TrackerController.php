<?php


namespace Intec\Tracker\Controller;


use Intec\Tracker\Model\DummyTracker;


class TracerController
{
  public static function dummyTracker()
  {
    $dTracker = new DummyTracker();
    $dTracker->log('Hello from TrackerController!');
  }
}
