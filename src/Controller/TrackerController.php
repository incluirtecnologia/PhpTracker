<?php


namespace Intec\Tracker\Controller;

use IntecPhp\Model\ResponseHandler;
use IntecPhp\Validator\InputValidator;
use Intec\Tracker\Model\DummyTracker;
use Intec\Tracker\Model\MouseMoveTracker;
use Intec\Tracker\Model\ClientTracker;
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

        $mTracker = new MouseMoveTracker(
            $params['x'],
            $params['y'],
            $params['element'],
            $params['screen'],
            $params['width'],
            $params['height'],
            $params['pathname'],
            $params['contentId']
        );

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
        if (isset($params['endDate'])) {
            $data = MouseMoveTracker::getMouseMoveData($params['serverName'], $params['pageVersion'], $params['screenSize'], $params['startDate'], $params['endDate']);
        } else {
            $data = MouseMoveTracker::getMouseMoveData($params['serverName'], $params['pageVersion'], $params['screenSize'], $params['startDate']);
        }

        echo json_encode($data);
    }

    public static function getAllDistinctSessions(Request $request)
    {
        try {
            $data = ClientTracker::getAllDistinctSessions();
            if ($data) {
                $rp = new ResponseHandler(200, 'operação ok', [
                    'sessions' => $data
                ]);
            } else {
                $rp = new ResponseHandler(200, 'operação ok', [
                    'sessions' => null
                ]);
            }
            $rp->printJson();
        } catch (\Exception $e) {
            $rp = new ResponseHandler(400, $e->getMessage() . '. Código: ' . $e->getCode());
            $rp->printJson();
        }
    }

    public static function filterData(Request $request)
    {
        $params = $request->getPostParams();

        $iv = new InputValidator([
            'value' => [
                'validators' => [
                    'IsEmptyValidator' => []
                ]
            ],
            'column' => [
                'validators' => [
                    'IsEmptyValidator' => [],
                    'InArrayValidator' => [
                        "allowedValues" => ['session_id', 'remote_addr']
                    ]
                ]
            ]
        ]);

        $iv->setData($params);

        if (!$iv->isValid()) {
            $errors = $iv->getErrorsMessages();
            $rp = new ResponseHandler(400, $iv->getGeneralErrorMessage(), $errors);
            return $rp->printJson();
        }

        try {
            $data = ClientTracker::filterData($params['column'], $params['value']);
            if ($data) {
                // Separacao de alguns dados e formatacao do campo data
                for ($i = 0; $i < count($data); $i++) {
                    $data[$i]['formatted_date'] = self::formatTimestamp($data[$i]['reg_date']);
                    $string = $data[$i]['session_values'];
                    if ($user_id = self::filterStringParam($string, 'id')) {
                        $data[$i]['user_id'] = $user_id;
                        $data[$i]['user_name'] = self::filterStringParam($string, 'name');
                        $data[$i]['user_type'] = self::filterStringParam($string, 'type');
                    }
                }
                $rp = new ResponseHandler(200, 'operação ok', [
                    'filteredData' => $data
                ]);
            } else {
                $rp = new ResponseHandler(200, 'operação ok', [
                    'filteredData' => null
                ]);
            }
            $rp->printJson();
        } catch (\Exception $e) {
            $rp = new ResponseHandler(400, $e->getMessage() . '. Código: ' . $e->getCode());
            $rp->printJson();
        }
    }

    // Filtra parametros de strings do tipo N1=V1&N2=V2...Nx=Vx
    private function filterStringParam($string, $sParam)
    {
        $arrParams = explode("&", $string);
        foreach ($arrParams as $param) {
            $keyAndValue = explode("=", $param);
            if ((count($keyAndValue) == 2) && ($keyAndValue[0] == $sParam)) {
                return $keyAndValue[1];
            }
        }

        return false;
    }

    private function formatTimestamp($timestamp)
    {
        return date('d/m/Y H:i:s', strtotime($timestamp));
    }

    public static function getTrackedUserById(Request $request)
    {
        $params = $request->getPostParams();

        $iv = new InputValidator([
            'id' => [
                'validators' => [
                    'IsEmptyValidator' => []
                ]
            ]
        ]);

        $iv->setData($params);

        if (!$iv->isValid()) {
            $errors = $iv->getErrorsMessages();
            $rp = new ResponseHandler(400, $iv->getGeneralErrorMessage(), $errors);
            return $rp->printJson();
        }

        try {
            $data = ClientTracker::getTrackedUserById($params['id']);
            if ($data) {
                $header = self::generateHeaderTrackedUser($data[0]['session_values']);

                for ($i = 0; $i < count($data); $i++) {
                    $data[$i]['formatted_date'] = self::formatTimestamp($data[$i]['reg_date']);
                }
                $rp = new ResponseHandler(200, 'operação ok', [
                    'trackedUser' => $data,
                    'trackedHeader' => $header
                ]);
            } else {
                $rp = new ResponseHandler(200, 'operação ok', [
                    'trackedUser' => null
                ]);
            }
            $rp->printJson();
        } catch (\Exception $e) {
            $rp = new ResponseHandler(400, $e->getMessage() . '. Código: ' . $e->getCode());
            $rp->printJson();
        }
    }

    private function generateHeaderTrackedUser($session_values)
    {
        $header = [];
        if ($user_id = self::filterStringParam($session_values, 'id')) {
            $header['user_id'] = $user_id;
            $header['user_name'] = self::filterStringParam($session_values, 'name');
            $header['user_type'] = self::filterStringParam($session_values, 'type');
        }

        return $header;
    }
}
