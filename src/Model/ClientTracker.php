<?php


namespace Intec\Tracker\Model;


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
  private $requestTime;

  public function __construct()
  {
    $this->createConnection();
    $this->ip = $this->getIpAddress();
    $this->serverName = filter_input(INPUT_SERVER, 'SERVER_NAME');
    $this->serverPort = filter_input(INPUT_SERVER, 'SERVER_PORT');
    $this->serverRequestUri = filter_input(INPUT_SERVER, 'REQUEST_URI');
    $this->serverSoftware = filter_input(INPUT_SERVER, 'SERVER_SOFTWARE');
    $this->requestMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
    $this->httpCookie = filter_input(INPUT_SERVER, 'HTTP_COOKIE');
    $this->userAgent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT');
    $this->remoteAddr = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
    $this->remotePort = filter_input(INPUT_SERVER, 'REMOTE_PORT');
    $this->requestTime = filter_input(INPUT_SERVER, 'REQUEST_TIME');
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
      $stmt = $this->conn->prepare('INSERT INTO client_info VALUES(null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
      $stmt->execute([
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
        $this->requestTime,
      ]);
    } catch(PDOException $e) {
      error_log($e->getMessage());
      if($this->conn->inTransaction()) {
          $this->conn->rollBack();
      }
    }
  }

}
