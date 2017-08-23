<?php


namespace Intec\Tracker\Model;


 class DummyTracker
 {

   public function log($message) {
     error_log($message);
   }

 }
