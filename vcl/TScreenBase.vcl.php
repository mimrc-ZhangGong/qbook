<?php
if( !defined('IN') ) die('bad request');

class TScreenBase extends TWinControl{

	private $width = 768; //768 or 480 or 240;
	public $Caption = '当前应用标题';
	public $menus = array();
	public $mainmenu = array();
	public $HeadVisible = true;
	public $FootVisible = true;
	public $mainbox;

	public function __construct()
	{
		global $Session;
		global $mainmenu_class;
		if($mainmenu_class){ //取得菜单列表
			$mainmenu = new $mainmenu_class;
		}else{
			die(__class__.'config error');
		}
		$this->mainmenu = $mainmenu->getItems();
		$this->width = $Session->ScreenWidth;
		$this->mainbox = new TWinControl($this);
	}
	
	public function __get($name){
		if($name === 'width')
			return $this->width;
	}
		
	public function AddMenu($item)
	{
		$this->menus[] = $item;
	}

	public function OnBeforeShow(){
		echo "<html>\n<head>\n";
		global $Script;
		if($Script){
			echo "<script type=\"text/javascript\" src=\"vcl/jquery-1.7.1.min.js\"></script>\n";
			//echo "<script type=\"text/javascript\" src=\"vcl/jquery-1.7.js\"></script>\n";
			echo $Script . "\n";
		}
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/>\n";
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\"/>\n";
		//echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"common.css\"/>\n";
		echo "<title>$this->Caption</title>";
		echo "</head>\n";
		echo "<body>\n";
	}
	
	public function OnShow(){
		global $PV_TOTAL;
		if($PV_TOTAL){
			echo "<center>$PV_TOTAL</center>";
		}
		echo "</body>\n</html>\n";
	}
	
	public function Begin(){
	}
	
	public function End(){
	}
}
?>