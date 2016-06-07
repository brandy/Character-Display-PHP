<?php
/*
+--------------------------------------------------------+
|                   Made by HoltHelper                   |
|                                                        |
| Please give proper credits when using this code since  |
| It took me over a couple of months to finish this code |
|                                                        |
+--------------------------------------------------------+
*/
class Controller {
	
	public $stand = 1;
	private $debug;
	private $vslot;
	
	// Default Varibles
	public $skin = 0;
	public $gender = 0;
	
	//Default Gender Clothes
	public $clothes = array(
		"coat" => array(0 => 1040036, 1 => 1041046),
		"pants" => array(0 => 1060026, 1 => 1061039)
	);
	
	function __construct($debug = false) {
		if($debug != true) {
			header('Content-type: image/png');
			$this->image = ImageCreateTrueColor(96, 96);
			ImageSaveAlpha($this->image, true);
			ImageFill($this->image, 0, 0, ImageColorAllocateAlpha($this->image, 0, 0, 0, 127));
		}
	}
	
	public function setConstants($variable) {
		$this->debug  = (bool)$variable['debug'];
		$this->gender = (int)$variable['gender'];
		$this->job    = (int)$variable['job'];
		if(!isset($variable['coat'])) {
			$variable['coat'] = $this->clothes['coat'][$variable['gender']];
		}
		if(!isset($variable['pants']) && $variable['coat'] < 1050000) {
			$variable['pants'] = $this->clothes['pants'][$variable['gender']];
		}
		foreach(array_slice($variable, 3) as $wz => $lv1) {
			if(is_array($lv1)) {
				foreach($lv1 as $type => $lv2) {
					$variable[$wz][$type] = self::INILoader("./".$wz."/".$lv2.".ini");
				}
			} else {
				$variable[$wz] = self::INILoader("./".$wz."/".$lv1.".ini");
			}
			$this->$wz = $variable[$wz];
		}
		if(isset($this->weapon['base'])) {
			$this->stand = $this->weapon['base']['info']['stand'];
		}
		if(isset($this->weapon['cash'])) {
			$this->weapon = $this->weapon['cash'];
		} elseif(isset($this->weapon['base'])) {
			$this->weapon = $this->weapon['base'];
		}
		if(isset($this->cap)) {
			$this->vslot = $this->cap['info']['vslot'];
		}
		return $this;
	}
	
	public function skin($type) {
		if(isset($this->skin)) {
			if($type == "head" || ($type == "ear" && ($this->job == 2002 || ($this->job >= 2300 && $this->job <= 2312)))) {
				self::useImage($this->skin[$type]['i'], $this->skin[$type]['x'], $this->skin[$type]['y']);
			} elseif($type == "body" ||  $type == "arm" || $type == "hand") {
				self::useImage($this->skin[$type][$this->stand.'.i'], $this->skin[$type][$this->stand.'.x'], $this->skin[$type][$this->stand.'.y']);
			}
		}
		return $this;
	}
	
	public function hair($z) {
		$removeHair = array(
			"hairOverHead" => array( array("H1", "H4", "H5"), 2 ),
			"hair" => array( array("H1", "H2", "H4"), 3 ),
			"hairBelowBody" => array( array("H1", "Hb"), 2 )
		);
		if(isset($this->hair)
			&& !(count(array_intersect($removeHair[$z][0], str_split($this->vslot, 2))) >= $removeHair[$z][1])) {
			foreach($this->hair as $value) {
				if(isset($value['z']) && $value['z'] == $z) {
					self::useImage($value['i'], $value['x'], $value['y']);
				}
			}
		}
		return $this;
	}
	
	public function accessory($type, $z) {
		$vslot = array("face" => "Af", "eyes" => "Ay", "ears" => "Ae");
		$faceException = array("CpH1H2H3H5HfHsAfAyAsAeHbH4H6","CpHdH1H2H3H4H5HfHsFcAfAyAsAfAe","CpH1H2H3H4H5H6HfHsHbHcAfAyAsAfAe");
		if(isset($this->accessory)) {
			if(isset($this->accessory[$type])
			&& (($type == "face" && in_array($this->vslot, $faceException)) || !in_array($vslot[$type], str_split($this->vslot, 2)))) {
				foreach($this->accessory[$type] as $value) {
					if(isset($value['z']) && $value['z'] == $z) {
						self::useImage($value['i'], $value['x'], $value['y']);
					}
				}
			}
		}
		return $this;
	}
	
	public function lv1($wz, $z) { // Face/Cap
		if(isset($this->$wz)) {
			foreach($this->$wz as $value) {
				if(isset($value['z']) && $value['z'] == $z) {
					self::useImage($value['i'], $value['x'], $value['y']);
				}
			}
		}
		return $this;
	}
	
	public function lv2($wz, $z) { // Cape/Coat/Glove/Pants/Shoes/Shield/Weapon
		if(isset($this->$wz)) {
			foreach($this->$wz as $value) {
				if(isset($value[$this->stand.'.z']) && $value[$this->stand.'.z'] == $z) {
					self::useImage($value[$this->stand.'.i'], $value[$this->stand.'.x'], $value[$this->stand.'.y']);
				}
			}
		}
		return $this;
	}
	
	private function weaponShield($job) {
		if(($job >= 430) && ($job <= 434)) { // isDualBlade
			return true;
		} elseif(($job == 3002) || ($job >= 3600 && $job <= 3612)) { // isXenon
			return true;
		} elseif(($job == 6001) || ($job >= 6500 && $job <= 6512)) { // isAngelicBuster
			return true;
		}
		return false;
	}
	
	private function useImage($string, $x, $y) {
		if(strlen($string) > 0 && $this->debug == false) {
			$implace = imagecreatefromstring(base64_decode($string));
			imagecopy($this->image, $implace, $x, $y, 0, 0, imagesx($implace), imagesy($implace));
		}
	}
	
	private function INILoader($path) {
		if(self::exists($path)) {
			return parse_ini_file($path, true);
		}
	}
	
	private function exists($path) {
		if(file_exists($path))
			return true;
		clearstatcache();
	}
	
	public function show($string) {
		echo "<pre>";
		print_r($string);
		echo "</pre>";
	}
	
	public function debug() {
		return self::show(get_object_vars($this));
	}
	
	function __destruct() {
		if($this->debug != true) {
			ImagePng($this->image);
			ImageDestroy($this->image);
		}
	}

}
?>