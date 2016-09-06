<?php
if( !defined('IN') ) die('bad request');

class QBPerson extends TDBForm
{
	public function OnCreate(){
		parent::OnCreate();
		$this->Caption = '员工人事资料录入';
		global $Session;
		$this->TableName = 'QB_Person';
		$this->Fields['CorpCode_'] = array('Caption' => '公司',
			'view' => false, 'modify' => 'ReadOnly', 'append' => 'ReadOnly',
			'Value' => $Session->CorpCode);
		$this->Fields['DeptName_'] = array('Caption' => '部门名称');
		$this->Fields['PersonName_'] = array('Caption' => '员工姓名');
		$this->Fields['Province_'] = array('Caption' => '籍贯',
			'Hint' => '省市名称');
		$this->Fields['Phone_'] = array('Caption' => '手机号码');
		$this->Fields['InDate_'] = array('Caption' => '入职日期');
		$this->Fields['OutDate_'] = array('Caption' => '离职日期');
		$this->Fields['BankCode_'] = array('Caption' => '银行帐号');
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
		$this->AddMenu('快捷操作');
		$this->AddMenu(array($this->GetUrl(VIEW_APPEND),  '增加'));
		$this->AddMenu('薪资资料');
		$this->AddMenu(array('index.php?m=QBPerson', '人事档案录入'));
		$this->AddMenu(array('index.php?m=QBSalary', '员工薪资总表'));
	}
	
	public function OnDefault()
	{
		global $Session;
		//打开数据集
		$DataSet = new TDataSet();
		$DataSet->CommandText = "select * from $this->TableName "
			. "where CorpCode_='$Session->CorpCode' order by CorpCode_,DeptName_";
		$DataSet->Open();
		$rec = $DataSet->RecordCount();
		$this->DataSet = $DataSet;

		//显示数据集
		$grid = new TDBGrid($this);
		$grid->DataSet = $DataSet;
		$grid->Fields = $this->Fields;
		$grid->Show();
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
}
/*数据结构
CREATE TABLE IF NOT EXISTS `QB_Person` (
  `CorpCode_` varchar(10) NOT NULL COMMENT '公司代码',
  `DeptName_` varchar(30) NOT NULL,
  `PersonName_` varchar(30) DEFAULT NULL,
  `Province_` varchar(30) DEFAULT NULL,
  `Phone_` varchar(30) DEFAULT NULL,
  `InDate_` date DEFAULT NULL,
  `OutDate_` date DEFAULT NULL,
  `BankCode_` varchar(30) DEFAULT NULL,
  `Remark_` varchar(255) DEFAULT NULL,
  `UpdateUser_` varchar(30) NOT NULL,
  `UpdateDate_` datetime NOT NULL,
  `AppUser_` varchar(30) NOT NULL,
  `AppDate_` datetime NOT NULL,
  `UpdateKey_` varchar(36) NOT NULL,
  UNIQUE KEY `UpdateKey_` (`UpdateKey_`),
  KEY `CorpCode_` (`CorpCode_`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='员工人事档案表';
*/