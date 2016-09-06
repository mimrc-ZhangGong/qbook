<?php
if( !defined('IN') ) die('bad request');

class TScreen768 extends TScreenBase{
	private $colspan = 2;
	private $appTable;
	private $title_tr, $title_td; //标题行
	private $menu_tr, $menu_td; //主菜单
	private $index_tr; //temp
	private $index_td; //索引菜单区
	private $main_tr, $main_td; //主操作区
	private $msg_tr, $msg_td; //提示讯息
	private $foot_tr, $foot_td; //版权区
	
	public function __construct(){
		parent::__construct();
		$this->appTable = new TABLE($this); $this->appTable->Window = $this;
		$this->title_tr = new TR($this); $this->title_tr->Window = $this->appTable;
		$this->menu_tr  = new TR($this); $this->menu_tr->Window  = $this->appTable;
		$this->index_tr = new TR($this); $this->index_tr->Window = $this->appTable;
		$this->main_tr  = new TR($this); $this->main_tr->Window  = $this->appTable;
		//$this->msg_tr   = new TR($this); $this->msg_tr->Window   = $this->appTable;
		$this->foot_tr  = new TR($this); $this->foot_tr->Window  = $this->appTable;
		{ //归属
			$this->title_td = new TD($this); $this->title_td->Window = $this->title_tr;
			$this->menu_td  = new TD($this); $this->menu_td->Window  = $this->menu_tr;
			
			$this->index_td = new TD($this); $this->index_td->Window = $this->index_tr;
			$this->msg_td   = new TD($this); $this->msg_td->Window   = $this->index_tr;
			$this->main_td  = new TD($this); $this->main_td->Window  = $this->main_tr;
			/* 菜单栏右置
			$this->msg_td  = new TD($this); $this->msg_td->Window  = $this->index_tr;
			$this->index_td = new TD($this); $this->index_td->Window = $this->index_tr;
			$this->main_td   = new TD($this); $this->main_td->Window   = $this->main_tr;
			*/
			$this->foot_td  = new TD($this); $this->foot_td->Window  = $this->foot_tr;
		}
		{ //样式
			$val = 1024 - 30;
			//$this->appTable->Params = "border=\"0\" width=\"" . $val . " px\" id=\"table1\" "
			$this->appTable->Params = "border=\"0\" width=\"100%\" id=\"table1\" "
				. "style=\"background-color: #C0C0C0\" cellspacing=\"4\"";
			$this->title_td->Params = "colspan=\"$this->colspan\" height=\"90\" bgcolor=\"#ECE9D8\"";
			$this->menu_tr->Params ="id=\"menu_tr\"";
			$this->menu_td->Params = "colspan=\"$this->colspan\"";
			//$this->msg_tr->Params ="style=\"background-color: #ECE9D8\"";
			$this->msg_td->Params = "class=\"message\" height=\"25\" colspan=\"2\" style=\"background-color: #ECE9D8\"";
			$this->msg_td->Params = "class=\"message\" height=\"25\"";
			$this->index_td->Params = "width=\"140 px\" style=\"background-color: #ECE9D8\" "
				. "valign=\"top\" class=\"menu\" rowspan=\"2\"";
			$this->main_tr->Params = "height=\"350\"";
			$this->main_td->Params = "height=\"100%\" valign=\"top\" "
				. "class=\"main\"";
			$this->foot_tr->Params = "height=\"80\"";
			$this->foot_td->Params = "colspan=\"$this->colspan\" bgcolor=\"#ECE9D8\"";
		}
		$this->mainbox->Window = $this->main_td;
	}
	
	public function OnBeforeShow(){
		parent::OnBeforeShow();
		global $appname, $Session, $copyright, $website, $admin_email;
		if(!$Session) $Session = new TWebSession();
		//系统标题
		$this->title_td->Lines[] = "<p align=\"center\"><b><font size=\"7\">$appname</font></b></p>";
		//主菜单区
		//$this->menu_td->Lines[] = "<center>\n";
		$this->menu_td->Lines[] = "&nbsp;\t";
		$i = 0;
		foreach($this->mainmenu as $item){
			$url = $item[0];
			$name = $item[1];
			if($i > 0) $this->menu_td->Lines[] = " |\n";
			$i++ ;
			$this->menu_td->Lines[] = "<a href=\"$url\" class=\"mainmenu\">$name</a>";
		}
		//$this->menu_td->Lines[] = "</center>\n";
		//提示讯息区
		$this->msg_td->Lines[] = "&nbsp;<a href=\"?width=240\">手机屏</a> $Session->Message";
		//动态菜单
		if(count($this->menus) > 0){
			$this->index_td->Lines[] = "<table border=\"0\" width=\"100%\">\n";
			foreach($this->menus as $item){
				if(is_array($item)){
					if($item[0] <> ''){
						$this->index_td->Lines[] = "<tr height=\"25\"><td><a href=\"$item[0]\">$item[1]";
						if(count($item) == 3) $this->index_td->Lines[] = " $item[2]";
						$this->index_td->Lines[] = "</a></td></tr>\n";
					}else{
						$this->index_td->Lines[] = "<tr height=\"25\"><td>$item[1]</td></tr>\n";
					}
				}
				else{
					$this->index_td->Lines[] = "<tr height=\"25\"><td align=\"center\" bgcolor=\"#0000FF\">"
						. "<font color=\"#FFFFFF\">$item</font></td></tr>\n";
				}
			}
			$this->index_td->Lines[] = "</table>\n";
		}
		//版权区
		$this->foot_td->Lines[] = 
'<p align="center" style="line-height: 150%">版权所有：'.$copyright.'<br/>
网址：<a href="http://'.$website.'">http://'.$website.'</a><br/>
系统管理员：<a href="mailto:邮件：'.$admin_email.'">'.$admin_email.'</a></p>
';
	}
}
?>