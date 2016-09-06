<?php
class TFrmLogin extends TDBForm
{
	public function checkLogin(){
		return true;
	}

	public function OnCreate(){
		parent::OnCreate();
		$this->Caption = '系统登入';
		if(isLogin()){
			$this->AddMenu('工作微博');
			$this->AddMenu(array('?m=TWFDiary&a=OnAppend', '写新的微博'));
			$this->AddMenu(array('?m=TWFDiary', '查看我的微博'));
			$this->AddMenu(array('?m=TWFDiary&a=ViewUsers', '看看同事在忙什么'));
		}
		$this->AddMenu('友情链接');
		$this->AddMenu(array('http://www.mimrc.com', '深圳华软公司'));
		$this->AddMenu(array('http://www.c123.com', '中国短讯网'));
	}
	
	public function OnDefault(){
		global $Session;
		if($Session->Login){
			echo "<p>您好：$Session->UserName <p/>\n";
			echo "<p>当前帐号：$Session->UserCode <p/>\n";
			$url = $_SERVER['PHP_SELF'] . "?logout";
			echo "<p><a href=\"$url\">退出登录！</a></p>\n";
		}else{
			$url = $this->GetUrl();
			echo "<center>\n";
			echo "<form method=\"POST\" action=\"$url\">\n";
			echo "<p>用户帐号：<input type=\"text\" name=\"UserCode\" size=\"10\"></p>\n";
			echo "<p>用户密码：<input type=\"password\" name=\"password\" size=\"10\"></p>\n";
			echo "<p><input type=\"submit\" value=\"提交\" name=\"B1\">&nbsp;\n";
			echo "<input type=\"reset\" value=\"重置\" name=\"B2\"></p>\n";
			echo "</form>\n";
			echo "<center>\n";
			echo "<center>$Session->Message</center>\n";
		}
	}
}
?>