<?php


namespace Intec\Tracker\Model;

use Intec\Session\Session;

class ClientTracker extends AbstractTracker
{
    private $ip;
    private $serverName;
    private $serverPort;
    private $serverRequestUri;
    private $serverSoftware;
    private $requestMethod;
    private $requestParams;
    private $httpCookie;
    private $userAgent;
    private $remoteAddr;
    private $remotePort;
    private $trackerSession;
    private $sessionValues;
    private $uploadedFiles;

    const DEFAULT_SESSION_KEY = 'tracker_session';

    const ERR_COPY_FILE = -1;

    const PATH_SAVED_FILES = "vendor/intec/tracker/data/saved-files";

    public function __construct()
    {
        $se = Session::getInstance();
        if (!$se->exists(self::DEFAULT_SESSION_KEY)) {
            $se->set(self::DEFAULT_SESSION_KEY, uniqid());
        }

        $this->createConnection();
        $this->session_id = $se->get(self::DEFAULT_SESSION_KEY);
        $this->ip = $this->getIpAddress();
        $this->sessionValues = (string)$se;
        $this->serverName = filter_input(INPUT_SERVER, 'SERVER_NAME');
        $this->serverPort = filter_input(INPUT_SERVER, 'SERVER_PORT');
        $this->serverRequestUri = filter_input(INPUT_SERVER, 'REQUEST_URI');
        $this->serverSoftware = filter_input(INPUT_SERVER, 'SERVER_SOFTWARE');
        $this->requestMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        $this->httpCookie = filter_input(INPUT_SERVER, 'HTTP_COOKIE');
        $this->userAgent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT');
        $this->remoteAddr = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
        $this->remotePort = filter_input(INPUT_SERVER, 'REMOTE_PORT');
    }

    public function getIpAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[count($ips) - 1]);
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    public function setRequestParams($requestParams)
    {
        $strRequestParams = implode(', ', $requestParams);
        if (!empty($strRequestParams)) {
            $this->requestParams = $strRequestParams;
        }
    }

    public function copyFile($name, $tmpName, $pathTo)
    {
        if (!file_exists($pathTo)) {
            die("Directory not found");
        }

        $pathDocument = $pathTo . "/". $name;

        if (copy($tmpName, $pathDocument)) {
            return $name;
        }

        return self::ERR_COPY_FILE;
    }

    public function setFiles($filesParams)
    {
        $files = array();
        $keys = array_keys($filesParams);
        $hash = hash('sha256', microtime(true));
        foreach ($keys as $key) {
            if ($filesParams[$key]['error'] == 0) {
                $type = strtolower(explode("/", $filesParams[$key]['type'])[1]);
                $fileName = $key . '_' . $hash . '.' . $type;
                $cpyFile = self::copyFile($fileName, $filesParams[$key]['tmp_name'], self::PATH_SAVED_FILES);
                if ($cpyFile != self::ERR_COPY_FILE) {
                    array_push($files, $fileName);
                }
            }
        }
        if (count($files) > 0) {
            $this->uploadedFiles = implode(', ', $files);
        }
    }

    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    public function save()
    {
        try {
            $stmt = $this->conn->prepare('INSERT INTO client_info
                (session_id, ip, server_name, server_port, server_request_uri, server_software,
                request_method, request_params, http_cookie, user_agent, remote_addr, remote_port,
                session_values, uploaded_files) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                $this->session_id,
                $this->ip,
                $this->serverName,
                $this->serverPort,
                $this->serverRequestUri,
                $this->serverSoftware,
                $this->requestMethod,
                $this->requestParams,
                $this->httpCookie,
                $this->userAgent,
                $this->remoteAddr,
                $this->remotePort,
                $this->sessionValues,
                $this->uploadedFiles,
            ]);

            return $this->session_id;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
        }

        return false;
    }

    public static function getAllDistinctSessions()
    {
        $conn = DbConnection::createDbConnection();
        $stmt = $conn->query("select distinct session_id, remote_addr from client_info");
        if ($stmt && $sessions = $stmt->fetchAll()) {
            return $sessions;
        }

        return false;
    }
}
