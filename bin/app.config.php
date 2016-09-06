<?php
//其它配置文件
//require('users.config.php');
//require('mainmenu.config.php');

//以下变量为框架所必须，可修改但不允许删除
{
	//定义应用基本讯息
	global $appid, $appname, $copyright, $website;
	$appid = 'knowall';
	$appname = '企业帐务通';
	$copyright = '深圳市华软资讯科技有限公司';
	$website = 'qbook.sinaapp.com';

	//定义系统管理员邮件地址
	global $admin_email; 
	$admin_email = '1416960@qq.com';

	//定义用户管理器
	global $users_class;
	$users_class = 'TWFUsers';
	
	//定义主菜单
	global $mainmenu_class;
	$mainmenu_class = 'mainmenu';

	//定义PV统计工具代码，将显示于首页下方
	global $PV_TOTAL;
	$PV_TOTAL = "<script src=\"http://s13.cnzz.com/stat.php?id=3789449&web_id=3789449\" language=\"JavaScript\"></script>";
}

//可在此定义app专用变量


class mainmenu
{
	public function getItems(){
		global $Session;
		//定义主菜单
		$mainmenu = array(array('index.php', '首 页'));
		if(!$Session->Login){
			$mainmenu[] = array('index.php?m=TFrmLogin', '用户登入');
			//$mainmenu[] = array('http://appdocs.sinaapp.com/helpme.php?id=100003', '系统简介');
		}
		else{
			if(uLevel() <= 2){ //普通用户
				$mainmenu[] = array('?m=QBRecord', '快速记帐');
			}
			if(uLevel() <= 1){ //企业管理员
				$mainmenu[] = array('?m=QBBook', '会计记帐');
			}
			if(uLevel() <= 2){ //普通用户
				$mainmenu[] = array('?m=QBReport', '财务报表');
			}
			if(uLevel() <= 1){ //企业管理员
				$mainmenu[] = array('?m=QBCode', '基本设置');
				$mainmenu[] = array('?m=QBPerson', '薪资管理');
				$mainmenu[] = array('?m=userview', '企业用户');
			}
			if(uLevel() === 0){ //系统管理员
				$mainmenu[] = array('?m=TWFCusList', '所有企业');
				//$mainmenu[] = array('?m=userlist', '所有用户');
				if(!OnSAE()){
					$mainmenu[] = array('http://localhost/phpmyadmin/', 'MySQL管理');
				}
			}
			$mainmenu[] = array('?m=myset', '我的设置');
			$mainmenu[] = array('index.php?logout', '退出系统');
		}
		return $mainmenu;
	}
}
?>