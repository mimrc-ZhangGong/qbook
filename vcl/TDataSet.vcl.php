<?php
if( !defined('IN') ) die('bad request');

class TDataSet
{
	private $Connection;
	private $Current;

	public $RecordSet;
	public $CommandText;
	
	//构造函数
	public function TDataSet()
	{
		global $DBConnection;
		$this->Connection = $DBConnection;
	}
	//求最大行数
	public function RecordCount()
	{
		try {
			if($this->RecordSet){
				$num = mysql_num_rows($this->RecordSet);
				return($num);
			}
			else{
				echo '<p>RecordCount Error: ' . $this->CommandText . '</p>';
			}
		} catch (Exception $e){
			echo $e->getMessage();
			exit;
		}
	}
	//打开数据集
	public function Open($sql = '')
	{
		if($sql <> ''){
			$this->CommandText = $sql;
		}
		$this->RecordSet = mysql_query($this->CommandText, $this->Connection);
		if(!$this->RecordSet){
			echo '<p>'.mysql_error().'</p>';
			echo '<p>Open Error: ' . $this->CommandText . '</p>';
		}
	}
	//执行SQL指令
	public function Execute()
	{
		if(!mysql_query($this->CommandText)){
			//echo "$this->CommandText \n";
			echo mysql_error();
		}
	}
	//执行SQL指令
	public function ExecSQL($sql)
	{
		if(!mysql_query($sql))
			echo mysql_error();
	}
	//下一条记录
	public function Next()
	{
		$this->Current = mysql_fetch_array($this->RecordSet);
		return $this->Current;
	}
	//取得字段数目
	public function FieldCount()
	{
		return(mysql_num_fields($this->RecordSet));
	}
	//根据字段索引取得字段名称
	public function getFieldName($index)
	{
		return(mysql_field_name($this->RecordSet, $index));
	}
	//根据字段索引取得字段值
	public function FieldByIndex($index)
	{
		$fd = $this->getFieldName($index);
		return($this->Current[$fd]);
	}
	//根据字段名称取得字段值
	public function FieldByName($fieldname)
	{
		return($this->Current[$fieldname]);
	}
	//支持以属性方式取得当前值
	public function __get($name){
		if(strpos($name, '_')){
			return($this->Current[$name]);
		}
	}
}
?>