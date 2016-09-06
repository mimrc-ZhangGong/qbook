<?php

class QBClass extends TDBForm{
	public function OnCreate(){
		parent::OnCreate();
		$this->Caption = '会计科目录入';
		global $Session;
		$this->TableName = 'QB_Class';
		$this->TableSort = 'order by CorpCode_,Code_';
		$this->Fields['CorpCode_'] = array('Caption' => '公司',
			'view' => false, 'modify' => 'ReadOnly', 'append' => 'ReadOnly',
			'Value' => $Session->CorpCode);
		$this->Fields['Code_'] = array('Caption' => '科目类别代码');
		$this->Fields['Name_'] = array('Caption' => '科目类别名称');
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
	}
}
/*
CREATE TABLE IF NOT EXISTS `QB_Class` (
  `Code_` varchar(10) NOT NULL COMMENT '科目类别',
  `Name_` varchar(30) NOT NULL COMMENT '类别名称',
  `Remark_` varchar(80) DEFAULT NULL,
  `UpdateUser_` varchar(10) NOT NULL,
  `UpdateDate_` datetime NOT NULL,
  `AppUser_` varchar(30) NOT NULL,
  `AppDate_` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdateKey_` varchar(36) NOT NULL,
  PRIMARY KEY (`Code_`),
  UNIQUE KEY `UpdateKey_` (`UpdateKey_`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/
?>
