<?php 

namespace RateLimit;

define('RATE_IN_RANGE', "-5 minutes");

class Track {
    protected $rateData = [];

    protected $path = '../tmp/rate_limit/rate.limit';

    protected $accessPerMinutes = 1;

    public function __construct() {
        if(!file_exists($this->path)) {
            $this->updateStorage();
        }
        $this->updateLocalRate();
    }

    public function track() {
        if($this->is_ip_tracked()) {
            # Not first session
            return $this->moveOn();
        } else {
            # First session
            $this->create_access();
            return $this->moveOn();
        }
    }

    public function moveOn() {
        $status = false;
        $already = false;
        for($i = 0; $i <= sizeof($this->rateData) - 1;$i++) {
            $user = $this->rateData[$i];
            if($user->ip == $this->_ip()) {

                if($user->last_access < strtotime(RATE_IN_RANGE)) {
                    // User unbanned
                    $this->rateData[$i]->access = [];
                    $this->rateData[$i]->access[] = time();
                    $this->rateData[$i]->last_access = time();
                    $status = true;
                    $this->updateStorage();
                    $this->updateLocalRate();
                }

                if(sizeof($user->access) <= $this->accessPerMinutes - 1 && !$status) {
                    $this->rateData[$i]->access[] = time();
                    $this->rateData[$i]->last_access = time();
                    $status = true;
                    $this->updateStorage();
                    $this->updateLocalRate();
                }
                return $status;
            }
        }
    }

    public function set_access_pre_minutes($access_count) {
        $this->accessPerMinutes = $access_count;
    }

    public function create_access() {
        if(!$this->is_ip_tracked()) {
            $this->rateData[] = [
                "ip" => $this->_ip(),
                'access' => [],
                'last_access' => null
            ];
            $this->updateStorage();
            $this->updateLocalRate();
        }
    }

    public function is_ip_tracked() {
        for($i = 0; $i <= sizeof($this->rateData) - 1; $i++) {
            $user = $this->rateData[$i];
            if($user->ip == $this->_ip()) {
                return true;
            }
        }
    }

    public function _ip() {
        return $_SERVER['REMOTE_ADDR'];
    }

    private function updateLocalRate() {
        $this->rateData = json_decode(file_get_contents($this->path));
    }

    private function updateStorage() {
        $fp = fopen($this->path, "wb");
        fwrite($fp, json_encode($this->rateData));
        fclose($fp);
    }
 
}
