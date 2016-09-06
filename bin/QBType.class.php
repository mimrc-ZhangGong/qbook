<?php
if( !defined('IN') ) die('bad request');

class QBType extends TDBForm
{
	public function OnCreate(){
		parent::OnCreate();
		$this->Caption = '快速记帐类别录入';
		global $Session;
		$this->TableName = 'QB_Type';
		$this->TableSort = 'order by Class_,Title_';
		$this->Fields['CorpCode_'] = array('Caption' => '公司',
			'view' => false, 'modify' => 'ReadOnly', 'append' => 'ReadOnly',
			'Value' => $Session->CorpCode);
		$this->Fields['Class_'] = array('Caption' => '大分类',
			'view' => true, 'Value' => '默认分类',
			'Hint' => '如：主营业务、家庭开支');
		$this->Fields['Title_'] = array('Caption' => '子分类');
		$this->Fields['DrCode_'] = array('Caption' => '借方科目',
			'OnGetText' => 'AccCode_GetText');
		$this->Fields['CrCode_'] = array('Caption' => '贷方科目',
			'OnGetText' => 'AccCode_GetText');
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
		$this->AddMenu('交易类别定义');
		$this->AddMenu(array($this->GetUrl(0),  '交易类别列表'));
		$this->AddMenu(array($this->GetUrl(VIEW_APPEND),  '增加交易类别'));
		$this->AddMenu(array($this->GetUrl('ImportDefault'),  '导入类别-主营业务'));
		$this->AddMenu(array($this->GetUrl('CreatePHP'), '导出成PHP代码'));
		$this->AddMenu('基本设置');
		$this->AddMenu(array('index.php?m=QBCode', '会计科目定义'));
		$this->AddMenu(array('index.php?m=QBType', '交易类别定义'));
	}
	
	public function CreatePHP(){ //导出成PHP代码
		global $Session;
		$ds = new TDataSet();
		$ds->Open("select Class_,Title_,DrCode_,CrCode_,Remark_ from QB_Type "
			. "where CorpCode_='$Session->CorpCode' order by Class_,Title_");
		echo '$Datas = array();' . "<br/>\n";
		while($ds->Next()){
			echo '$'."Datas[] = array("
				. "'Class_' => '$ds->Class_',"
				. "'Title_' => '$ds->Title_',"
				. "'DrCode_' => '$ds->DrCode_',"
				. "'CrCode_' => '$ds->CrCode_',"
				. "'Remark_' => '$ds->Remark_'"
				. ");<br/>\n";
		}
	}

	public function ImportDefault(){ //导入类别-主营业务
		$Datas = array();
		$Datas[] = array('Class_' => '主营业务','Title_' => '产品销售收入','DrCode_' => '1202','CrCode_' => '5100','Remark_' => '');
		$Datas[] = array('Class_' => '主营业务','Title_' => '现金进货','DrCode_' => '1211','CrCode_' => '1202','Remark_' => '');
		$Datas[] = array('Class_' => '主营业务','Title_' => '请客户吃饭','DrCode_' => '4201','CrCode_' => '1202','Remark_' => '');
		$Datas[] = array('Class_' => '主营业务','Title_' => '购办公用品','DrCode_' => '1102','CrCode_' => '1202','Remark_' => '');
		$Datas[] = array('Class_' => '主营业务','Title_' => '购生产设备','DrCode_' => '1101','CrCode_' => '1202','Remark_' => '');
		$Datas[] = array('Class_' => '主营业务','Title_' => '贷款','DrCode_' => '1201','CrCode_' => '2300','Remark_' => '');
		$Datas[] = array('Class_' => '主营业务','Title_' => '进货(以后付款)','DrCode_' => '1211','CrCode_' => '2201','Remark_' => '');
		//
		global $Session;
		foreach($Datas as $Item){
			$class = $Item['Class_'];
			$title = $Item['Title_'];
			if(!DBExists("select Title_ from QB_Type "
				. "where CorpCode_='$Session->CorpCode' and Title_='$title'")){
				$rec = new TPostRecord('QB_Type');
				$rec->CorpCode_=$Session->CorpCode;
				$rec->Class_=$Item['Class_'];
				$rec->Title_=$Item['Title_'];
				$rec->DrCode_=$Item['DrCode_'];
				$rec->CrCode_=$Item['CrCode_'];
				if(array_key_exists('Remark_', $Item)){
					$rec->Remark_=$Item['Remark_'];
				}
				$rec->SystemFields = array('UpdateUser_', 'UpdateDate_', 'AppUser_', 'AppDate_', 'UpdateKey_');
				$rec->PostAppend();
				echo "会计科目 <b>$class - $title</b> 导入成功；<br/>\n";
			}
		}
		echo '导入完成！';
	}
	
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