<?php
class TWFDiary extends TDBForm
{ //工作日志管理
	private $Edit3;
	private $Edit1;
	private $Edit2;

	public function OnCreate(){
		parent::OnCreate();
		$this->Caption = '工作微博(日志)';
		global $Session;
		$this->TableName = 'WF_Diary';
		$this->Fields['CorpCode_'] = array('Caption' => '公司',
			'view' => false, 'modify' => 'ReadOnly', 'append' => 'ReadOnly',
			'Value' => $Session->CorpCode);
		$this->Fields['TBDate_'] = array('Caption' => '工作日期',
			'Value' => date('Y-m-d'));
		$this->Fields['AppDate_'] = array('Caption' => '建档日期',
			'view' => true, 'modify' => 'ReadOnly', 'append' => 'ReadOnly',
			'Value' => date('Y-m-d h:m'));
		$this->Fields['Contents_'] = array('Caption' => '工作内容',
			'Hint' => '<br/>最多输入内容255个汉字，否则会保存失败！',
			'Control' => 'TMemo');
		$this->Fields['UpdateUser_'] = array('Caption' => '更新人员',
			'view' => false, 'modify' => 'ReadOnly', 'append' => false);
		$this->Fields['UpdateDate_'] = array('Caption' => '更新日期',
			'view' => false, 'modify' => 'ReadOnly', 'append' => false);
		$this->Fields['AppUser_'] = array('Caption' => '建档人员',
			'view' => false, 'modify' => 'ReadOnly', 'append' => 'ReadOnly',
			'Value' => $Session->UserCode);
		$this->Fields['UpdateKey_'] = array('Caption' => '更新标识',
			'view' => false, 'modify' => false, 'append' => false);
		$this->Fields['OP'] = array('Caption' => '操作', 'isData' => false,
			'OnGetText' => 'OP_GetText');
		$this->AddMenu('工作微博');
		$this->AddMenu(array('?m=TWFDiary&a=OnAppend', '写新的微博'));
		$this->AddMenu(array('?m=TWFDiary', '查看我的微博'));
		//
		$ds = new TDataSet();
		$ds->Open("select distinct DeptName_ from WF_UserInfo "
			. "where CorpCode_='$Session->CorpCode' and Enabled_=1 "
			. "order by DeptName_");
		while($ds->Next()){
			$this->AddMenu(array($this->GetUrl('ViewUsers','Dept='.$ds->DeptName_,'TWFDiary'),
				'[' . $ds->DeptName_ . ']在忙什么'));
		}
		//$this->AddMenu(array($this->GetUrl(0, 'op=sendmb'),  '发送到手机上'));
		$this->AddMenu(array($this->GetUrl('AddressBook'),  '同事通讯录'));
		$this->AddMenu('帮助文档');
		$this->AddMenu(array($this->GetUrl('Helpme', 'id=100006'), '功能说明'));
	}
	
	public function OP_GetText($DataSet, $FieldCode, $FieldInfo){
		global $Session;
		if($DataSet->AppUser_ === $Session->UserCode){
			$uid = $DataSet->UpdateKey_;
			$url1 = BuildUrl($this->GetUrl(VIEW_MODIFY, "uid=$uid"), '修改');
			$url2 = BuildUrl($this->GetUrl(VIEW_DELETE, "uid=$uid"), '删除');
			return $url1 . ' ' . $url2;
		}else{
			return BuildUrl('#', '评论').'(暂无法使用)';
		}
	}
	
	public function OnDefault(){
		$this->ViewDiary();
	}
	
	public function ViewUsers(){ //查看所有的用户
		global $Session;
		$args = array();
		//$args[] = array('用户帐号', '用户姓名', '今天的微博', '操作');
		$ds = new TDataSet();
		$ds->CommandText = "select UserCode_,UserName_ from WF_UserInfo "
			. "where CorpCode_='$Session->CorpCode' and Enabled_=1";
		$dept = isset($_GET['Dept']) ? $_GET['Dept'] : null;
		if(($dept) and ($dept <> '')){
			$ds->CommandText .= " and DeptName_='$dept'";
		}
		$ds->Open();
		while($ds->Next()){
			$args[$ds->UserCode_] = array($ds->UserCode_, $ds->UserName_, '');
		}
		//搜索当天的微博
		$ds = null;
		//
		$today = isset($_GET['Day']) ? $_GET['Day'] : date('Y-m-d');
		echo $today.' 的微博('.BuildUrl($this->GetUrl('ViewUsers',
			'Day='.DateAdd('d', -1, $today)), '前一天').')：<hr/>';
		$ds = new TDataSet();
		$ds->CommandText = 'select AppUser_,Contents_ from WF_Diary';
		$ds->CommandText .= " where TBDate_='$today'";
		$ds->Open();
		while($ds->Next()){
			if(array_key_exists($ds->AppUser_, $args)){
				$args[$ds->AppUser_][2] .= $ds->Contents_ . '<br/>';
			}
		}
		//显示
		foreach($args as $Lines){
			echo "<p>";
			$url = $this->GetUrl('ViewDiary', "user=$Lines[0]");
			$url = BuildUrl($url, $Lines[1]);
			echo "<b>$url-$Lines[0]：</b><br/>";
			echo $Lines[2] <> '' ? "$Lines[2]" : '<font color="red">(无)</font>';
			echo "</p>";
		}
	}
	
	public function ViewDiary(){ //查看一个人所有人的工作微博
		global $Session;
		$user = isset($_GET['user']) ? $_GET['user'] : $Session->UserCode;

		//打开数据集
		$DataSet = new TDataSet();
		$DataSet->CommandText = "select * from $this->TableName "
			. "where AppUser_='$user' order by TBDate_ DESC";
		$DataSet->Open();
		$rec = $DataSet->RecordCount();
		$this->DataSet = $DataSet;

		//显示数据集
		$grid = new TDBGrid($this);
		$grid->DataSet = $DataSet;
		$grid->Fields = $this->Fields;
		$grid->Show();
	}
	
	public function AddressBook(){ //同事通讯录
		global $Session;
		$ds = new TDataSet();
		$ds->Open("select DeptName_,UserName_,SMSNo_,Email_,QQ_ from WF_UserInfo "
			. "where CorpCode_='$Session->CorpCode' and Enabled_=1 "
			. "order by DeptName_,UserCode_");
		$grid = new TDBGrid($this);
		$grid->DataSet = $ds;
		$grid->Fields['DeptName_'] = array('Caption' => '部门');
		$grid->Fields['UserName_'] = array('Caption' => '姓名');
		$grid->Fields['SMSNo_'] = array('Caption' => '手机');
		$grid->Fields['Email_'] = array('Caption' => '邮件');
		$grid->Fields['QQ_'] = array('Caption' => 'QQ');
		$grid->Show();
	}
}

/* 数据结构
CREATE TABLE IF NOT EXISTS `WF_Diary` (
  `CorpCode_` varchar(10) NOT NULL COMMENT '企业代码',
  `TBDate_` date NOT NULL,
  `Contents_` varchar(255) DEFAULT NULL,
  `UpdateUser_` varchar(30) NOT NULL,
  `UpdateDate_` datetime NOT NULL,
  `AppUser_` varchar(30) NOT NULL,
  `AppDate_` datetime NOT NULL,
  `UpdateKey_` varchar(36) NOT NULL,
  UNIQUE KEY `UpdateKey_` (`UpdateKey_`),
  KEY `CorpCode_` (`CorpCode_`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

*/
?>