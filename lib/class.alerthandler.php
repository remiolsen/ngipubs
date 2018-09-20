<?php
class alertHandler {
	function __construct() {
		if(isset($_SESSION['alerts'])) {
			$this->save=TRUE;
			$this->alerts=$_SESSION['alerts'];
		} else {
			$this->save=FALSE;
			$this->alerts=array();
		}
	}
	
	public function setAlert($text,$type='notice') {
		$types=array('notice','warning','error','success');
		if(trim($text)!='') {
			$type=in_array($type,$types) ? $type : 'notice';
			$result=array(
				'type' => $type, 
				'text' => $text
			);
			
			if($this->save) {
				array_push($_SESSION['alerts'],$result);
			}
			
			array_push($this->alerts,$result);
		}
	}
	
	// Customized to Zurb Foundation
	public function render($type=FALSE) {
		$classes=array(
			'notice' => 'primary', 
			'warning' => 'warning', 
			'error' => 'alert', 
			'success' => 'success'
		);

		if(count($this->alerts)) {
			switch($type) {
				default:
				case 'callout':
					foreach($this->alerts as $alert) {
						$html.='<div class="callout '.$classes[$alert['type']].'">'.$alert['text']."</div>\n";
					}
				break;
				
				case 'label':
					foreach($this->alerts as $alert) {
						$html.='<span class="label '.$classes[$alert['type']].'">'.$alert['text']."</span>";
					}
				break;
			}
			return $html;
		}
	}
	
	public function clear() {
		$this->alerts=array();
		if($this->save) {
			$_SESSION['alerts']=array();
		}
	}
	
	public function saveSession() {
		$this->save=TRUE;
		$_SESSION['alerts']=$this->alerts;
	}
	
	public function killSession() {
		$this->save=FALSE;
		unset($_SESSION['alerts']);
	}
}