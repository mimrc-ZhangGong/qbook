<?php
if( !defined('IN') ) die('bad request');

class TCheckList extends TWinControl{
    public $ListCode;
    public $Caption;
    public $DataSet;
    private $Checked = false;
    public $ReadOnly = false;
    public $Value = '';
    private $Items = array();

    public function __set($name, $value){
        parent::__set($name, $value);
        if($name === 'Checked'){
            $this->Checked = $value;
        }else{
            parent::__set($name, $value);
        }
    }

    public function __get($name){
        if($name === 'Checked'){
            return $this->Checked;
        }elseif($name === 'HtmlText'){
            return $this->GetHtmlText();
        }else{
            return parent::__get($name);
        }
    }

    public function GetHtmlText(){
        $readonly = $this->ReadOnly ? " disabled" : "";
        $text = '<input type="checkbox"';
        $text .= ($this->Checked ? ' checked' : '') . $readonly;
        $text .= ' name="'.$this->Name.'"';
        $text .= ' id="' .$this->Name. '"';
        $text .= '>'.$this->Caption.'</input>&nbsp;';
        $text .= "<select name=\"$this->ListCode\" $readonly>\n";
        foreach($this->Items as $Val => $Text){
            $selected = '';
            if($this->Value == $Val){
                $selected = " selected";
            }
            $text .= "<option value=\"$Val\"$selected>$Text</option>\n";
        }
        $text .= "</select>&nbsp;&nbsp;\n";
        return $text;
    }

    public function LinkField($FieldCode, $ListCode, $FieldInfo){
        $this->Name = $FieldCode;
        $this->ListCode = $ListCode;
        $this->Caption = $FieldInfo->Caption;
        $this->Checked = $FieldInfo->Checked;
        $this->Value = $FieldInfo->Value;
        $this->Items = $FieldInfo->Items;
        if($this->DataSet){
            if(is_string($FieldInfo->modify)){
                $this->ReadOnly = $FieldInfo->modify == 'ReadOnly';
            }else{
                $this->ReadOnly = !$FieldInfo->modify;
            }
            if($this->DataSet->FieldByName($FieldCode) == 0)
                $this->Checked = false;
            else{
                $this->Checked = true;
            }
            $this->Value = $this->DataSet->FieldByName($ListCode);
        }else{
            if(is_string($FieldInfo->append)){
                $this->ReadOnly = $FieldInfo->append == 'ReadOnly';
            }else{
                $this->ReadOnly = !$FieldInfo->append;
            }
        }
    }
}
?>