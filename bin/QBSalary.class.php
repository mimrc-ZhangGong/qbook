<?php
if( !defined('IN') ) die('bad request');

class QBSalary extends TDBForm
{
	public function OnCreate(){
		parent::OnCreate();
		$this->Caption = '员工薪资总表';
		global $Session;
		$this->TableName = 'QB_Salary';
		$this->Fields['CorpCode_'] = array('Caption' => '公司',
			'view' => false, 'modify' => 'ReadOnly', 'Value' => $Session->CorpCode);
		$this->Fields['YearMonth_'] = array('Caption' => '发薪年月');
		$this->Fields['DeptName_'] = array('Caption' => '部门名称');
		$this->Fields['PersonName_'] = array('Caption' => '员工姓名',
			'OnGetText' => 'PersonName_GetText');
		$this->Fields['InDate_'] = array('Caption' => '入职日期');
		$this->Fields['OutDate_'] = array('Caption' => '离职日期');
		$this->Fields['BankCode_'] = array('Caption' => '银行帐号',
			'view' => false);
		$this->Fields['Amount1_'] = array('Caption' => '应发薪资');
		$this->Fields['Amount2_'] = array('Caption' => '个税扣除');
		$this->Fields['Amount3_'] = array('Caption' => '社保扣除');
		$this->Fields['AddDiff_'] = array('Caption' => '其它扣补');
		$this->Fields['Amount_'] = array('Caption' => '实发薪资',
			'modify' => 'ReadOnly');
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
		$this->AddMenu(array($this->GetUrl('CreateMonth', 'Value='.date('Ym')), '生成指定月份薪资表'));
		$this->AddMenu('薪资资料');
		$this->AddMenu(array('index.php?m=QBPerson', '人事档案录入'));
		//
		$Month = date('Ym');
		$this->AddMenu(array('index.php?m=QBSalary', '员工薪资-' . $Month));
		$ds = new TDataSet();
		$ds->Open("select DISTINCT YearMonth_ from QB_Salary "
			. "where CorpCode_='$Session->CorpCode' and YearMonth_<>'$Month'");
		while($ds->Next()){
			$Month = $ds->YearMonth_;
			$this->AddMenu(array($this->GetUrl(0, "Month=$Month"), '员工薪资-' . $Month));
		}
	}
	
	public function OnDefault()
	{
		if(isset($_GET['Month'])){
			$Month = $_GET['Month'];
			$_SESSION['WorkMonth'] = $Month;
		}elseif(isset($_SESSION['WorkMonth'])){
			$Month = $_SESSION['WorkMonth'];
		}else{
			$Month = date('Ym');
		}
		echo "<p>$Month 薪资表如下：</p>\n";
		//打开数据集
		global $Session;
		$DataSet = new TDataSet();
		$DataSet->CommandText = "select * from $this->TableName "
			. "where CorpCode_='$Session->CorpCode' and YearMonth_='$Month'"
			. "order by DeptName_,PersonName_";
		$DataSet->Open();
		$this->ShowDataSet($DataSet);
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
	
	public function CreateMonth(){ //生成指定月份薪资表
		echo "<p><font color=\"red\">警告：若指定年月存在资料，则人事资料及银行帐号资料将会被覆盖！</font></p>\n";
		$form = new TEditForm($this);
		$form->AddHidden('a', 'OnCreateMonth');
		$month = new TEdit($this);
		$month->Text = date('Ym');
		$month->Caption = '指定年月';
		$month->Window = $form;
		$month->Name = 'Value';
		$form->Show();
	}
	
	public function OnCreateMonth(){ //生成指定月份薪资表
		global $Session;
		$Value = isset($_POST['Value']) ? $_POST['Value'] : null;
		if($Value){
			$ds = new TDataSet();
			$ds->Open("select * from QB_Person where CorpCode_='$Session->CorpCode'");
			while($ds->Next()){
				if(!DBExists("select * from QB_Salary "
					. "where CorpCode_='$Session->CorpCode' and YearMonth_='$Value' "
					. "and PersonName_='$ds->PersonName_'")){
					$rec = new TPostRecord('QB_Salary');
					$rec->CorpCode_=$ds->CorpCode_;
					$rec->YearMonth_=$Value;
					$rec->DeptName_=$ds->DeptName_;
					$rec->PersonName_=$ds->PersonName_;
					$rec->InDate_=$ds->InDate_;
					$rec->OutDate_=$ds->OutDate_;
					$rec->BankCode_=$ds->BankCode_;
					$rec->Remark_=$ds->Remark_;
					$rec->SystemFields = array('AppUser_', 'AppDate_', 'UpdateUser_', 'UpdateDate_', 'UpdateKey_');
					$rec->PostAppend();
				}else{
					$rec = new TPostRecord('QB_Salary');
					$rec->DeptName_=$ds->DeptName_;
					$rec->InDate_=$ds->InDate_;
					$rec->OutDate_=$ds->OutDate_;
					$rec->BankCode_=$ds->BankCode_;
					$rec->Remark_=$ds->Remark_;
					$rec->SystemFields = array('UpdateUser_', 'UpdateDate_', 'UpdateKey_');
					$rec->PostModify("CorpCode_='$Session->CorpCode' and YearMonth_='$Value' "
						. "and PersonName_='$ds->PersonName_'");
				}
			}
			echo $Value . '月份薪资表生成成功！';
		}else{
			$this->BadRequest();
		}
	}

	public function OnPostModify(){
		//todo: '请在加入申请修改数据保存的代码';
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
		//实发薪资 = 应发薪资 - 个税扣除 - 社保扣除
		$rec->Amount_ = $rec->Amount1_ - $rec->Amount2_ - $rec->Amount3_ + $rec->AddDiff_;
		$rec->PostModify("UpdateKey_='$uid'");
		$this->OnDefault();
	}
	
	public function PersonName_GetText($DataSet, $Field, $Param){
		$text = $DataSet->PersonName_;
		return BuildUrl($this->GetUrl('ViewPerson', "Person=$text"), $text);
	}
	
	public function ViewPerson(){ //显示一个人所有月份的工资
		$PersonName = isset($_GET['Person']) ? $_GET['Person'] : null;
		if($PersonName){
			echo "<p>$PersonName 的历史薪资记录：</p>\n";
			//打开数据集
			global $Session;
			$DataSet = new TDataSet();
			$DataSet->CommandText = "select * from $this->TableName "
				. "where CorpCode_='$Session->CorpCode' and PersonName_='$PersonName'"
				. "order by YearMonth_";
			$DataSet->Open();
			$rec = $DataSet->RecordCount();
			$this->DataSet = $DataSet;
			$this->ShowDataSet($DataSet);
			/*
			//显示数据集
			$grid = new TDBGrid($this);
			$grid->DataSet = $DataSet;
			$grid->Fields = $this->Fields;
			$grid->Show();
			*/
		}else{
			$this->BadRequest();
		}
	}
	
	private function ShowDataSet(TDataSet $DataSet){
		$args = array(array('发薪年月', '部门名称', '员工姓名', '入职日期', '离职日期',
			'应发薪资', '个税扣除', '社保扣除', '其它扣补', '实发薪资', '操作'));
		$total = array(0, 0, 0, 0, 0);
		while($DataSet->Next()){
			$args[] = array(); $line = count($args) - 1;
			$args[$line][] = $DataSet->YearMonth_;
			$args[$line][] = $DataSet->DeptName_;
			$args[$line][] = $this->PersonName_GetText($DataSet, 'PersonName_', $this->Fields['PersonName_']);
			$args[$line][] = $DataSet->InDate_;
			$args[$line][] = $DataSet->OutDate_;
			$args[$line][] = $DataSet->Amount1_;
			$args[$line][] = $DataSet->Amount2_;
			$args[$line][] = $DataSet->Amount3_;
			$args[$line][] = $DataSet->AddDiff_;
			$args[$line][] = $DataSet->Amount_;
			$args[$line][] = $this->OP_GetText($DataSet,null,null);
			$total[0] += $args[$line][5];
			$total[1] += $args[$line][6];
			$total[2] += $args[$line][7];
			$total[3] += $args[$line][8];
			$total[4] += $args[$line][9];
		}
		//显示汇总
		$args[] = array(); $line = count($args) - 1;
		$args[$line][] = '小计：';
		$args[$line][] = null;
		$args[$line][] = '共 ' . (count($args) - 2) . ' 笔';
		$args[$line][] = null;
		$args[$line][] = null;
		$args[$line][] = $total[0];
		$args[$line][] = $total[1];
		$args[$line][] = $total[2];
		$args[$line][] = $total[3];
		$args[$line][] = $total[4];
		$args[$line][] = null;
		//显示数据集
		$grid = new TDBGrid($this);
		$grid->OutArray($args);
		$grid->Show();
	}
}
/*数据结构
CREATE TABLE IF NOT EXISTS `QB_Salary` (
  `CorpCode_` varchar(10) NOT NULL COMMENT '公司代码',
  `YearMonth_` int NOT NULL,
  `DeptName_` varchar(30) NOT NULL,
  `PersonName_` varchar(30) DEFAULT NULL,
  `InDate_` date DEFAULT NULL,
  `OutDate_` date DEFAULT NULL,
  `BankCode_` varchar(30) DEFAULT NULL,
  `Amount_` float(18,4) DEFAULT NULL,
  `Remark_` varchar(255) DEFAULT NULL,
  `UpdateUser_` varchar(30) NOT NULL,
  `UpdateDate_` datetime NOT NULL,
  `AppUser_` varchar(30) NOT NULL,
  `AppDate_` datetime NOT NULL,
  `UpdateKey_` varchar(36) NOT NULL,
  UNIQUE KEY `UpdateKey_` (`UpdateKey_`),
  KEY `CorpCode_` (`CorpCode_`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='员工薪资总表';
*/