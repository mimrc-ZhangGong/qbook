<?php
if( !defined('IN') ) die('bad request');

class QBRecord extends TDBForm
{
	private $Edit3;
	private $Edit1;
	private $Edit2;

	public function OnCreate(){
		parent::OnCreate();
		$this->Caption = '快速记帐作业';
		global $Session;
		$this->TableName = 'QB_Record';
		$this->Fields['CorpCode_'] = array('Caption' => '公司',
			'view' => false, 'modify' => 'ReadOnly', 'Value' => $Session->CorpCode);
		$this->Fields['TBDate_'] = array('Caption' => '记帐日期',
			'modify' => 'ReadOnly', 'Value' => date('Y-m-d'));
		$this->Fields['Subject_'] = array('Caption' => '摘要',
			'Type' => 'TListBox',
			'Items' => array('1001' => '固定资产', '1002' => '流动资产'));
		$this->Fields['Amount_'] = array('Caption' => '金额',
			'Value' => '0');
		$this->Fields['ToBook_'] = array('Caption' => '记入总帐否',
			'modify' => 'ReadOnly', 'Value' => '0');
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
		$this->AddMenu(array($this->GetUrl(0),  '今日列表'));
		$this->AddMenu(array($this->GetUrl(VIEW_APPEND), '增加记录'));
		$this->AddMenu('其它操作');
		$this->AddMenu(array($this->GetUrl(VIEW_APPEND, '', 'QBType'), '增加记帐类别'));
		//$this->AddMenu(array($this->GetUrl(0, 'op=sendmb'),  '发送到手机上'));
		//
		$this->Edit3 = new TRadioButtons($this);
		$this->Edit3->Caption = '类别';
		//
		$param = $this->Fields['Subject_'];
		$this->Edit1 = new TEdit($this);
		$this->Edit1->Caption = $param['Caption'];
		$this->Edit1->AllowNull = true;
		$this->Edit1->Width = 30;
		//
		$param = $this->Fields['Amount_'];
		$this->Edit2 = new TEdit($this);
		$this->Edit2->Caption = $param['Caption'];
		$this->Edit2->Width = 8;
	}

	
	public function OnAppend(){
		global $Session;
		echo "<form method=\"post\" action=\"index.php?m=QBRecord\" enctype=\"multipart/form-data\">\n";
		echo "<p>\n";
		echo "<input type=\"hidden\" name=\"mode\" value=\"append\">\n";
		//
		$items = array();
		$DataSet = new TDataSet();
		$DataSet->CommandText = "select Class_,Title_ from QB_Type "
			. "where CorpCode_='$Session->CorpCode' order by Class_,Title_";
		$DataSet->Open();
		while($DataSet->Next()){
			$items[$DataSet->Class_][] = $DataSet->Title_;
		}
		foreach($items as $group => $lines){
			echo "<p>$group:\n";
			foreach($lines as $line){
				echo "<input type=\"radio\" name=\"RadioButtons1\" value=\"$line\" >$line</input>\n";
			}
			echo "</p>\n";
		}
		//
		echo "<p>暂无分类: <input type=\"radio\" name=\"RadioButtons1\" value=\"0\" checked=\"checked\">";
		echo "其它类别(选择此项将不生成会计凭证记帐记录，须事后再予补充登记方可纳入财务报表)\n";
		echo "<p>备注说明：" . $this->Edit1->GetHtmlText() . "\n";
		echo " 金额: " . $this->Edit2->GetHtmlText() . "\n";
		echo "<input type=\"submit\" value=\"提交\" name=\"B1\"/>\n";
		echo "<input type=\"reset\" value=\"重置\" name=\"B2\"/></p>\n";
		echo "</form>\n";
	}

	public function OnPostAppend(){
		//todo: 请在加入申请增加数据保存的代码
		$this->Edit3->checkInput();
		if($this->checkInput()){
			global $Session;
			$rec = new TPostRecord('QB_Record');
			$rec->CorpCode_ = $Session->CorpCode;
			$rec->TBDate_ = date('Y-m-d');
			$rec->Amount_ = $this->Edit2->Text;
			if($this->Edit3->Value <> '0'){
				if($this->Edit1->Text <> '')
					$rec->Subject_ = $this->Edit3->Value . '-' . $this->Edit1->Text;
				else
					$rec->Subject_ = $this->Edit3->Value;
				$this->AppendToBook($rec, $this->Edit3->Value);
				$rec->ToBook_ = 1;
			}else{
				$rec->Subject_ = $this->Edit1->Text;
			}
			$rec->SystemFields = array('UpdateUser_', 'UpdateDate_', 'AppUser_', 'AppDate_', 'UpdateKey_');
			$rec->PostAppend();
			$this->OnDefault();
		}else{
			echo '您的输入有误！';
		}
	}
	
	public function AppendToBook($Record, $Title){
		global $Session;
		$tpl = new TDataSet();
		$tpl->CommandText = "select DrCode_,CrCode_ from QB_Type "
			. "where CorpCode_='$Session->CorpCode' and Title_='$Title'";
		$tpl->Open();
		if($tpl->Next()){
			$rec = new TPostRecord('QB_Book');
			$rec->CorpCode_ = $Session->CorpCode;
			$rec->TBDate_ = date('Y-m-d');
			$rec->Subject_ = $Record->Subject_;
			$rec->DrCode_ = $tpl->DrCode_;
			$rec->CrCode_ = $tpl->CrCode_;
			$rec->Amount_ = $Record->Amount_;
			$rec->SystemFields = array('UpdateUser_', 'UpdateDate_', 'AppUser_', 'AppDate_', 'UpdateKey_');
			$rec->PostAppend();
			$Record->ToBook_=1;
		}
	}

	public function AccCode_GetText($DataSet, $FieldCode, $FieldInfo){
		global $Session;
		$AccCode = $DataSet->FieldByName($FieldCode);
		$AccName = DBRead("select Name_ from QB_Code "
			. "where CorpCode_='$Session->CorpCode' and Code_='$AccCode'");
		if(!$AccName) $AccName = '';
		return "$AccCode-$AccName";
	}
}