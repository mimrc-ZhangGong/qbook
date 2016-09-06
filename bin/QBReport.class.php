<?php
class QBReport extends TDBForm
{
	private $ReportMonth;
	
	public function OnCreate(){
		parent::OnCreate();
		if(isset($_POST['ReportMonth'])){
			$this->ReportMonth = $_POST['ReportMonth'];
			$_SESSION['ReportMonth'] = $this->ReportMonth;
		}elseif(isset($_SESSION['ReportMonth'])){
			$this->ReportMonth = $_SESSION['ReportMonth'];
		}else{
			$this->ReportMonth = date('Ym');
			$_SESSION['ReportMonth'] = $this->ReportMonth;
		};
		$this->Caption = '记帐财务报表';
		$this->AddMenu('财务报表 - ' . $this->ReportMonth);
		$this->AddMenu(array($this->GetUrl('Report1'),  '资产负债表'));
		$this->AddMenu(array($this->GetUrl('Report4'),  '科目余额表'));
		//$this->AddMenu(array($this->GetUrl('Report6'),  '现金流量表'));
		$this->AddMenu(array($this->GetUrl('Report2'),  '期间损益表'));
		//$this->AddMenu(array($this->GetUrl('Report3'),  '业主权益变动表'));
		$this->AddMenu(array($this->GetUrl('Report7'),  '资产变更历史'));
	}
	
	public function OnDefault(){
		$month = new TEdit($this);
		$month->Caption = '报表年月';
		$month->Text = $this->ReportMonth;
		$month->Hint = '请输入您要查询的财务年月';
		$month->Name = 'ReportMonth';
		$form = new TEditForm($this);
		$form->AddHidden('a', 'UpdateReportMonth');
		$month->Window = $form;
		$form->Show();
		//$this->Report0();
	}
	
	public function UpdateReportMonth(){
		if(isset($_POST['ReportMonth'])){
			$this->ReportMonth = $_POST['ReportMonth'];
			$_SESSION['ReportMonth'] = $this->ReportMonth;
			echo "报表工作年月已设置为: $this->ReportMonth";
		}else{
			$this->BadRequest();
		}
	}
	
	public function Report7(){
		//资产负债表-交易汇总表
		$last = idate('t'); //求本月的总天数
		$args[] = array('类别', '1.资产', '2.负债', '3.权益', '4.支出', '5.收入');
		for($i = 1; $i <= $last; $i++)
			$args[] = array('2012/1/' . $i, 0,0,0,0,0);
		$args[++$last] = array('合计', 0,0,0,0,0);
		//
		global $Session;
		$ds = new TDataSet();
//		$CurDate = YMToDate($this->ReportMonth);
		$ds->CommandText = "select TBDate_,DrCode_,CrCode_,Amount_ from QB_Book"
			. " where CorpCode_='$Session->CorpCode'";
//			. " and TBDate_>='".MonthBof()."' and TBDate_<='".MonthEof(Date())."'";
		$ds->CommandText .= " order by TBDate_";
		$ds->Open();
		if($ds->RecordCount() == 0){
			echo '未找到本月的交易记录！';
			exit;
		}
		while($ds->Next()){
			$date = idate('d', strtotime($ds->TBDate_));
			$dr = intval(substr($ds->DrCode_, 0, 1));
			$cr = intval(substr($ds->CrCode_, 0, 1));
			if($cr <> $dr){
				//	1201-银行存款	5100-主营业务收入
				if(!isset($args[$date][$dr])) $args[$date][$dr] = 0;
				if(!isset($args[$date][$cr])) $args[$date][$cr] = 0;
				if($this->GetDC($dr))
					$args[$date][$dr] += $ds->Amount_;
				else
					$args[$date][$dr] -= $ds->Amount_;
				if(!$this->GetDC($cr))
					$args[$date][$cr] += $ds->Amount_;
				else
					$args[$date][$cr] -= $ds->Amount_;
				//
				for($i = 0; $i <= 5; $i++){
					if(!isset($args[$date][$i])) $args[$date][$i] = 0;
				}
				$args[$last][1] += $args[$date][1];
				$args[$last][2] += $args[$date][2];
				$args[$last][3] += $args[$date][3];
				$args[$last][4] += $args[$date][4];
				$args[$last][5] += $args[$date][5];
			}
		}
		//清除当日无交易的记录
		foreach($args as $date => $items){
			if($items[0] <> '合计'){
				if(($items[1] === 0) and ($items[2] === 0) and ($items[3] === 0)
					and ($items[4] === 0) and ($items[5] === 0))
					unset($args[$date]);
			}
		}
		$Grid = new TDBGrid($this);
		$Grid->OutArray($args);
		$Grid->Show();
		//进行汇总
		$a = $args[$last][1];
		$b = $args[$last][2];
		$d = $args[$last][5];
		$e = $args[$last][4];
		$f = $d - $e;
		$c = $args[$last][3] + $f;
		echo "<p>资产($a) = 负债($b) + 所有者权益($c)；收入($d) - 支出($e) = 本期损益($f)</p>\n";
	}
	
	public function Report1(){
		//资产负债表
		global $Session;
		$cs = new TDataSet();
		$cs->CommandText = "select distinct Class_ from QB_Code "
			. "where CorpCode_='$Session->CorpCode' order by 1";
		$cs->Open();
		while($cs->Next()){
			echo $cs->Class_;
		}
	}
	
	public function Report2(){
		$total1 = 0;
		$total2 = 0;
		//期间损益表
		$args = $this->GetAccAmount();
		$Codes = $this->GetCodes();
		echo "<p><b>本期收入明细：</b></p>\n";
		$line = 0;
		$lines[] = array('记帐年月', '会计科目', '科目名称' , '金额');
		foreach($args as $code => $amount){
			if(substr($code, 0, 1) == '5'){
				$line++;
				$lines[$line][0] = $this->ReportMonth;
				$lines[$line][1] = $code;
				$lines[$line][2] = array_key_exists($code, $Codes) ? $Codes[$code] : $code;
				$lines[$line][3] = $amount;
				$total1 += $amount;
			}
		}
		$line++;
		$lines[$line][0] = '收入小计';
		$lines[$line][1] = '';
		$lines[$line][2] = '';
		$lines[$line][3] = $total1;
		$Grid = new TDBGrid($this);
		$Grid->OutArray($lines);
		$Grid->Show();
		unset($lines);
		//
		echo "<p><b>本期支出明细：</b></p>\n";
		$line = 0;
		$lines[] = array('记帐年月', '会计科目', '科目名称' , '金额');
		foreach($args as $code => $amount){
			if(substr($code, 0, 1) == '4'){
				$line++;
				$lines[$line][0] = $this->ReportMonth;
				$lines[$line][1] = $code;
				$lines[$line][2] = array_key_exists($code, $Codes) ? $Codes[$code] : $code;
				$lines[$line][3] = $amount;
				$total2 += $amount;
			}
		}
		$line++;
		$lines[$line][0] = '支出小计';
		$lines[$line][1] = '';
		$lines[$line][2] = '';
		$lines[$line][3] = $total2;
		$Grid = new TDBGrid($this);
		$Grid->OutArray($lines);
		$Grid->Show();
		//
		$total = $total1 - $total2;
		echo "<p>$total1 元(收入) - $total2 元(支出) = $total 元(本期损益)</p>\n";
	}
	
	public function Report3(){
		//业主权益变动表
	}
	
	public function Report4(){
		//科目余额表
		$args = $this->GetAccAmount();
		$Codes = $this->GetCodes();
		$line = 0;
		$lines[] = array('记帐年月', '会计科目', '科目名称' , '金额');
		foreach($args as $code => $amount){
			$line++;
			$lines[$line][0] = $this->ReportMonth;
			$lines[$line][1] = BuildUrl($this->GetUrl('Report5', 'code='.$code), $code);
			$lines[$line][2] = array_key_exists($code, $Codes) ? $Codes[$code] : $code;
			$lines[$line][3] = $amount;
		}
		$Grid = new TDBGrid($this);
		$Grid->OutArray($lines);
		$Grid->Show();
	}
	
	public function Report5(){ //交易明细表
		if(isset($_GET['code'])){
			$code = $_GET['code'];
			$sql = "and ((DrCode_='".$_GET['code']."') or (CrCode_='".$_GET['code']."'))";
			//打开数据集
			global $Session;
			$DataSet = new TDataSet();
			$DataSet->CommandText = "select * from QB_Book "
				. "where CorpCode_='$Session->CorpCode'$sql "
				. "order by TBDate_";
			$DataSet->Open();
			$i = 0;
			$total = array(0,0,0);
			$data[] = array('选择', '记帐日期', '摘要', '借方科目', '借方金额', '贷方科目', '贷方金额', '科目余额');
			while($DataSet->Next()){
				$i++;
				$data[$i][] = '<input type="checkbox" name="uid[]" value="'.$DataSet->UpdateKey_.'">';
				$data[$i][] = $DataSet->TBDate_;
				$data[$i][] = BuildUrl($this->GetUrl(VIEW_MODIFY, 'uid='.$DataSet->UpdateKey_,'QBBook'), $DataSet->Subject_);
				if($DataSet->DrCode_ == $code){
					$data[$i][] = $code;
					$data[$i][] = $DataSet->Amount_;
					$data[$i][] = BuildUrl($this->GetUrl('Report5', 'code='.$DataSet->CrCode_), $DataSet->CrCode_);
					$data[$i][] = '';
					$total[0] += $DataSet->Amount_;
					$total[2] += $DataSet->Amount_;
				}else{
					$data[$i][] = BuildUrl($this->GetUrl('Report5', 'code='.$DataSet->DrCode_), $DataSet->DrCode_);
					$data[$i][] = '';
					$data[$i][] = $code;
					$data[$i][] = $DataSet->Amount_;
					$total[1] += $DataSet->Amount_;
					$total[2] -= $DataSet->Amount_;
				}
				$data[$i][] = $total[2];
			}
			$i++;
			$data[$i][] = '';
			$data[$i][] = '合计：';
			$data[$i][] = '';
			$data[$i][] = '';
			$data[$i][] = $total[0];
			$data[$i][] = '';
			$data[$i][] = $total[1];
			$data[$i][] = $total[2];
			//显示数据集
			$url = $this->GetUrl('BatchUpdate');
			echo '<form method="post" action="'.$url.'" enctype="multipart/form-data">';
			echo '<input type="hidden" name="goback" value="'.$_SERVER['REQUEST_URI'].'">';
			$grid = new TDBGrid($this);
			$grid->OutArray($data);
			$grid->Show();
			echo '<p>';
			$edt = new TEdit($this);
			$edt->Name = 'UpdateDate';
			$edt->Text = date('Y-m-d');
			$edt->Width = 10;
			echo '修改日期为：', $edt->GetHtmlText();
			echo '<input type="submit" value="提交" name="B1">';
			echo '</form>';
			echo '</p>';
		}
	}
	
	public function BatchUpdate(){
		global $Session;
		$uid = $_POST['uid'];
		$date = $_POST['UpdateDate'];
		foreach($uid as $id){
			$sql = "update QB_Book set TBDate_='".$date."' "
				. "where CorpCode_='".$Session->CorpCode."' and UpdateKey_='".$id."'";
			ExecSQL($sql);
		}
		echo '修改成功，'.BuildUrl($_POST['goback'],'按此返回！');
	}
	
	private function GetCodes(){ //取得会计科目列表
		global $Session;
		$Codes = array();
		$ds = new TDataSet();
		$ds->CommandText = "select Code_,Name_ from QB_Code "
			. "where CorpCode_='$Session->CorpCode' order by Code_";
		$ds->Open();
		while($ds->Next()){
			$Codes[$ds->Code_] = $ds->Name_;
		}
		return $Codes;
	}
	
	private function GetAccAmount(){ //取得科目余额表
		global $Session;
		$args = array();
		$ds = new TDataSet();
		$ds->CommandText = "select DrCode_,CrCode_,Amount_ from QB_Book "
			. "where CorpCode_='$Session->CorpCode' order by TBDate_";
		$ds->Open();
		while($ds->Next()){
			$dr = intval(substr($ds->DrCode_, 0, 1));
			$cr = intval(substr($ds->CrCode_, 0, 1));
			if(!array_key_exists($ds->DrCode_, $args)) $args[$ds->DrCode_] = 0;
			if(!array_key_exists($ds->CrCode_, $args)) $args[$ds->CrCode_] = 0;
			if($this->GetDC($dr))
				$args[$ds->DrCode_] += $ds->Amount_;
			else
				$args[$ds->DrCode_] -= $ds->Amount_;
			if(!$this->GetDC($cr))
				$args[$ds->CrCode_] += $ds->Amount_;
			else
				$args[$ds->CrCode_] -= $ds->Amount_;
		}
		ksort($args);
		return $args;
	}
	
	private function GetDC($val){
		return ($val == 1) or ($val == 4) ? true : false;
	}
}
?>