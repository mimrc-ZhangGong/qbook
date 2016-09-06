<?php
if( !defined('IN') ) die('bad request');

class TDBForm extends TForm
{
	public $DataSet;
	public $TableName = '';
	public $TableSort = '';
	public $Fields = array();
	public $UseDatabase = true;
	
	/*
	public function __construct($Owner = null){
		$this->BadMainface = array('SaveToExcel'); //转XLS
		//$this->UseDatabase = false; //不使用数据库
		parent::__construct($Owner);
	}
	*/
	
	public function OnCreate()
	{
		if($this->UseDatabase){
			//echo '<center>Use Database.</center>';
			$dm = new TMainData();
		}
	}

	public function OnDefault()
	{
		if($this->TableName === ''){
			$this->BadRequest();
			exit;
		}
		
		//打开数据集
		global $Session;
		$DataSet = new TDataSet();
		$DataSet->CommandText = "select * from $this->TableName "
			. "where CorpCode_='$Session->CorpCode'";
		if($this->TableSort <> ''){
			$DataSet->CommandText .= ' ' . $this->TableSort;
		}
		$DataSet->Open();

		//显示数据集
		$grid = new TDBGrid($this);
		$grid->DataSet = $DataSet;
		$grid->Fields = $this->Fields;
		$grid->Show();
	}
	
	public function OnAppend(){
		$form = new TEditForm($this);
		$form->Caption = '增加记录';
		$form->AddHidden('mode', 'append');
		foreach($this->Fields as $code => $param){
			$fi = new TFieldInfo($code, $param);
			if($fi->isData and $fi->append){
				$edit = new $fi->Control($this);
				$edit->Window = $form;
				$edit->LinkField($code, $fi); 
			}
			$fi = null;
		}
		$form->Show();
	}

	public function OnModify(){
		$uid = $_GET['uid'];
		//打开数据集
		global $Session;
		$ds = new TDataSet();
		$ds->CommandText = "select * from $this->TableName "
			. "where CorpCode_='$Session->CorpCode' and UpdateKey_='$uid'";
		$ds->Open();
		if($ds->RecordCount() > 0){
			$ds->Next();
			$form = new TEditForm($this);
			$form->AddHidden('mode', 'modify');
			$form->AddHidden('uid', $uid);
			$form->DataSet = $ds;
			foreach($this->Fields as $code => $param){
				$fi = new TFieldInfo($code, $param);
				if($fi->isData and $fi->modify){
					$edit = new $fi->Control($this);
					$edit->Window = $form;
					$edit->DataSet = $ds;
					$edit->LinkField($code, $fi); 
				}
				$fi = null;
			}
			$form->Show();
		}else{
			echo "<p>Bad Request</p>\n";
		}
	}

	public function OnPostAppend(){
		//todo: 请在加入申请增加数据保存的代码
		global $Session;
		$rec = new TPostRecord($this->TableName);
		foreach($this->Fields as $field => $params){
			$fi = new TFieldInfo($field, $params);
			if($fi->isData and $fi->append){
				if($fi->Control === 'TCheckBox'){
					$rec->__set($field, isset($_POST[$field]) ? 1 : 0);
				}else{
					$rec->__set($field, isset($_POST[$field])? $_POST[$field] : null);
				}
			}elseif($field === 'UpdateUser_'){
				$rec->__set($field, $Session->UserCode);
			}elseif($field === 'UpdateDate_'){
				$rec->__set($field, 'Now()');
			}elseif($field === 'AppUser_'){
				$rec->__set($field, $Session->UserCode);
			}elseif($field === 'AppDate_'){
				$rec->__set($field, 'Now()');
			}elseif($field === 'UpdateKey_'){
				$rec->__set($field, 'UUID()');
			}
		}
		$rec->PostAppend();
		$this->OnDefault();
	}

	public function OnPostModify(){
		//todo: '请在加入申请修改数据保存的代码';
		if(!isset($_POST['uid'])){
			echo '错误的调用方式：uid不允许为空！';
			exit;
		}
		global $Session;
		$rec = new TPostRecord($this->TableName);
		foreach($this->Fields as $field => $params){
			$fi = new TFieldInfo($field, $params);
			if($fi->isData and $fi->modify){
				if($fi->Control === 'TCheckBox'){
					$rec->__set($field, isset($_POST[$field]) ? 1 : 0);
				}elseif(isset($_POST[$field])){
					$rec->__set($field, $_POST[$field]);
				}
			}elseif($field === 'UpdateUser_'){
				$rec->__set($field, $Session->UserCode);
			}elseif($field === 'UpdateDate_'){
				$rec->__set($field, 'Now()');
			}elseif($field === 'UpdateKey_'){
				$rec->__set($field, 'UUID()');
			}
		}
		$uid = $_POST['uid'];
		$rec->PostModify("UpdateKey_='$uid'");
		$this->OnDefault();
	}

	public function OnDelete(){
		//todo: 请在加入显示申请删除数据的代码
		$uid = $_GET['uid'];
		$form = new TEditForm($this);
		$form->Caption = '请确认';
		$form->AddHidden('mode', 'delete');
		$form->AddHidden('uid', $uid);
		//显示要删除的内容
		$edt = new TEdit($this);
		$edt->Caption = '唯一标识';
		$edt->Text = $uid;
		$edt->ReadOnly = true;
		$edt->Window = $form;
		//再次确认
		$rb = new TRadioButtons($this);
		$rb->Name = 'confirm';
		$rb->Caption = '确认删除';
		$rb->Items['yes'] = '确认';
		$rb->Items['no'] = '取消';
		$rb->Value = 'no';
		$rb->Window = $form;
		$form->Show();
	}

	public function OnPostDelete(){
		//todo: 请在加入申请删除数据保存的代码
		$uid = isset($_POST['uid']) ? $_POST['uid'] : null;
		if($uid and ($_POST['confirm'] == 'yes')){
			global $Session;
			$sql = "delete from $this->TableName "
				. "where CorpCode_='$Session->CorpCode' and UpdateKey_='$uid'";
			$DataSet = new TDataSet();
			$DataSet->CommandText = $sql;
			$DataSet->Execute();
		}
		$this->OnDefault();
	}
	
	public function checkInput(){
		$result = true;
		foreach($this->Components as $obj){
			if(method_exists($obj, 'checkInput')){
				if($obj->AllowNull)
					$obj->checkInput();
				else
					$result = $obj->checkInput();
				if(!$result) break;
			}
		}
		return $result;
	}

	public function AddMenu($item){
		global $Mainface;
		if($Mainface){
			$Mainface->AddMenu($item);
		}
	}
	
	public function AddLine($value){
		$this->Lines[] = "<p>$value</p>\n";
	}
	
	public function TalkUser($User, $Subject, $Body = '', $TargetID = '')
	{
		$msg = new TSendMessage();
		$msg->Users[]  = $User;
		$msg->Subject  = $Subject;
		$msg->Body     = $Body; 
		$msg->TargetID = $TargetID;
		$msg->Execute();
		foreach($msg->Messages as $Message){
			$this->AddLine($Message);
		}
	}

	public function OP_GetText($DataSet, $field, $params){
		$uid = $DataSet->FieldByName('UpdateKey_');
		$url1 = "<a href=\"".$this->GetUrl(VIEW_MODIFY, 'uid='.$uid)."\">修改</a>";
		$url2 = "<a href=\"".$this->GetUrl(VIEW_DELETE, 'uid='.$uid)."\">删除</a>";
		return $url1 . ' ' . $url2;
	}
}
?>