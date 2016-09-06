<?php
class TWFUsers extends TCustomUsers{
	private $users;
	
	public function __construct()
	{
		// 载入默认的
		//parent::__construct();
		$dm = new TMainData();
		$result = mysql_query("select * from WF_UserInfo where Enabled_=1");

		while($row = mysql_fetch_array($result)){
			$this->users[$row['UserCode_']] = $row;
		}		
	}
	
	public function checkUser($UserCode){
		return array_key_exists($UserCode, $this->users);
	}
	
	public function checkPassword($UserCode, $password){
		if(array_key_exists($UserCode, $this->users)){
			if($this->users[$UserCode]['UserPasswd_'] === md5($password)){
                $this->UPLoginTime($UserCode);
                return true;
			}
		}
	}
	
	public function getUserName($UserCode){
		if(array_key_exists($UserCode, $this->users)){
			return $this->users[$UserCode]['UserName_'];
		}else{
			return $UserCode;
		}
	}
	
	public function getUserLevel($UserCode){
		if(array_key_exists($UserCode, $this->users)){
			return intval($this->users[$UserCode]['Level_']);
		}else{
			return 2;
		}
	}
	
	public function ReadValue($UserCode, $field){
		if(array_key_exists($UserCode, $this->users)){
			return $this->users[$UserCode][$field];
		}else{
			return 0;
		}
	}

    public  function  UPLoginTime($UserCode){
        if(!mysql_query("update WF_UserInfo set LoginTime_='"
            . date('Y-m-d H:i:s') . "' where UpdateKey_='"
            . $this->users[$UserCode]['UpdateKey_'] . "'")){
            die('update LoginTime Error!');
        }
    }
}
?>