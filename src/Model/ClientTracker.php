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
  private $httpCookie;
  private $userAgent;
  private $remoteAddr;
  private $remotePort;
  private $trackerSession;
  private $sessionValues;

  const DEFAULT_SESSION_KEY = 'tracker_session';

  public function __construct()
  {
    $se = Session::getInstance();
    if(!$se->exists(self::DEFAULT_SESSION_KEY)) {
      $se->set(self::DEFAULT_SESSION_KEY, uniqid());
    }

    $this->createConnection();
    $this->id = $se->get(self::DEFAULT_SESSION_KEY);
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

  public function getIpAddress() {

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[count($ips) - 1]);
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
  }

  public function save()
  {
    try {
      $stmt = $this->conn->prepare('INSERT INTO client_info
    (id, ip, server_name, server_port, server_request_uri, server_software,
    request_method, http_cookie, user_agent, remote_addr, remote_port,
     session_values) VALUES(?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?)');
      $stmt->execute([
        $this->id,
        $this->ip,
        $this->serverName,
        $this->serverPort,
        $this->serverRequestUri,
        $this->serverSoftware,
        $this->requestMethod,
        $this->httpCookie,
        $this->userAgent,
        $this->remoteAddr,
        $this->remotePort,
        $this->sessionValues,
      ]);

      return $this->id;

    } catch(PDOException $e) {
      error_log($e->getMessage());
      if($this->conn->inTransaction()) {
          $this->conn->rollBack();
      }
    }

    return false;
  }

}
