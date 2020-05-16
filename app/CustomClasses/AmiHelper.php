<?php


//
// Developed by CoCo
// Copyright (C) 2016 CoCoSoft
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
namespace App\CustomClasses;

use App\CustomClasses\AmiHelper;

class AmiHelper {

protected $amiHdle;

public function get_peer_array($iax=false) {

	if ( ! $this->amiHelperLogin()) {
		return false;
	}

	$sip_peers = array(); 	
	if ($iax) {
		$amisiprets = $this->amiHdle->getIaxPeers();
	}
	else {
		$amisiprets = $this->amiHdle->getSipPeers();
	}
	$sip_peers = $this->build_peer_array($amisiprets);
	$this->amiHdle->logout();
	return $sip_peers;
}

public function get_coreShowChannels() {

	if ( ! $this->amiHelperLogin()) {
		return false;
	}
	
	$amisiprets = $this->amiHdle->getCoreShowChannels();
	$this->amiHdle->logout();
	return $amisiprets;
}

public function get_database($pkey,&$cfim,&$cfbs,&$ringdelay,&$twin) {

	if ( ! $this->amiHelperLogin()) {
		return false;
	}
	$cfim = $this->amiHdle->GetDB('cfim', $pkey);
	$cfbs = $this->amiHdle->GetDB('cfbs', $pkey);
	$ringdelay = $this->amiHdle->GetDB('ringdelay', $pkey);
	$twin = $this->amiHdle->GetDB('srktwin', $pkey);
	$this->amiHdle->logout();
	return;
}

public function put_database($newkey) {
	
	if ( ! $this->amiHelperLogin()) {
		return false;
	}
			
	if (isset($_POST['cfim'])) {
		$cfim			= strip_tags($_POST['cfim']);
		if ($cfim) {
			$this->amiHdle->PutDB('cfim', $newkey, $cfim);
		}
		else {
			$this->amiHdle->DelDB('cfim', $newkey);
		}
	}
	if (isset($_POST['cfbs'])) {
		$cfbs			= strip_tags($_POST['cfbs']);
		if ($cfbs) {
			$this->amiHdle->PutDB('cfbs', $newkey, $cfbs);
		}
		else {
			$this->amiHdle->DelDB('cfbs', $newkey);
		}					
	}
	if (isset($_POST['ringdelay'])) {
		$ringdelay		= strip_tags($_POST['ringdelay']);	
		if ($ringdelay) {
			$this->amiHdle->PutDB('ringdelay', $newkey, $ringdelay);
		}
		else {
			$this->amiHdle->DelDB('ringdelay', $newkey);
		}				
	}
	if (isset($_POST['celltwin'])) {
		if ($_POST['celltwin'] == 'ON') {
			$twin = strip_tags($_POST['cellphone']);
			$this->amiHdle->PutDB('srktwin', $newkey, $twin);
		}
		else {
			$this->amiHdle->DelDB('srktwin', $newkey);
		}	
	}
									
	$this->amiHdle->logout();

}	

public function amiHelperLogin() {
	$params = array('server' => '127.0.0.1', 'port' => '5038');
	$astrunning=false;
	$helper = new helper;
	if ( $helper->check_pid() ) {	
		$astrunning = true;
	}
	if ( $astrunning ) {			
		$this->amiHdle = new ami($params);
		$amiconrets = $this->amiHdle->connect();
		if ( !$amiconrets ) {
			return false;
		}
		else {
			$this->amiHdle->login('sark','mysark');
		}	
	}
	return true;
}
	
public function amiHelperLogout() {
	$params = array('server' => '127.0.0.1', 'port' => '5038');
	$astrunning=false;
	$helper = new helper;
	if ( $helper->check_pid() ) {	
		$astrunning = true;
	}
	if ( $astrunning ) {			
		$this->amiHdle = new ami($params);
		$this->amiHdle->logout();
	}
	return true;
}

private function build_peer_array($amirets) {
/*
 * build an array of peers by cleaning up the AMI output
 * (which contains stuff we don't want).
 */ 
	$peer_array=array();
	$lines = explode("\r\n",$amirets);	
	$peer = 0;
	foreach ($lines as $line) {
		// ignore lines that aren't couplets
		if (!preg_match(' /:/ ',$line)) { 
				continue;
		}
		
		// parse the couplet	
		$couplet = explode(': ', $line);
		
		// ignore events and ListItems
		if ($couplet[0] == 'Event' || $couplet[0] == 'ListItems') {
			continue;
		}
		
		//check for a new peer and set a new key if we have one
		if ($couplet[0] == 'ObjectName') {
			preg_match(' /^(.*)\// ',$couplet[1],$matches);
			if (isset($matches[1])) {
				$peer = $matches[1];
			}
			else {
				$peer = $couplet[1];
			}
		}
		else {
			if (!$peer) {
				continue;
			}
			else {
				$peer_array [$peer][$couplet[0]] = $couplet[1];
			}
		}
	}
	return $peer_array;	
}


}
?>
