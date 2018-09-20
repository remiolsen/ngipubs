<?php
/* -----------------------------------------------------------------

Dependencies: class.couch.php, class.clarity.php

config/site
config/couch
config/clarity

----------------------------------------------------------------- */

class NGIprojects {
	public function __construct() {
		global $CONFIG;
		$message=FALSE;
		
		$cache=$_SERVER['DOCUMENT_ROOT'].rtrim($CONFIG['site']['subdir'],'/').'/cache/projects.json';
		$current_time=time(); 
		$expire_min=60; // Cached for 1h
		$expire_time=$expire_min*60;
		
		if(file_exists($cache)) {
			$file_time=filemtime($cache);
			if($current_time-$expire_time<$file_time) {
				$data=file_get_contents($cache);
			} else {
				// Time has expired - generate new cache file
				if($data=$this->getProjects($cache)) {
					$message=array('message' => 'Data loaded successfully, cached for '.$expire_min.' min', 'type' => 'success');
				} else {
					$data=file_get_contents($cache); // Fetch from file if data source not available
					$message=array('message' => 'Data source unavailable, fetching data from cached results', 'type' => 'warning');
				}
			}
		} else {
			// Cache file does not exist - generate new cache file
			$data=$this->getProjects($cache);
			$message=array('message' => 'Data loaded successfully, cached for '.$expire_min.' min', 'type' => 'success');
		}

		$result=json_decode($data,TRUE);
		$this->data=$result['data'];
		$this->meta=$result['meta'];
		$this->message=$message;
	}
	
	// Filter out date range from sorted projects
	// from = ISO date
	// to = ISO date
	// filter_date = ID of date to filter
	// exclude_aborted = TRUE|FALSE
	// Select date range for specified date type
	public function selectDateRange($projects,$filter_date,$from,$to,$exclude_aborted=TRUE) {
		foreach($projects as $key => $project) {
			if($this->is_date($project[$filter_date])) {
				if($project[$filter_date]>=$from && $project[$filter_date]<=$to) {
					if(($this->is_date($project['date_aborted']) && !$exclude_aborted) || !$this->is_date($project['date_aborted'])) {
						if($project['type']==$type || !$type) {
							$selected[$key]=$project;
						}
					}
				}
			}
		}
		return $selected;
	}
	
	// Filter project by one or more field values
	// $filter = array('type' => 'Production') 
	public function filterProjects($projects,$filter) {
		if(is_array($projects)) {
			if(is_array($filter)) {
				foreach($filter as $key => $value) {
					$projects=array_filter($projects,function($var) use ($key,$value) {
						return ($var[$key] == $value);
					});	
				}
				return $projects;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	
	// Outputs an array with projects sorted according to year and either month or quarter
	public function sortDates($projects,$datefield='date_queue',$output='month') {
		foreach($projects as $limsid => $project) {
			if($timestamp=strtotime($project[$datefield])) {
				$month=date('n',$timestamp);
				$quarter=ceil($month/3);
				$year=date("Y",$timestamp);
				switch($output) {
					default:
					case 'month':
						$list[$year][$month][$limsid]=$project;
					break;
					
					case 'quarter':
						$list[$year][$quarter][$limsid]=$project;
					break;
					
					case 'year':
						$list[$year][$limsid]=$project;
					break;
				}
			} else {
				$errors[$project['lims_id']]="Missing value for $datefield";
			}
		}
		ksort($list);
		foreach($list as $year => $period) {
			ksort($list[$year]);
		}

		return array('data' => $list, 'error' => $errors);
	}
	
	public function sortApplication($projects,$set=FALSE) {
		foreach($projects as $limsid => $project) {
			if(is_array($set)) {
				if(in_array($project['application'],$set)) {
					$list[$project['application']][$limsid]=$project;
				}
			} else {
				$list[$project['application']][$limsid]=$project;
			}
		}
		return $list;
	}

	public function sortField($projects,$field,$set=FALSE) {
		foreach($projects as $limsid => $project) {
			if(is_array($set)) {
				if(in_array($project[$field],$set)) {
					$list[$project[$field]][$limsid]=$project;
				}
			} else {
				$list[$project[$field]][$limsid]=$project;
			}
		}
		return $list;
	}
	
	// Return the common name of the species in the reference genome field for both old and new format
	public function normalizeRefGenome($reference_genome) {
		$species_list_old=array(
			"hg19"		=>	"Human", 
			"mm9"		=>	"Mouse", 
			"rn4"		=>	"Rat", 
			"sacCer2"	=>	"Budding yeast", 
			"dm3"		=>	"Fruit fly", 
			"TAIR9"		=>	"Thale cress", 
			"xenTro2"	=>	"Frog", 
			"WS210"		=>	"Nematode", 
			"canFam3"	=>	"Dog", 
			"multiple"	=>	"Multiple", 
			"Zv8"		=> 	"Zebrafish", 
			"other"		=>	"Other"
		);
		
		preg_match("/^(.+) \(.+, .+\)/",$reference_genome,$matches); // New ref genome format
		if($matches[1]!="") {
			return $matches[1];
		} else {
			// Old format
			if(array_key_exists($reference_genome,$species_list_old)) {
				return $species_list_old[$reference_genome];
			} else {
				return FALSE;
			}
		}
	}

	public function sortProjectStates($projects) {
		foreach($projects as $limsid => $project) {
			if($this->is_date($project['date_close'])) {
				if($this->is_date($project['date_aborted'])) {
					$list['aborted'][$limsid]=$project;
				} else {
					$list['closed'][$limsid]=$project;
				}
			} else {
				if($this->is_date($project['date_queue'])) {
					$list['ongoing'][$limsid]=$project;
				} elseif($this->is_date($project['date_open'])) {
					$list['reception_control'][$limsid]=$project;
				} else {
					$list['pending'][$limsid]=$project;
				}
			}
		}
		return $list;
	}
	
	// Added extra category: Library_prep (both DNA and RNA)
	public function sortProjectPrepType($projects) {
		$types=array('DNA','RNA','Library');
		$libprep=array('DNA','RNA');
		foreach($projects as $limsid => $project) {
			$prep=$this->parseLibprep($project['lib_prep']);
			if(in_array($prep['input'],$types)) {
				if(in_array($prep['input'],$libprep)) {
					$list['Library_prep'][$limsid]=$project;
				}
				$list[$prep['input']][$limsid]=$project;
			} else {
				$errors[$limsid]="Unknown prep input for project $limsid: ".$prep['input'];
			}
		}
		return array('data' => $list, 'error' => $errors);
	}
	
	// Takes a list of times (in days) and outputs a list of binned values according to $categories
	public function binTimes($data,$categories=FALSE) {
		if(!is_array($categories)) {
			// Set default bins
			$categories=array(
				array('title' => '0-4 weeks', 'min' => 0, 'max' => 28), 
				array('title' => '5-8 weeks', 'min' => 29, 'max' => 56), 
				array('title' => '9-12 weeks', 'min' => 57, 'max' => 84), 
				array('title' => '13-16 weeks', 'min' => 85, 'max' => 112), 
				array('title' => '16+ weeks', 'min' => 113, 'max' => 9999)
				);
		}
		
		foreach($data as $days) {
			foreach($categories as $key => $bin) {
				if($days>=$bin['min'] && $days<=$bin['max']) {
					$result[$key]++;
				}
			}
		}

		foreach($categories as $key => $bin) {
			$output[$bin['title']]=$result[$key];
		}

		return $output;
	}
	
	// Takes a list of projects and returns a list of times in days between two given dates
	public function parseProjectDates($projects,$from='date_queue',$to='date_close') {
		if(is_array($projects)) {
			$now=date('Y-m-d');
			foreach($projects as $limsid => $project) {
				if(array_key_exists($from,$project)) {
					$date_from=$project[$from];
					if($to=='now') {
						$date_to=$now;
					} elseif(array_key_exists($to,$project)) {
						$date_to=$project[$to];
					} else {
						$date_to=FALSE;
					}
					
					if($diff=$this->dateDiff($date_from,$date_to)) {
						$list[$limsid]=$diff;
					} else {
						$errors[$limsid]="Missing dates in $limsid (from: $date_from, to: $date_to)";
					}
				} else {
					$errors[$limsid]="Missing dates in $limsid (from: $date_from)";
				}
			}
			asort($list);
			return array('data' => $list, 'error' => $errors);
		} else {
			return array('data' => FALSE, 'error' => 'Invalid input');
		}
	}
	
	// Calculate the percentiles in a series of values in an array
	// e.g. $k=0.25 (1st quartile), $k=0.5 (median, second quartile), $k=0.75 (3rd quartile) 
	public function kpercentile($data, $k) {
		sort($data);
	    $n=count($data);
	    $p=$k*($n-1);
	    if($p==(int)($p)) {
		    return $data[$p];
		} else {
		    return ($data[(int)$p]+$data[(int)($p+1)])/2;
		}
	}
	
	// Calculate the difference in days between two dates
	private function dateDiff($from,$to) {
		if($this->is_date($from) && $this->is_date($to)) {
			$date_from=new DateTime($from);
			$date_to=new DateTime($to);
			$date_diff=$date_from->diff($date_to);
			return $date_diff->format('%a'); //days
		} else {
			return FALSE;
		}
	}

	// Validate date and return as ISO format
	private function is_date($str) {
		$stamp=strtotime(trim($str));
		$month=date('m',$stamp);
		$day=date('d',$stamp);
		$year=date('Y',$stamp);
		if(checkdate($month,$day,$year)) {
			if($stamp!="") {
				return "$year-$month-$day";
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	// Get all projects from StatusDB
	private function getProjects($cache) {
		global $CONFIG;
		
		$now=time();

		$couch=new Couch($CONFIG['couch']['host'],$CONFIG['couch']['port'],$CONFIG['couch']['user'],$CONFIG['couch']['pass']);
		$json=$couch->getView($CONFIG['couch']['views']['projects']);
		
		if(is_object($json)) {
		    foreach($json->rows as $object) {
			    $projects[$object->key[1]]=array(
				    'lims_id'					=> $object->key[1], 
				    'project_name'				=> empty($object->value->project_name) ? "" : $object->value->project_name, 
				    'project_name_user'			=> empty($object->value->details->customer_project_reference) ? "" : $object->value->details->customer_project_reference, 
				    'project_funding'			=> empty($object->value->details->funding_agency) ? "" : $object->value->details->funding_agency, 
				    'project_category'			=> empty($object->value->details->project_category) ? "" : $object->value->details->project_category, 
				    'portal_id'					=> empty($object->value->details->portal_id) ? "" : $object->value->details->portal_id, 
				    'type'						=> empty($object->value->details->type) ? "" : $object->value->details->type, 
				    'agreement_cost'			=> empty($object->value->details->agreement_cost) ? 0 : $object->value->details->agreement_cost, 
				    'application'				=> empty($object->value->application) ? "" : $object->value->application, 
				    'reference_genome'			=> empty($object->value->reference_genome) ? "" : $object->value->reference_genome, 
				    'organism'					=> empty($object->value->details->organism) ? "" : $object->value->details->organism, 
				    'sample_units_ordered'		=> empty($object->value->details->sample_units_ordered) ? "" : $object->value->details->sample_units_ordered, 
				    'sample_units_imported'		=> empty($object->value->no_samples) ? "" : $object->value->no_samples, 
				    'sample_disposal'			=> empty($object->value->details->disposal_of_any_remaining_samples) ? "" : $object->value->details->disposal_of_any_remaining_samples, 
				    'lib_prep'					=> empty($object->value->details->library_construction_method) ? "" : $object->value->details->library_construction_method, 
				    'seq_platform'				=> empty($object->value->details->sequencing_platform) ? "" : $object->value->details->sequencing_platform, 
				    'seq_config'				=> empty($object->value->details->sequencing_setup) ? "" : $object->value->details->sequencing_setup, 
				    'seq_units_ordered'			=> empty($object->value->details->{'sequence_units_ordered_(lanes)'}) ? "" : $object->value->details->{'sequence_units_ordered_(lanes)'}, 
				    'date_order'				=> empty($object->value->details->order_received) ? "" : $object->value->details->order_received, 
				    'date_contract_sent'		=> empty($object->value->details->contract_sent) ? "" : $object->value->details->contract_sent, 
				    'date_plates_sent'			=> empty($object->value->details->plates_sent) ? "" : $object->value->details->plates_sent, 
				    'date_contract_received'	=> empty($object->value->details->contract_received) ? "" : $object->value->details->contract_received, 
				    'date_sample_info'			=> empty($object->value->details->sample_information_received) ? "" : $object->value->details->sample_information_received, 
				    'date_samples'				=> empty($object->value->details->samples_received) ? "" : $object->value->details->samples_received, 
				    'date_open'					=> empty($object->value->open_date) ? "" : $object->value->open_date, 
				    'date_queue'				=> empty($object->value->details->queued) ? "" : $object->value->details->queued, 
				    'date_seq_finished'			=> empty($object->value->details->all_samples_sequenced) ? "" : $object->value->details->all_samples_sequenced, 
				    'date_delivered'			=> empty($object->value->details->all_raw_data_delivered) ? "" : $object->value->details->all_raw_data_delivered, 
				    'date_close'				=> empty($object->value->close_date) ? "" : $object->value->close_date, 
				    'date_aborted'				=> empty($object->value->details->aborted) ? "" : $object->value->details->aborted 
			    );
		    }
		    
			$data=json_encode(array('meta' => array('timestamp' => $now, 'num_proj' => count($projects)), 'data' => $projects));
			if($cache) {
				file_put_contents($cache,$data);
			}
		} else {
			$data=FALSE;
		}
	
	    return $data;
	}
	
	// Get specific project from LIMS (using LIMS ID)
	public function getProject($lims_id) {
		global $CONFIG;
		if($lims_id=validateLimsProjectID($lims_id)) {
			$clarity=new Clarity();
			if($project=$clarity->getEntity("projects/$lims_id")) {
				return array('project' => $project, 'error' => FALSE);
			} else {
				return array('project' => FALSE, 'error' => "Project not found: $lims_id");
			}
		} else {
			return array('project' => FALSE, 'error' => "Invalid format of LIMS ID: $lims_id");
		}
	}
	
	// Parse information from library prep method
	public function parseLibprep($prep) {
	 	if(preg_match("/(.+)\[(\d+|-)\]/",$prep,$temp_data)) {
		 	// Only parse the string if it matches the new format
		 	// input,type,option,category [document ID]
			list($data['input'],$data['type'],$data['option'],$data['category'])=explode(",",$temp_data[1]);
		 	$data['document']=$temp_data[2];
	 	} else {
		 	// Latest format...
		 	list($data['input'],$data['type'],$data['option'],$data['category'],$data['document'])=explode(",",$prep);
	 	}
	
	 	// Remove whitespace
	 	foreach($data as $key => $value) {
		 	$data[$key]=trim($value);
	 	}
	 	
	 	return $data;
	}
	
	// Charts
	
	// Format labels for charts
	// $list = array with limsid as key
	public function formatLabels($list) {
		if(is_array($list)) {
			$labels=array_keys($list);
			foreach($labels as $key => $label) {
				$labels[$key]=$label.', '.$this->data[$label]['project_name'];
			}
			return $labels;
		} else {
			return FALSE;
		}
	}
}