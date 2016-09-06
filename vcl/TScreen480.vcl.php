<?php
if( !defined('IN') ) die('bad request');

class TScreen480 extends TScreenBase{
	private $colspan = 2;

	public function __construct(){
		parent::__construct();
		$this->mainbox->Window = $this;
	}

	public function OnBeforeShow(){
		parent::OnBeforeShow();
		//主表格
		$val = 800 - 30;
		echo "<table border=\"0\" id=\"table1\" width=\"100%\" "
			. "style=\"background-color: #C0C0C0\" cellspacing=\"4\">\n";
		//显示主菜单
		$this->ShowMainMenu();
		//显示状态栏
		$height = 25;
		$url = "<a href=\"?width=768\">电脑屏</a>";
		global $Session;
		if($Session->Message){
			echo "<tr height=\"$height\"><td colspan=\"$this->colspan\" class=\"message\">";
			echo '&nbsp;' . $url . ' ' . $Session->Message;
			echo "</td></tr>\n";
		}
		//主功能区, 动态菜单 and 主显示区
		echo "<tr height=\"350\">\n";
		if($this->colspan > 1){
			$this->ShowIndexMenu();
		}
		//主显示区
		echo "<td height=\"100%\" valign=\"top\" class=\"main\">\n";
	}
	
	public function OnShow(){
		global $copyright, $admin_email, $website;
		echo "\n</td></tr>\n";
		echo "<tr height=\"25\">\n";
		echo "<td colspan=\"$this->colspan\" bgcolor=\"#ECE9D8\">\n";
		echo "<p>版权所有：$copyright</p>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		parent::OnShow();
	}
	
	public function ShowMainMenu(){
		echo "<tr><td colspan=\"$this->colspan\">\n";
		echo "<center>\n";
		$i = 0;
		global $Session;
		global $appname;
		$this->mainmenu[0][1] = $appname;
		foreach($this->mainmenu as $item){
			$url = $item[0];
			$name = $item[1];
			if($i > 0) echo " | ";
			$i++ ;
			echo "<a href=\"$url\">$name</a>";
		}
		echo "\n</center>\n";
		echo "</td></tr>\n";
	}
	
	public function ShowIndexMenu(){
		echo "<td width=\"140 px\" bgcolor=\"#FFFFFF\" valign=\"top\" class=\"menu\">\n";
		//动态菜单
		if(count($this->menus) > 0){
			echo "<table border=\"0\" width=\"100%\">\n";
			foreach($this->menus as $item){
				if(!is_array($item)){
					echo "<tr height=\"25\"><td align=\"center\" bgcolor=\"#0000FF\">"
						. "<font color=\"#FFFFFF\">$item</font></td></tr>\n";
				}
				else{
					echo "<tr height=\"25\"><td><a href=\"$item[0]\">$item[1]";
					if(count($item) == 3) echo " $item[2]";
					echo "</a></td></tr>\n";
				}
			}
			echo "</table>\n";
		}
		echo "</td>\n";
	}
}
?>