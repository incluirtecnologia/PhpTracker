<?php


namespace Intec\Tracker\Middleware;

use Intec\Tracker\Model\DummyTracker;
use Intec\Tracker\Model\UriTracker;
use Intec\Router\Request;

use Intec\Tracker\Model\ClientTracker;
use Intec\Tracker\Model\GoogleAdwordsTracker;

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

    public static function userTracker(Request $request)
    {
        $clientTracker = new ClientTracker();
        // Obtendo os parametros do metodo de requisicao
        $requestMethod = $clientTracker->getRequestMethod();
        if ($requestMethod == 'GET') {
            $clientTracker->setRequestParams($request->getQueryParams());
        } else { // POST
            $clientTracker->setRequestParams($request->getPostParams());
        }
        $clientTracker->setFiles($request->getFilesParams());
        $id = $clientTracker->save();
        if ($id) {
            $adwordsTracker = new GoogleAdwordsTracker($id, $request->getQueryParams());
            $adwordsTracker->save();
        }
    }
}
