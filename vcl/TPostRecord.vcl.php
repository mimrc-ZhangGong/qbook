<?php
if( !defined('IN') ) die('bad request');

class TPostRecord {
	private $Table = '';
	private $Fields = array();
	private $SystemFields = array('UpdateKey_');
	private $CommandText;
	
	public function __construct($table){
		$this->Table = $table;
	}

	public function SetField($name, $value){
		$this->Fields[$name] = $value;
	}
	
	public function __get($name){
		if(array_key_exists($name, $this->Fields)){
			return($this->Fields[$name]);
		}elseif($name == 'CommandText'){
			return $this->CommandText;
		}
	}
	
	public function __set($name, $value){
		if($name === 'SystemFields'){
			global $Session;
			foreach($value as $field){
				if($field === 'UpdateUser_')
					$this->Fields[$field] = $Session->UserCode;
				elseif($field === 'UpdateDate_')
					$this->Fields[$field] = 'NOW()';
				elseif($field === 'AppUser_')
					$this->Fields[$field] = $Session->UserCode;
				elseif($field === 'AppDate_')
					$this->Fields[$field] = 'NOW()';
				elseif($field === 'UpdateKey_')
					$this->Fields[$field] = 'UUID()';
				else
					die('Error System-Field: '. $field);
			}
		}else{
			$this->Fields[$name] = $value;
		}
	}
	
	public function PostAppend(){
		if($this->Table <> ''){
			$s1 = '';
			$s2 = '';
			global $Session;
			foreach($this->Fields as $field => $value){
				$s1 .= $field . ',';
				if(substr($value, strlen($value) - 2) == '()')
					$s2 .= $value . ',';
				else
					$s2 .= "'" . $value . "',";
			}
			$s1 = substr($s1, 0, strlen($s1) - 1);
			$s2 = substr($s2, 0, strlen($s2) - 1);
			$sql = "insert into $this->Table ($s1) values ($s2)";
            //echo $sql;
			$this->CommandText = $sql;
			return ExecSQL($sql);
		}else{
			die('Save error: $table name is null');
		}
	}
	
	public function PostModify($where = ''){
		if($this->Table <> ''){
			$s2 = '';
			if($where === ''){
				if(isset($_POST['uid']))
					$s2 = "UpdateKey_='$uid'";
			}else{
				$s2 = $where;
			}
			//
			if($s2 <> ''){
				$s1 = '';
				foreach($this->Fields as $field => $value){
					if(substr($value, strlen($value) - 2) == '()')
						$s1 .= "$field = $value,";
					else
						$s1 .= "$field = '$value',";
				}
				$s1 = substr($s1, 0, strlen($s1) - 1);
				$sql = "update $this->Table set $s1 where $s2";
				//echo $sql;
				$this->CommandText = $sql;
				return ExecSQL($sql);
			}else{
				die('Save ' . $this->Table . ' error: $where is null');
			}
		}else{
			die('Save error: $table name is null');
		}
	}
}
?>