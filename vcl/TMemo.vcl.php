<?php
if( !defined('IN') ) die('bad request');

class TMemo extends TWinControl{
	private $Text = '';
	private $Width = 40;
	private $Caption = '';
	private $Hint = '';
	public $AllowNull = false;
	public $Visible = true;
	public $ReadOnly = false;
	//与数据库有关
	public $DataSet;
	private $FieldInfo;
	
	public function LinkField($FieldCode, $FieldInfo){
		$this->Name = $FieldCode;
		$this->FieldInfo = $FieldInfo;
		$this->Caption = $FieldInfo->Caption;
		$this->Hint = $FieldInfo->Hint;
		if(is_string($FieldInfo->modify)){
			$this->ReadOnly = $FieldInfo->modify == 'ReadOnly';
		}else{
			$this->ReadOnly = !$FieldInfo->modify;
		}
		if($this->DataSet){
			$this->Text = $this->DataSet->FieldByName($FieldCode);
		}else{
			$this->Text = $FieldInfo->Value;
		}
	}
	
	public function GetHtmlText(){
		$readonly = $this->ReadOnly ? ' readonly' : '';
		return "<textarea rows=\"10\" name=\"$this->Name\" "
			. "cols=\"$this->Width\"$readonly>$this->Text</textarea>\n";
	}
	
	public function checkInput(){
		if($this->Name <> ''){
			if(isset($_POST[$this->Name])){
				$this->Text = $_POST[$this->Name];
			}
		}
		if($this->AllowNull) //允许为空
			return true;
		else
			return $this->Text === '' ? false : true;
	}
	
	public function __set($name, $value){
		parent::__set($name, $value);
		if($name === 'Text'){
			$this->Text = $value;
		}elseif($name === 'Width'){
			$this->Width = $value;
		}elseif($name === 'Caption'){
			$this->Caption = $value;
		}elseif($name === 'Hint'){
			$this->Hint = $value;
		}
	}
	
	public function __get($name){
		if($name === 'Text'){
			return $this->Text;
		}elseif($name === 'Width'){
			return $this->Width;
		}elseif($name === 'Caption'){
			if($this->Caption === '')
				return $this->Name;
			else
				return $this->Caption;
		}elseif($name === 'Hint'){
			return $this->Hint;
		}elseif($name === 'HtmlText'){
			return $this->GetHtmlText();
		}else{
			return parent::__get($name);
		}
	}
}
?>