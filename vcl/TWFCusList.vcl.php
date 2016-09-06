<?php
if( !defined('IN') ) die('bad request');

class TWFCusList extends TDBForm
{

	public function checkLogin(){
		if(uLevel() > 0){
			return false;
		}else{
			return parent::checkLogin();
		}
	}

	public function OnCreate(){
		parent::OnCreate();
		$this->Caption = '企业登记总表';
		global $Session;
		$this->TableName = 'WF_CusList';
		$this->TableSort = 'order by Code_';
		$this->Fields['Code_'] = array('Caption' => '公司代码',
			'OnGetText' => 'Code_GetText');
		$this->Fields['ShortName_'] = array('Caption' => '公司简称');
		$this->Fields['Name_'] = array('Caption' => '公司全称');
		$this->Fields['RegDate_'] = array('Caption' => '注册时间',
			'Value' => date('Ym'));
		$this->Fields['Remark_'] = array('Caption' => '备注');
		$this->Fields['UpdateUser_'] = array('Caption' => '更新人员',
			'view' => false, 'modify' => 'ReadOnly', 'append' => false);
		$this->Fields['UpdateDate_'] = array('Caption' => '更新日期',
			'view' => false, 'modify' => 'ReadOnly', 'append' => false);
		$this->Fields['AppUser_'] = array('Caption' => '建档人员',
			'view' => false, 'modify' => 'ReadOnly', 'append' => 'ReadOnly',
			'Value' => $Session->UserCode);
		$this->Fields['AppDate_'] = array('Caption' => '建档日期',
			'view' => false, 'modify' => 'ReadOnly', 'append' => 'ReadOnly',
			'Value' => date('Y-m-d h:m'));
		$this->Fields['UpdateKey_'] = array('Caption' => '更新标识',
			'view' => false, 'modify' => false, 'append' => false);
		$this->Fields['OP'] = array('Caption' => '操作', 'isData' => false,
			'OnGetText' => 'OP_GetText');
		$this->AddMenu('企业列表');
		$this->AddMenu(array($this->GetUrl(VIEW_APPEND),  '增加'));
		$this->AddMenu(array($this->GetUrl(0,'','userlist'),  '所有系统用户'));
		//$this->AddMenu(array($this->GetUrl('UpdateCusCode'),  '修改公司编号'));
	}
	
	public function UpdateCusCode(){
		$args = array('demo' => '120000',
			'MIMRC' => '120001',
			'HSB' => '120003',
			'HENGNU' => '120004');
		$sl = new TStringList();
		foreach($args as $old => $new){
			$sl->Add("update QB_Book set CorpCode_='$new' where CorpCode_='$old'");
			$sl->Add("update QB_Code set CorpCode_='$new' where CorpCode_='$old'");
			$sl->Add("update QB_CusList set Code_='$new' where Code_='$old'");
			$sl->Add("update QB_Person set CorpCode_='$new' where CorpCode_='$old'");
			$sl->Add("update QB_Record set CorpCode_='$new' where CorpCode_='$old'");
			$sl->Add("update QB_Salary set CorpCode_='$new' where CorpCode_='$old'");
			$sl->Add("update QB_Type set CorpCode_='$new' where CorpCode_='$old'");
			$sl->Add("update WF_Diary set CorpCode_='$new' where CorpCode_='$old'");
			$sl->Add("update WF_UserInfo set CorpCode_='$new' where CorpCode_='$old'");
		}
		for($i = 0; $i < $sl->Count - 1; $i++){
			echo $sl->Strings($i) . ";<br/>\n";
		}
		echo '全部执行完成！';
	}
	
	public function OnDefault()
	{
		//打开数据集
		global $Session;
		$DataSet = new TDataSet();
		$DataSet->CommandText = "select * from $this->TableName";
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
			. "where UpdateKey_='$uid'";
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

	public function OnPostDelete(){
		//todo: 请在加入申请删除数据保存的代码
		$uid = isset($_POST['uid']) ? $_POST['uid'] : null;
		if($uid and ($_POST['confirm'] == 'yes')){
			global $Session;
			$sql = "delete from $this->TableName "
				. "where UpdateKey_='$uid'";
			$DataSet = new TDataSet();
			$DataSet->CommandText = $sql;
			$DataSet->Execute();
		}
		$this->OnDefault();
	}
	
	public function Code_GetText($DataSet, $Field, $FieldInfo){
		$code = $DataSet->Code_;
		return BuildUrl("?m=userlist&CusCode=$code", $code);
	}
}
/*数据结构
CREATE TABLE IF NOT EXISTS `QB_CusList` (
  `Code_` varchar(10) NOT NULL COMMENT '企业代码',
  `Name_` varchar(80) NOT NULL,
  `Remark_` varchar(255) DEFAULT NULL,
  `UpdateUser_` varchar(30) NOT NULL,
  `UpdateDate_` datetime NOT NULL,
  `AppUser_` varchar(30) NOT NULL,
  `AppDate_` datetime NOT NULL,
  `UpdateKey_` varchar(36) NOT NULL,
  UNIQUE KEY `UpdateKey_` (`UpdateKey_`),
  KEY `Code_` (`Code_`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='企业代码表';
*/