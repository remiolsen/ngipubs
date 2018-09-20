<?php
class NGIportal {
	public function __construct() {
		global $CONFIG;
		$this->baseuri=rtrim($CONFIG['portal']['host'],"/");
		$this->username=$CONFIG['portal']['user'];
		$this->password=$CONFIG['portal']['pass'];
		$this->cache=$_SERVER['DOCUMENT_ROOT'].rtrim($CONFIG['site']['subdir'],'/').'/cache/orders.json';
	}
	
	public function getOrder($order_id) {
		if(trim($order_id)!='') {
			return $this->getData('/order/'.$order_id);
		} else {
			return FALSE;
		}
	}
	
	public function getOrders() {
		// Cache results, if no cache file exists use https://ngisweden.scilifelab.se/api/v1/orders?recent=false (will fetch all!)
		// Otherwise use https://ngisweden.scilifelab.se/api/v1/orders?recent=true (fetch 500 latest)
		// Build JSON file with order ID as identifier and append only the new ones to the cached file
		
		$current_time=time(); 
		$expire_min=60; // Cached for 1h
		$expire_time=$expire_min*60;
		
		if(file_exists($this->cache)) {
			$file_time=filemtime($this->cache);
			if($current_time-$expire_time<$file_time) {
				$output=json_decode(file_get_contents($this->cache),TRUE);
			} else {
				// Time has expired - generate new cache file (fetch 500 most recent orders)
				if($data=$this->getData('/orders?recent=true')) {
					$output=$this->updateOrderList($data);
					$message=array('message' => 'Data loaded successfully, cached for '.$expire_min.' min', 'type' => 'success');
				} else {
					$output=json_decode(file_get_contents($this->cache),TRUE); // Fetch from file if data source not available
					$message=array('message' => 'Data source unavailable, fetching data from cached results', 'type' => 'warning');
				}
			}
		} else {
			// Cache file does not exist - generate new cache file
			$data=$this->getData('/orders?recent=false');
			$output=$this->updateOrderList($data);
			$message=array('message' => 'Cache file created, cached for '.$expire_min.' min', 'type' => 'success');
		}
		
		return $output;
	}
	
	public function getAccounts() {
		return $this->getData('/accounts');
	}
	
	private function updateOrderList($data) {
		if(file_exists($this->cache)) {
			$output=json_decode(file_get_contents($this->cache));
		} else {
			$output=array();
		}
		
		foreach($data->items as $order) {
			$output[$order->identifier]=$order;
		}
		
		if(file_put_contents($this->cache, json_encode($output))) {
			return $output;
		} else {
			return FALSE;
		}
	}
		
	private function getData($path) {
		$header=array('header' => "Authorization: Basic ".base64_encode($this->username.":".$this->password));
		$uri=$this->baseuri.$path;
		$curl=curl_init();
		curl_setopt($curl,CURLOPT_URL,$uri);
		curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,5);
		curl_setopt($curl,CURLOPT_TIMEOUT,120);
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_HTTPHEADER,$header);
		$json=curl_exec($curl);
		curl_close($curl);
		$data=json_decode($json);
		if(is_object($data)) {
			return $data;
		} else {
			return FALSE;
		}
	}
}