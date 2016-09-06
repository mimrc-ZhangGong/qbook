<?php
if( !defined('IN') ) die('bad request');

class QBBook extends TDBForm
{
	public function OnCreate(){
		parent::OnCreate();
		$this->Caption = '会计凭证录入';
		global $Session;
		$this->TableName = 'QB_Book';
		$this->Fields['CorpCode_'] = array('Caption' => '公司',
			'view' => false, 'modify' => 'ReadOnly', 'append' => 'ReadOnly',
			'Value' => $Session->CorpCode);
		$this->Fields['TBDate_'] = array('Caption' => '记帐日期',
			'Value' => date('Y-m-d'));
		$this->Fields['Subject_'] = array('Caption' => '摘要');
		$this->Fields['DrCode_'] = array('Caption' => '借方科目',
			'Hint' => "按此<a href=\"?m=QBCode\" target=\"qbcode\">查看会计科目</a>",
			'OnGetText' => 'AccCode_GetText');
		$this->Fields['CrCode_'] = array('Caption' => '贷方科目',
			'Hint' => "按此<a href=\"?m=QBCode\" target=\"qbcode\">查看会计科目</a>",
			'OnGetText' => 'AccCode_GetText');
		$this->Fields['Amount_'] = array('Caption' => '金额');
		$this->Fields['Remark_'] = array('Caption' => '备注',
			'view' => false);
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
		$this->AddMenu('会计记帐');
		$this->AddMenu(array($this->GetUrl(VIEW_APPEND),  '增加记帐凭证'));
		$this->AddMenu('系统开帐');
		$this->AddMenu(array($this->GetUrl('InitBook'),  '系统开帐作业'));
	}

	public function InitBook(){ //系统开帐作业
		echo '此功能正在开发中，预计2012/2/5完成';
	}
	
	public function OnDefault(){
		//打开数据集
		global $Session;
		$DataSet = new TDataSet();
		$DataSet->CommandText = "select * from $this->TableName "
			. "where CorpCode_='$Session->CorpCode' "
			. "order by TBDate_,Subject_,Amount_";
		$DataSet->Open();
		$rec = $DataSet->RecordCount();
		$this->DataSet = $DataSet;

		//显示数据集
		$grid = new TDBGrid($this);
		$grid->DataSet = $DataSet;
		$grid->Fields = $this->Fields;
		$grid->Show();
	}
	/*
	public function OnDelete(){
		global $Session;
		$uid = $_GET['uid'];
		$sql = "delete from $this->TableName "
			. "where CorpCode_='$Session->CorpCode' and UpdateKey_='$uid'";
		ExecSQL($sql);
		$this->OnDefault();
	}
	*/
	public function OP_GetText($DataSet, $FieldCode, $FieldInfo){
		$uid = $DataSet->UpdateKey_;
		$url1 = BuildUrl($this->GetUrl(VIEW_MODIFY, "uid=$uid"), '修改');
		$url2 = BuildUrl($this->GetUrl(VIEW_DELETE, "uid=$uid"), '删除');
		return $url1 . ' ' . $url2;
	}

	public function AccCode_GetText($DataSet, $FieldCode, $FieldInfo){
		global $Session;
		$AccCode = $DataSet->FieldByName($FieldCode);
		$AccName = DBRead("select Name_ from QB_Code "
			. "where CorpCode_='$Session->CorpCode' and Code_='$AccCode'");
		return "$AccCode-$AccName";
	}
}