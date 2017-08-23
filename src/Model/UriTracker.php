<?php


namespace Intec\Tracker\Model;


class UriTracker
{

  private $uri;

  public function __construct($uri)
  {
    $this->uri = $uri;
  }

  public function save()
  {
    error_log($this->uri);
  }

}
