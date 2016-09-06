<?php
if( !defined('IN') ) die('bad request');

class TFrmMain extends TDBForm
{
	public function checkLogin(){
		return true;
	}

	public function OnCreate()
	{
		parent::OnCreate();
		$this->Caption = '企业帐务通 V0.1';
		$this->Message = "欢迎使用本系统！";
		if(isLogin()){
			$this->AddMenu('工作微博');
			$this->AddMenu(array('?m=TWFDiary&a=OnAppend', '写新的微博'));
			$this->AddMenu(array('?m=TWFDiary', '查看我的微博'));
			$this->AddMenu(array('?m=TWFDiary&a=ViewUsers', '看看同事在忙什么'));
		}
		$this->AddMenu('友情链接');
		$this->AddMenu(array('http://appdocs.sinaapp.com', '云文档中心'));
		$this->AddMenu(array('http://knowall.sinaapp.com', '企业百事知'));
		$this->AddMenu('帮助文档');
		$this->AddMenu(array($this->GetUrl('Helpme', 'id=100005'), '系统简介'));
		$this->AddMenu(array($this->GetUrl('Helpme', 'id=100501'), '企业帐务通销售计划书'));
	}
	
	public function OnDefault()
	{
		global $Session;
		$ws = $this->Session;
		echo "<p>";
		echo "<font color=\"red\">系统重要提示：</font><br/>\n";
		echo "<p>";
		echo "1.目前本系统还在开发中，截止到2012/2/1，还有至少一半的功能项待完成。目前本系统仍处于开发与内部测试中！";
		echo "</p><p>";
		echo "2.为了更好地完善本系统，我们邀请10名企业成为VIP永久免费客户，有兴趣参与的企业，请使用QQ(1416960)与我们联络！<br/>\n";
		echo "</p><p>";
		echo "3.本系统的界面目前是未经美工处理的原始界面，后期会由专业美工进行美化，现会影响心情但不影响系统功能 :) <br/>\n";
		echo "</p><p>";
		echo "4.目前界面主要是满足手机平台使用，若您在手机上使用，会发现界面很漂亮！\n";
		echo "</p>";
		echo "<p>";
		echo "<font color=\"red\">您知道如何记帐吗？</font>举个例子(以下针对同一个客户)：<br/>\n";
		echo "<br/>";
		$args[] = array('记帐日期', '摘要', '收入', '支出', '结余');
		$args[] = array('2012-01-01', '银行存款', '', '', '50000');
		$args[] = array('2012-01-02', '进原材料', '', '40000', '10000');
		$args[] = array('2012-01-03', '购二手设备', '', '5500', '500');
		$args[] = array('2012-01-04', '销售加工后的产品', '63000', '', '63500');
		$args[] = array('2012-01-05', '请他吃饭', '', '400', '63100');
		$args[] = array('2012-01-06', '借钱给他', '', '6000', '57100');
		$Grid = new TDBGrid($this);
		$Grid->OutArray($args);
		$Grid->Show();
		echo "<br/>";
		echo "<font color=\"red\">请问您对上述交易，赚钱多少？</font><br/>\n";
		echo "<br/>";
		echo "是不是无法一眼看出？这就是传统流水记帐法的缺点！如果您面临此类困扰，那么本系统可以帮忙您解决！<br/>\n";
		echo "<br/>";
		echo "本系统采用现代会计手法，使用借贷观念进行记帐，基于会计准则，但不会让您面临望而生畏的财务专业知识门槛。<br/>\n";
		echo "<br/>";
		echo "<font color=\"red\">进入系统试试看，这或许就是您要的！</font><br/>\n";
		echo "<br/>";
		echo "测试帐号：user01, 密码：111111<br/>";
		echo "</p>";
	}
}
?>