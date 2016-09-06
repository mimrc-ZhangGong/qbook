<?php

class TWebSession
{
	private $Login = false;
	private $UserCode = '';
	private $UserName = '';
	private $Message = '欢迎！';
	public $users;
	private $ScreenWidth = 768;
	
	public function __construct(){
		global $users_class;
		if(!$users_class) die('config error!');
		$sw_arr = array(0, 768, 480, 240);
		//适应不同的设备
		if(isset($_GET['width'])){
			$wh = $_GET['width'];
			if(array_search($wh, $sw_arr) > 0){
				$_SESSION['width'] = $wh;
				$this->ScreenWidth = $wh;
				//echo "ScreenWidth set $wh ok!";
			}
		}elseif(isset($_SESSION['width'])){
			$this->ScreenWidth = $_SESSION['width'];
		}
		$this->users = new $users_class;
		if(isset($_SESSION['UserCode'])){
			if(isset($_GET['logout'])){
				unset($_SESSION['UserCode']);
			}
			else{
				$UserCode = $_SESSION['UserCode'];
				$this->Login = true;
				$this->UserCode = $UserCode;
				$this->UserName = $this->users->getUserName($UserCode);
                $this->users->UPLoginTime($UserCode);
				$this->Message = "欢迎您：$UserCode";
			}
		}
		if(!$this->Login){
			if(isset($_POST['UserCode'])){
				$UserCode = $_POST['UserCode'];
				$password = $_POST['password'];
				if($UserCode and $password){
					$this->Login($UserCode, $password);
				}
				else
					$this->Message = '请登入系统！';
			}
			else
				$this->Message = '请登入系统！';
		}
	}
	
	public function Login($UserCode, $password){
		if($this->users->checkUser($UserCode)){
			if($this->users->checkPassword($UserCode, $password)){
					$this->Login = true;
					$this->UserCode = $UserCode;
					$this->UserName = $this->users->getUserName($UserCode);
					$this->Message = "欢迎您：$UserCode";
					$_SESSION['UserCode'] = $UserCode;
					return true;
				}
			else
				$this->Message = "用户 $UserCode 密码错误！";
				return false;
		}
		else
			$this->Message = "用户帐号 [$UserCode] 不存在！";
			return false;
	}
	
	public function getUserName($UserCode)
	{
		return $this->users->getUserName($UserCode);
	}
	
	public function __get($name){
		if($name === 'Login')
			return $this->Login;
		elseif($name === 'UserCode')
			return $this->UserCode;
		elseif($name === 'UserName')
			return $this->UserName;
		elseif($name === 'UserLevel'){
				if($this->Login)
					return $this->users->getUserLevel($this->UserCode);
				else
					return 2;
			}
		elseif($name === 'CorpCode'){
				if($this->Login)
					return $this->users->ReadValue($this->UserCode, 'CorpCode_');
				else
					return 'ERPV61';
			}
		elseif($name === 'Message')
			return $this->Message;
		elseif($name === 'CorpCode')
			return 'MIMRC';
		elseif($name === 'ScreenWidth'){
			return $this->ScreenWidth;
		}
		else
			return null;
	}
	
	public function __set($name, $value){
		if($name === 'Message'){
			$this->Message = $value;
		}
	}
}

function isLogin(){
	global $Session;
	if($Session){
		return $Session->Login;
	}else{
		return false;
	}
}

function uLevel(){
	global $Session;
	return $Session->UserLevel;
}
?>