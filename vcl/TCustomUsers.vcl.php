<?php
class TCustomUsers{
	public function checkUser($UserCode){
		return false;
	}
	
	public function checkPassword($UserCode, $password){
		return false;
	}
	
	public function getUserName($UserCode){
		return $UserCode;
	}
	
	public function getUserLevel($UserCode){
		return 2;
	}
}
?>