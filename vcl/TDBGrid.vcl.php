<?php
if( !defined('IN') ) die('bad request');

class TDBGrid extends TWinControl
{
	public $DataSet;
	public $Fields;
	private $MaxRows = 100; //每页显示最大行数
	private $Page = 1;
	private $LastPage;
	private $RowCount = 0; //记录总数
	private $StartRow = 0;
	private $EndRow = 9;
	private $colspan = 0;
	
	public function Begin()
	{
		if(isset($_GET['Page'])){
			$this->Page = $_GET['Page'];
		}
		$this->StartRow = $this->MaxRows * ($this->Page - 1);
		$this->EndRow = $this->StartRow + $this->MaxRows - 1;
		$this->Lines[] = "\n<table class=\"data\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\">\n";
	}
	
	public function End()
	{
		$this->LastPage = ($this->RowCount - ($this->RowCount % $this->MaxRows)) / $this->MaxRows;
		if($this->RowCount % $this->MaxRows > 0) $this->LastPage++;
		if(($this->StartRow > 0) or ($this->EndRow < $this->RowCount)){
			$this->Lines[] = "<tr class=\"tr_even\">\n";
			$this->Lines[] = "<td colspan=\"$this->colspan\">\n";
			//
			$this->Lines[] = "<table width=\"100%\">\n";
			$this->Lines[] = "<tr>\n<td>\n";
			$this->Lines[] = "总记录数：$this->RowCount 笔, 每页显示 $this->MaxRows 行, 第 $this->Page 页\n";
			$this->Lines[] = "</td>\n";
			$this->Lines[] = "<td align=\"right\">\n";
			$this->Lines[] = $this->GetPageUrl(1, '第一页') . " \n";
			$this->Lines[] = $this->GetPageUrl($this->Page - 1, '上一页') . " \n";
			$this->Lines[] = $this->GetPageUrl($this->Page + 1, '下一页') . " \n";
			$this->Lines[] = $this->GetPageUrl($this->LastPage, '最后一页') . " \n";
			$this->Lines[] = "</td>\n</tr>\n";
			$this->Lines[] = "</table>\n";
			//
			$this->Lines[] = "</td>\n";
			$this->Lines[] = "</tr>\n";
		}
		$this->Lines[] = "</table>\n";
	}
	
	
	public function OnShow()
	{
		if(is_array($this->Fields)){ //有定义字段的输出方式
			$this->ShowByFields();
		}
		elseif($this->DataSet){
			$this->ShowDefault();
		}
		if(count($this->Lines) > 0){
			foreach($this->Lines as $line){
				echo $line;
			}
		}
	}
	
	public function ShowByFields(){ //根据 Fields 定义输出
		$this->Begin();
		$this->Lines[] = "<tr class=\"tr_theme\">\n";
		foreach($this->Fields as $code => $params){
			$this->AddTitle($code, $params);
		}
		$this->Lines[] = "</tr>\n";
		for($i=0; $i<$this->DataSet->RecordCount(); $i++)
		{
			$this->DataSet->Next();
			if(($i >= $this->StartRow) and ($i <= $this->EndRow)){
				$this->Lines[] = "<tr class=\"tr_even\">\n";
				foreach($this->Fields as $code => $params){
					$this->AddField($code, $params);
				}
				$this->Lines[] = "</tr>\n";
			}
		}
		$this->RowCount = $this->DataSet->RecordCount();
		$this->End();
	}
	
	public function ShowDefault(){ // 直接输出所有的字段
		$this->Begin();
		$this->Lines[] = "<tr class=\"tr_theme\">\n";
		for($j=0; $j < $this->DataSet->FieldCount(); $j++)
		{
			$this->AddHead($this->DataSet->getFieldName($j));
		}
		$this->Lines[] = "</tr>\n";
		$row = 0;
		for($i=0; $i<$this->DataSet->RecordCount(); $i++)
		{
			$this->DataSet->Next();
			if(($row >= $this->StartRow) and ($row <= $this->EndRow)){
				$this->Lines[] = "<tr class=\"tr_even\">\n";
				for($j=0; $j < $this->DataSet->FieldCount(); $j++)
				{
					$this->AddItem($this->DataSet->FieldByIndex($j));
				}
				$this->Lines[] = "\n</tr>\n";
			}
			$row++;
		}
		$this->RowCount = $this->DataSet->RecordCount();
		$this->End();
	}
	
	public function OutArray($Datas){ //输出指定的 $Datas 数组
		if(!is_array($Datas)){
			die('OutArray Error: $Datas 不是一个数组！');
		}
		$this->Begin();
		$i = 0;
		foreach($Datas as $Line){
			if(($i >= $this->StartRow) and ($i <= $this->EndRow)){
				$this->Lines[] = $i === 0 ? "<tr class=\"tr_theme\">" : "<tr class=\"tr_even\">";
				foreach($Line as $cell)
					$this->Lines[] = $i === 0 ? $this->AddHead($cell) : $this->AddItem($cell);
				$this->Lines[] = "</tr>\n";
			}
			$i++;
		}
		$this->RowCount = count($Datas);
		$this->End();
	}
	
	public function AddHead($Caption)
	{	
		$this->Lines[] = "<th>$Caption</th>";
		$this->colspan++;
	}
	
	public function AddItem($value)
	{
		$this->Lines[] = "<td>$value</td>";
	}
	
	public function AddTitle($field, $params)
	{
		if(is_numeric($field)){
			$this->AddHead('操作');
		}
		else{
			$fi = new TFieldInfo($field, $params);
			if($fi->view){
				$width = $fi->hasParam('width') ? $fi->width : 1;
				if($width > 0){
					$this->AddHead($fi->Caption);
				}
			}
		}
	}
	
	public function AddField($field, $params)
	{
		$fi = new TFieldInfo($field, $params);
		if($fi->view){
			$width = $fi->hasParam('width') ? $fi->width : 1;
			if($width > 0){
				if($width > 1){
					$this->Lines[] = '<td width="'.$width.'">';
				}else{
					$this->Lines[] = '<td>';
				}
				if($fi->hasParam('OnGetText')){
					$event = $fi->OnGetText;
					$value = $this->Owner->$event($this->DataSet, $field, $params);
					$this->Lines[] = $value;
				}elseif($fi->hasParam('Items')){
					$Items = $fi->Items;
					$value = $this->DataSet->FieldByName($field);
					if(array_key_exists($value, $Items)){
						$this->Lines[] = $Items[$value];
					}else{
						$this->Lines[] = $value;
					}
				}else{
					$this->Lines[] = $this->DataSet->FieldByName($field);
				}
				$this->Lines[] = '</td>';
			}
		}
	}
	
	private function GetPageUrl($Page, $Title){
		$PageNo = $Page;
		if($Page == 0)
			$PageNo = 1;
		elseif($Page > $this->LastPage)
			$PageNo = $this->LastPage;
		//
		$url = $_SERVER['PHP_SELF'];
		foreach($_GET as $key => $value){
			if($key <> 'Page'){
				$url .= (strpos($url, '?') ? "&" : "?") . "$key=$value";
			}
		}
		$url .= (strpos($url, '?') ? "&" : "?") . "Page=$PageNo";
		return BuildUrl($url, $Title);
	}
}
?>