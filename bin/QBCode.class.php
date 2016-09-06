<?php
if( !defined('IN') ) die('bad request');

class QBCode extends TDBForm
{
	public function __construct($Owner = null){
		$this->BadMainface = array('SaveToExcel'); //转XLS
		parent::__construct($Owner);
	}

	public function OnCreate(){
		parent::OnCreate();
		$this->Caption = '会计科目录入';
		global $Session;
		$this->TableName = 'QB_Code';
		$this->TableSort = 'order by CorpCode_,Code_';
		$this->Fields['CorpCode_'] = array('Caption' => '公司',
			'view' => false, 'modify' => 'ReadOnly', 'append' => 'ReadOnly',
			'Value' => $Session->CorpCode);
		$this->Fields['Class_'] = array('Caption' => '科目类别');
		$this->Fields['ParentCode_'] = array('Caption' => '上阶科目');
		$this->Fields['Code_'] = array('Caption' => '科目代码');
		$this->Fields['Name_'] = array('Caption' => '科目名称');
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
		$this->AddMenu('会计科目');
		$this->AddMenu(array($this->GetUrl(VIEW_APPEND),  '增加'));
		$this->AddMenu(array($this->GetUrl('BatchInit'), '导入默认值'));
		$this->AddMenu(array($this->GetUrl('CreatePHP'), '导出成PHP代码'));
		$this->AddMenu('基本设置');
		$this->AddMenu(array('index.php?m=QBCode', '会计科目定义'));
		$this->AddMenu(array('index.php?m=QBType', '交易类别定义'));
	}
	
	public function OnDefault(){
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
		echo "<p>工具：".BuildUrl($this->GetUrl('SaveToExcel'), "下载为XLS档案")."</p>";
	}
	
	public function SaveToExcel(){
		global $Session;
		$DataSet = new TDataSet($this);
		$DataSet->CommandText = "select * from $this->TableName "
			. "where CorpCode_='$Session->CorpCode' order by Code_";
		$DataSet->Open();
		$Titles = array('CorpCode_' => '公司别', 'ParentCode_' => '上级科目',
			'Code_' => '科目代码', 'Name_' => '科目名称',
			'Remark_' => '备注');
		$xls = new TEasyExcel();
		$text = $xls->LoadFromDataSet($DataSet, $Titles);
		$xls->SetDownload($Session->CorpCode.'会计科目明细.xls', $text);
	}
	
	public function OnDelete(){
		$uid = $_GET['uid'];
		$ds = new TDataSet();
		$ds->Open("select Code_,Name_ from QB_Code where UpdateKey_='$uid'");
		if($ds->Next()){
			if(!DBExists("select TBDate_ from QB_Book "
				. "where DrCode_='$ds->Code_' or CrCode_='$ds->Code_'")){
				//todo: 请在加入显示申请删除数据的代码
				$form = new TEditForm($this);
				$form->Caption = '请确认';
				$form->AddHidden('mode', 'delete');
				$form->AddHidden('uid', $uid);
				//显示要删除的内容
				$edt = new TEdit($this);
				$edt->Caption = '会计科目';
				$edt->Text = $ds->Name_;
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
			}else{
				echo "$ds->Name_[$ds->Code_] 已存在记帐记录，不允许删除！";
			}
		}else{
			echo 'bad request';
		}
	}

	public function CreatePHP(){ //导出PHP代码
		global $Session;
		$ds = new TDataSet();
		$ds->Open("select ParentCode_,Code_,Name_ from QB_Code "
			. "where CorpCode_='$Session->CorpCode' order by ParentCode_,Code_");
		echo '$Datas = array();' . "<br/>\n";
		while($ds->Next()){
			echo '$'."Datas[] = array('ParentCode_' => '$ds->ParentCode_',"
				. "'Code_' => '$ds->Code_', 'Name_' => '$ds->Name_');<br/>\n";
		}
	}
	
	public function BatchInit(){ //导入默认值
		$Datas = array();
		//资产
		$Datas[] = array('ParentCode_' => '1','Code_' => '1100', 'Name_' => '固定资产');
		$Datas[] = array('ParentCode_' => '1','Code_' => '1200', 'Name_' => '流动资产');
		$Datas[] = array('ParentCode_' => '1','Code_' => '1300', 'Name_' => '其它资产');
		//
		$Datas[] = array('ParentCode_' => '1100','Code_' => '1101', 'Name_' => '生产设备');
		$Datas[] = array('ParentCode_' => '1100','Code_' => '1102', 'Name_' => '办公设备');
		$Datas[] = array('ParentCode_' => '1200','Code_' => '1201', 'Name_' => '银行存款');
		$Datas[] = array('ParentCode_' => '1200','Code_' => '1202', 'Name_' => '现金');
		$Datas[] = array('ParentCode_' => '1200','Code_' => '1210', 'Name_' => '存货');
		$Datas[] = array('ParentCode_' => '1200','Code_' => '1221', 'Name_' => '应收帐款');
		$Datas[] = array('ParentCode_' => '1200','Code_' => '1222', 'Name_' => '应收借款');
		$Datas[] = array('ParentCode_' => '1210','Code_' => '1211', 'Name_' => '商品(贸易)');
		$Datas[] = array('ParentCode_' => '1210','Code_' => '1212', 'Name_' => '制成品(生产制造)');
		//负债
		$Datas[] = array('ParentCode_' => '2','Code_' => '2100', 'Name_' => '长期负债');
		$Datas[] = array('ParentCode_' => '2','Code_' => '2200', 'Name_' => '短期负债');
		$Datas[] = array('ParentCode_' => '2','Code_' => '2300', 'Name_' => '其它负债');
		//
		$Datas[] = array('ParentCode_' => '2200','Code_' => '2201', 'Name_' => '应付帐款');
		//业主权益
		$Datas[] = array('ParentCode_' => '3','Code_' => '3100', 'Name_' => '股本');
		$Datas[] = array('ParentCode_' => '3','Code_' => '3200', 'Name_' => '累计损益');
		//成本与费用
		$Datas[] = array('ParentCode_' => '4','Code_' => '4100', 'Name_' => '主营业务支出');
		$Datas[] = array('ParentCode_' => '4','Code_' => '4200', 'Name_' => '业务费用');
		$Datas[] = array('ParentCode_' => '4','Code_' => '4300', 'Name_' => '其它支出');
		$Datas[] = array('ParentCode_' => '4200','Code_' => '4201', 'Name_' => '餐饮费');
		$Datas[] = array('ParentCode_' => '4200','Code_' => '4202', 'Name_' => '礼品费');
		//收入
		$Datas[] = array('ParentCode_' => '5','Code_' => '5100', 'Name_' => '主营业务收入');
		$Datas[] = array('ParentCode_' => '5','Code_' => '5200', 'Name_' => '其它收入');
		//
		global $Session;
		foreach($Datas as $Item){
			$code = $Item['Code_'];
			$name = $Item['Name_'];
			if(!DBExists("select Code_ from QB_Code "
				. "where CorpCode_='$Session->CorpCode' and Code_='$code'")){
				$rec = new TPostRecord('QB_Code');
				$rec->CorpCode_=$Session->CorpCode;
				$rec->ParentCode_=$Item['ParentCode_'];
				$rec->Code_=$Item['Code_'];
				$rec->Name_=$Item['Name_'];
				if(array_key_exists('Remark_', $Item)){
					$rec->Remark_=$Item['Remark_'];
				}
				$rec->SystemFields = array('UpdateUser_', 'UpdateDate_', 'AppUser_', 'AppDate_', 'UpdateKey_');
				$rec->PostAppend();
				echo "会计科目 <b>$code - $name</b> 导入成功；<br/>\n";
			}
		}
		echo '导入完成！';
	}
}