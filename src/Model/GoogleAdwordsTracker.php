<?php


namespace Intec\Tracker\Model;


class GoogleAdwordsTracker extends AbstractTracker
{

  private $client_info_id;
	private $campaignid;
	private $adgroupid;
	private $feeditemid;
	private $targetid;
	private $loc_physical_ms;
	private $matchtype;
	private $network;
	private $device;
	private $devicemodel;
	private $keyword;
	private $placement;
	private $adposition;

  public function __construct($id, $params = [])
  {
    $this->createConnection();
    $this->client_info_id = $id;
    if(array_key_exists('campaignid', $params)) {
      	$this->campaignid = $params['campaignid'];
      	$this->adgroupid = $params['adgroupid'];
      	$this->feeditemid = $params['feeditemid'];
      	$this->targetid = $params['targetid'];
      	$this->loc_physical_ms = $params['loc_physical_ms'];
      	$this->matchtype = $params['matchtype'];
      	$this->network = $params['network'];
      	$this->device = $params['device'];
      	$this->devicemodel = $params['devicemodel'];
      	$this->keyword = $params['keyword'];
      	$this->placement = $params['placement'];
      	$this->adposition = $params['adposition'];
    }
  }

  public function hasParams()
  {
    if($this->campaignid) {
      return true;
    }
  }

  public function save() {

    if(!$this->hasParams()) {
      return false;
    }

    try {

      $stmt = $this->conn->prepare('INSERT INTO adwords
        (client_info_id, campaignid, adgroupid, feeditemid, targetid,
          loc_physical_ms, matchtype, network, device, devicemodel, keyword,
          placement, adposition)
        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)');
      $stmt->execute([
        $this->client_info_id,
      	$this->campaignid,
      	$this->adgroupid,
      	$this->feeditemid,
      	$this->targetid,
      	$this->loc_physical_ms,
      	$this->matchtype,
      	$this->network,
      	$this->device,
      	$this->devicemodel,
      	$this->keyword,
      	$this->placement,
      	$this->adposition
      ]);

      return $this->conn->lastInsertId();

    } catch(PDOException $e) {
      error_log($e->getMessage());
      if($this->conn->inTransaction()) {
          $this->conn->rollBack();
      }
    }

    return false;
  }


}
