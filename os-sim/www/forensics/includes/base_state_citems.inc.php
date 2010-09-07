<?php
/**
* Class and Function List:
* Function list:
* - BaseCriteria()
* - Init()
* - Import()
* - Clear()
* - Sanitize()
* - SanitizeElement()
* - PrintForm()
* - AddFormItem()
* - GetFormItemCnt()
* - SetFormItemCnt()
* - Set()
* - Get()
* - ToSQL()
* - Description()
* - isEmpty()
* - Import()
* - Sanitize()
* - GetFormItemCnt()
* - Set()
* - Get()
* - isEmpty()
* - MultipleElementCriteria()
* - Init()
* - Import()
* - Sanitize()
* - SanitizeElement()
* - GetFormItemCnt()
* - SetFormItemCnt()
* - AddFormItem()
* - Set()
* - Get()
* - isEmpty()
* - PrintForm()
* - Compact()
* - SanitizeElement()
* - Description()
* - SignatureCriteria()
* - Init()
* - Import()
* - Clear()
* - SanitizeElement()
* - PrintForm()
* - ToSQL()
* - Description()
* - Init()
* - Clear()
* - SanitizeElement()
* - PrintForm()
* - ToSQL()
* - Description()
* - Init()
* - Clear()
* - SanitizeElement()
* - PrintForm()
* - ToSQL()
* - Description()
* - Init()
* - Clear()
* - SanitizeElement()
* - PrintForm()
* - ToSQL()
* - Description()
* - Init()
* - Clear()
* - SanitizeElement()
* - PrintForm()
* - ToSQL()
* - Description()
* - Clear()
* - SanitizeElement()
* - PrintForm()
* - ToSQL()
* - Description()
* - IPAddressCriteria()
* - Import()
* - Clear()
* - SanitizeElement()
* - PrintForm()
* - ToSQL()
* - Description()
* - IPFieldCriteria()
* - PrintForm()
* - ToSQL()
* - Description()
* - TCPPortCriteria()
* - PrintForm()
* - ToSQL()
* - Description()
* - TCPFieldCriteria()
* - PrintForm()
* - ToSQL()
* - Description()
* - Init()
* - Clear()
* - SanitizeElement()
* - PrintForm()
* - ToSQL()
* - Description()
* - isEmpty()
* - UDPPortCriteria()
* - PrintForm()
* - ToSQL()
* - Description()
* - UDPFieldCriteria()
* - PrintForm()
* - ToSQL()
* - Description()
* - ICMPFieldCriteria()
* - PrintForm()
* - ToSQL()
* - Description()
* - Init()
* - Clear()
* - SanitizeElement()
* - PrintForm()
* - ToSQL()
* - Description()
* - DataCriteria()
* - Init()
* - Import()
* - Clear()
* - SanitizeElement()
* - PrintForm()
* - ToSQL()
* - Description()
* - Init()
* - Clear()
* - SanitizeElement()
* - PrintForm()
* - ToSQL()
* - Description()
* - Init()
* - Clear()
* - SanitizeElement()
* - PrintForm()
* - ToSQL()
* - Description()
* - Init()
* - Clear()
* - SanitizeElement()
* - PrintForm()
* - ToSQL()
* - Description()
* - Init()
* - Clear()
* - SanitizeElement()
* - PrintForm()
* - ToSQL()
* - Description()
* - Init()
* - Clear()
* - SanitizeElement()
* - PrintForm()
* - ToSQL()
* - Description()
* - Init()
* - Clear()
* - SanitizeElement()
* - PrintForm()
* - ToSQL()
* - Description()
* - Init()
* - Clear()
* - SanitizeElement()
* - PrintForm()
* - ToSQL()
* - Description()
* Classes list:
* - BaseCriteria
* - SingleElementCriteria extends BaseCriteria
* - MultipleElementCriteria extends BaseCriteria
* - ProtocolFieldCriteria extends MultipleElementCriteria
* - SignatureCriteria extends SingleElementCriteria
* - SignatureClassificationCriteria extends SingleElementCriteria
* - SignaturePriorityCriteria extends SingleElementCriteria
* - AlertGroupCriteria extends SingleElementCriteria
* - SensorCriteria extends SingleElementCriteria
* - TimeCriteria extends MultipleElementCriteria
* - IPAddressCriteria extends MultipleElementCriteria
* - IPFieldCriteria extends ProtocolFieldCriteria
* - TCPPortCriteria extends ProtocolFieldCriteria
* - TCPFieldCriteria extends ProtocolFieldCriteria
* - TCPFlagsCriteria extends SingleElementCriteria
* - UDPPortCriteria extends ProtocolFieldCriteria
* - UDPFieldCriteria extends ProtocolFieldCriteria
* - ICMPFieldCriteria extends ProtocolFieldCriteria
* - Layer4Criteria extends SingleElementCriteria
* - DataCriteria extends MultipleElementCriteria
* - OssimPriorityCriteria extends SingleElementCriteria
* - OssimRiskACriteria extends SingleElementCriteria
* - OssimRiskCCriteria extends SingleElementCriteria
* - OssimReliabilityCriteria extends SingleElementCriteria
* - OssimAssetSrcCriteria extends SingleElementCriteria
* - OssimAssetDstCriteria extends SingleElementCriteria
* - OssimTypeCriteria extends SingleElementCriteria
*/
/*******************************************************************************
** OSSIM Forensics Console
** Copyright (C) 2009 OSSIM/AlienVault
** Copyright (C) 2004 BASE Project Team
** Copyright (C) 2000 Carnegie Mellon University
**
** (see the file 'base_main.php' for license details)
**
** Built upon work by Roman Danyliw <rdd@cert.org>, <roman@danyliw.com>
** Built upon work by the BASE Project Team <kjohnson@secureideas.net>
**/
defined('_BASE_INC') or die('Accessing this file directly is not allowed.');
class BaseCriteria {
    var $criteria;
    var $export_name;
    var $db;
    var $cs;
    function BaseCriteria(&$db, &$cs, $name) {
        $this->db = & $db;
        $this->cs = & $cs;
        $this->export_name = $name;
        $this->criteria = NULL;
    }
    function Init() {
    }
    function Import() {
        /* imports criteria from POST, GET, or the session */
    }
    function Clear() {
        /* clears the criteria */
    }
    function Sanitize() {
        /* clean/validate the criteria */
    }
    function SanitizeElement() {
        /* clean/validate the criteria */
    }
    function PrintForm() {
        /* prints the HTML form to input the criteria */
    }
    function AddFormItem() {
        /* adding another item to the HTML form  */
    }
    function GetFormItemCnt() {
        /* returns the number of items in this form element  */
    }
    function SetFormItemCnt() {
        /* sets the number of items in this form element */
    }
    function Set($value) {
        /* set the value of this criteria */
    }
    function Get() {
        /* returns the value of this criteria */
    }
    function ToSQL() {
        /* convert this criteria to SQL */
    }
    function Description() {
        /* generate human-readable description of this criteria */
    }
    function isEmpty() {
        /* returns if the criteria is empty */
    }
};
class SingleElementCriteria extends BaseCriteria {
    function Import() {
        $this->criteria = SetSessionVar($this->export_name);
        $_SESSION[$this->export_name] = & $this->criteria;
    }
    function Sanitize() {
    	$this->SanitizeElement();
    }
    function GetFormItemCnt() {
        return -1;
    }
    function Set($value) {
        $this->criteria = $value;
    }
    function Get() {
        return $this->criteria;
    }
    function isEmpty() {
        if ($this->criteria == "") return true;
        else return false;
    }
};
class MultipleElementCriteria extends BaseCriteria {
    var $element_cnt;
    var $criteria_cnt;
    var $valid_field_list = Array();
    function MultipleElementCriteria(&$db, &$cs, $export_name, $element_cnt, $field_list = Array()) {
        $tdb = & $db;
        $cs = & $cs;
        $this->BaseCriteria($tdb, $cs, $export_name);
        $this->element_cnt = $element_cnt;
        $this->criteria_cnt = 0;
        $this->valid_field_list = $field_list;
    }
    function Init() {
        InitArray($this->criteria, $GLOBALS['MAX_ROWS'], $this->element_cnt, "");
        $this->criteria_cnt = 1;
        $_SESSION[$this->export_name . "_cnt"] = & $this->criteria_cnt;
    }
    function Import() {
        $this->criteria = SetSessionVar($this->export_name);
        $this->criteria_cnt = SetSessionVar($this->export_name . "_cnt");
        $_SESSION[$this->export_name] = & $this->criteria;
        $_SESSION[$this->export_name . "_cnt"] = & $this->criteria_cnt;
    }
    function Sanitize() {
        if (in_array("criteria", array_keys(get_object_vars($this)))) {
            for ($i = 0; $i < $this->element_cnt; $i++) {
                if (isset($this->criteria[$i])) $this->SanitizeElement($i);
            }
        }
    }
    function SanitizeElement($i) {
    }
    function GetFormItemCnt() {
        return $this->criteria_cnt;
    }
    function SetFormItemCnt($value) {
        $this->criteria_cnt = $value;
    }
    function AddFormItem(&$submit, $submit_value) {
        $this->criteria_cnt = & $this->criteria_cnt;
        AddCriteriaFormRow($submit, $submit_value, $this->criteria_cnt, $this->criteria, $this->element_cnt);
    }
    function Set($value) {
        $this->criteria = $value;
    }
    function Get() {
        return $this->criteria;
    }
    function isEmpty() {
        if ($this->criteria_cnt == 0) return true;
        else return false;
    }
    function PrintForm($field_list, $blank_field_string, $add_button_string) {
        for ($i = 0; $i < $this->criteria_cnt; $i++) {
            if (!is_array($this->criteria[$i])) $this->criteria = array();
            echo '    <SELECT NAME="' . htmlspecialchars($this->export_name) . '[' . $i . '][0]">';
            echo '      <OPTION VALUE=" " ' . chk_select($this->criteria[$i][0], " ") . '>__</OPTION>';
            echo '      <OPTION VALUE="(" ' . chk_select($this->criteria[$i][0], "(") . '>(</OPTION>';
            echo '    </SELECT>';
            echo '    <SELECT NAME="' . htmlspecialchars($this->export_name) . '[' . $i . '][1]">';
            echo '      <OPTION VALUE=" "      ' . chk_select($this->criteria[$i][1], " ") . '>' . $blank_field_string . '</OPTION>';
            reset($field_list);
            foreach($field_list as $field_name => $field_human_name) {
                echo '   <OPTION VALUE="' . $field_name . '" ' . chk_select($this->criteria[$i][1], $field_name) . '>' . $field_human_name . '</OPTION>';
            }
            echo '    </SELECT>';
            echo '    <SELECT NAME="' . htmlspecialchars($this->export_name) . '[' . $i . '][2]">';
            echo '      <OPTION VALUE="="  ' . chk_select($this->criteria[$i][2], "=") . '>=</OPTION>';
            echo '      <OPTION VALUE="!=" ' . chk_select($this->criteria[$i][2], "!=") . '>!=</OPTION>';
            echo '      <OPTION VALUE="<"  ' . chk_select($this->criteria[$i][2], "<") . '><</OPTION>';
            echo '      <OPTION VALUE="<=" ' . chk_select($this->criteria[$i][2], "<=") . '><=</OPTION>';
            echo '      <OPTION VALUE=">"  ' . chk_select($this->criteria[$i][2], ">") . '>></OPTION>';
            echo '      <OPTION VALUE=">=" ' . chk_select($this->criteria[$i][2], ">=") . '>>=</OPTION>';
            echo '    </SELECT>';
            echo '    <INPUT TYPE="text" NAME="' . htmlspecialchars($this->export_name) . '[' . $i . '][3]" SIZE=5 VALUE="' . $this->criteria[$i][3] . '">';
            echo '    <SELECT NAME="' . htmlspecialchars($this->export_name) . '[' . $i . '][4]">';
            echo '      <OPTION VALUE=" " ' . chk_select($this->criteria[$i][4], " ") . '>__</OPTION';
            echo '      <OPTION VALUE="(" ' . chk_select($this->criteria[$i][4], "(") . '>(</OPTION>';
            echo '      <OPTION VALUE=")" ' . chk_select($this->criteria[$i][4], ")") . '>)</OPTION>';
            echo '    </SELECT>';
            echo '    <SELECT NAME="' . htmlspecialchars($this->export_name) . '[' . $i . '][5]">';
            echo '      <OPTION VALUE=" "   ' . chk_select($this->criteria[$i][5], " ") . '>__</OPTION>';
            echo '      <OPTION VALUE="OR" ' . chk_select($this->criteria[$i][5], "OR") . '>' . _OR . '</OPTION>';
            echo '      <OPTION VALUE="AND" ' . chk_select($this->criteria[$i][5], "AND") . '>' . _AND . '</OPTION>';
            echo '    </SELECT>';
            if ($i == $this->criteria_cnt - 1) echo '    <INPUT TYPE="submit" class="button" NAME="submit" VALUE="' . htmlspecialchars($add_button_string) . '">';
            echo '<BR>';
        }
    }
    function Compact() {
        if ($this->isEmpty()) {
            $this->criteria = "";
            $_SESSION[$this->export_name] = & $this->criteria;
        }
    }
};
class ProtocolFieldCriteria extends MultipleElementCriteria {
    function SanitizeElement($i) {
        // Make a copy of the element array
        $curArr = $this->criteria[$i];
        // Sanitize the element
        $this->criteria[$i][0] = @CleanVariable($curArr[0], VAR_OPAREN);
        $this->criteria[$i][1] = @CleanVariable($curArr[1], "", array_keys($this->valid_field_list));
        $this->criteria[$i][2] = @CleanVariable($curArr[2], "", array(
            "=",
            "!=",
            "<",
            "<=",
            ">",
            ">="
        ));
        $this->criteria[$i][3] = @CleanVariable($curArr[3], VAR_DIGIT);
        $this->criteria[$i][4] = @CleanVariable($curArr[4], VAR_OPAREN | VAR_CPAREN);
        $this->criteria[$i][5] = @CleanVariable($curArr[5], "", array(
            "AND",
            "OR"
        ));
        // Destroy the copy
        unset($curArr);
    }
    function Description($human_fields) {
        $tmp = "";
        for ($i = 0; $i < $this->criteria_cnt; $i++) {
            if (is_array($this->criteria[$i])) if ($this->criteria[$i][1] != " " && $this->criteria[$i][3] != "") $tmp = $tmp . $this->criteria[$i][0] . $human_fields[($this->criteria[$i][1]) ] . ' ' . $this->criteria[$i][2] . ' ' . $this->criteria[$i][3] . $this->criteria[$i][4] . ' ' . $this->criteria[$i][5];
        }
        if ($tmp != "") $tmp = $tmp . $this->cs->GetClearCriteriaString($this->export_name) . '<BR>';
        return $tmp;
    }
}
class SignatureCriteria extends SingleElementCriteria {
    /*
    * $sig[3]: stores signature
    *   - [0] : exactly, roughly
    *   - [1] : signature
    *   - [2] : =, !=
    */
    var $sig_type;
    var $criteria = array(
        0 => '',
        1 => ''
    );
    function SignatureCriteria(&$db, &$cs, $export_name) {
        $tdb = & $db;
        $cs = & $cs;
        $this->BaseCriteria($tdb, $cs, $export_name);
        $this->sig_type = "";
    }
    function Init() {
        InitArray($this->criteria, 3, 0, "");
        $this->sig_type = "";
    }
    function Import() {
        parent::Import();
        $this->sig_type = SetSessionVar("sig_type");
        $_SESSION['sig_type'] = & $this->sig_type;
    }
    function Clear() {
    }
    function SanitizeElement() {
        if (!isset($this->criteria[0]) || !isset($this->criteria[1])) {
            $this->criteria = array(
                0 => '',
                1 => ''
            );
        }
        $this->criteria[0] = CleanVariable(@$this->criteria[0], "", array(
            " ",
            "=",
            "LIKE"
        ));
        $this->criteria[1] = filterSql(@$this->criteria[1]); /* signature name */
        $this->criteria[2] = CleanVariable(@$this->criteria[2], "", array(
            "=",
            "!="
        ));
    }
    function PrintForm() {
        if (!@is_array($this->criteria)) $this->criteria = array();
        echo '<SELECT NAME="sig[0]"><OPTION VALUE=" "  ' . chk_select(@$this->criteria[0], " ") . '>' . _DISPSIG;
        echo '                      <OPTION VALUE="="     ' . chk_select(@$this->criteria[0], "=") . '>' . _SIGEXACTLY;
        echo '                      <OPTION VALUE="LIKE" ' . chk_select(@$this->criteria[0], "LIKE") . '>' . _SIGROUGHLY . '</SELECT>';
        echo '<SELECT NAME="sig[2]"><OPTION VALUE="="  ' . chk_select(@$this->criteria[2], "=") . '>=';
        echo '                      <OPTION VALUE="!="     ' . chk_select(@$this->criteria[2], "!=") . '>!=';
        echo '</SELECT>';
        echo '<INPUT TYPE="text" NAME="sig[1]" SIZE=40 VALUE="' . htmlspecialchars(@$this->criteria[1]) . '"><BR>';
        if ($GLOBALS['use_sig_list'] > 0) {
            $temp_sql = "SELECT DISTINCT sig_name FROM signature";
            if ($GLOBALS['use_sig_list'] == 1) {
                $temp_sql = $temp_sql . " WHERE sig_name NOT LIKE '%SPP\_%'";
            }
            $temp_sql = $temp_sql . " ORDER BY sig_name";
            $tmp_result = $this->db->baseExecute($temp_sql);
            echo '<SELECT NAME="sig_lookup"
                       onChange=\'PacketForm.elements[4].value =
                         this.options[this.selectedIndex].value;return true;\'>
                <OPTION VALUE="null" SELECTED>{ Select Signature from List }';
            if ($tmp_result) {
                while ($myrow = $tmp_result->baseFetchRow()) echo '<OPTION VALUE="' . $myrow[0] . '">' . $myrow[0];
                $tmp_result->baseFreeRows();
            }
            echo '</SELECT><BR>';
        }
    }
    function ToSQL() {
    }
    function Description() {
        $tmp = $tmp_human = "";
        if ((isset($this->criteria[0])) && ($this->criteria[0] != " ") && (isset($this->criteria[1])) && ($this->criteria[1] != "")) {
            if ($this->criteria[0] == '=' && $this->criteria[2] == '!=') $tmp_human = '!=';
            else if ($this->criteria[0] == '=' && $this->criteria[2] == '=') $tmp_human = '=';
            else if ($this->criteria[0] == 'LIKE' && $this->criteria[2] == '!=') $tmp_human = ' ' . _DOESNTCONTAIN . ' ';
            else if ($this->criteria[0] == 'LIKE' && $this->criteria[2] == '=') $tmp_human = ' ' . _CONTAINS . ' ';
            $tmp = $tmp . _SIGNATURE . ' ' . $tmp_human . ' "';
            $pidsid = explode(";",$this->criteria[1]); 
            if (($this->db->baseGetDBversion() >= 100) && $this->sig_type == 1) $tmp = $tmp . html_entity_decode(preg_replace("/.*##/","",BuildSigByPlugin(intval($pidsid[0]),intval($pidsid[1]), $this->db))) . '" ' . $this->cs->GetClearCriteriaString($this->export_name);
            else $tmp = $tmp . htmlentities($this->criteria[1], ENT_COMPAT, "UTF-8") . '"' . $this->cs->GetClearCriteriaString($this->export_name);
            $tmp = $tmp . '<BR>';
        }
        return $tmp;
    }
}; /* SignatureCriteria */
class SignatureClassificationCriteria extends SingleElementCriteria {
    function Init() {
        $this->criteria = "";
    }
    function Clear() {
        /* clears the criteria */
    }
    function SanitizeElement() {
        $this->criteria = CleanVariable($this->criteria, VAR_DIGIT);
    }
    function PrintForm() {
        if ($this->db->baseGetDBversion() >= 103) {
            echo '<SELECT NAME="sig_class">
              <OPTION VALUE=" " ' . chk_select($this->criteria, " ") . '>' . _DISPANYCLASS . '
              <OPTION VALUE="null" ' . chk_select($this->criteria, "null") . '>-' . _UNCLASS . '-';
            $temp_sql = "SELECT sig_class_id, sig_class_name FROM sig_class";
            $tmp_result = $this->db->baseExecute($temp_sql);
            if ($tmp_result) {
                while ($myrow = $tmp_result->baseFetchRow()) echo '<OPTION VALUE="' . $myrow[0] . '" ' . chk_select($this->criteria, $myrow[0]) . '>' . $myrow[1];
                $tmp_result->baseFreeRows();
            }
            echo '</SELECT>&nbsp;&nbsp';
        }
    }
    function ToSQL() {
        /* convert this criteria to SQL */
    }
    function Description() {
        $tmp = "";
        if ($this->db->baseGetDBversion() >= 103) {
            if ($this->criteria != " " && $this->criteria != "") {
                if ($this->criteria == "null") $tmp = $tmp . _SIGCLASS . ' = ' . '<I>' . _UNCLASS . '</I><BR>';
                else $tmp = $tmp . _SIGCLASS . ' = ' . htmlentities(GetSigClassName($this->criteria, $this->db, ENT_COMPAT, "UTF-8")) . $this->cs->GetClearCriteriaString($this->export_name) . '<BR>';
            }
        }
        return $tmp;
    }
}; /* SignatureClassificationCriteria */
class SignaturePriorityCriteria extends SingleElementCriteria {
    var $criteria = array();
    function Init() {
        $this->criteria = "";
    }
    function Clear() {
        /* clears the criteria */
    }
    function SanitizeElement() {
        if (!isset($this->criteria[0]) || !isset($this->criteria[1])) {
            $this->criteria = array(
                0 => '',
                1 => ''
            );
        }
        $this->criteria[0] = CleanVariable(@$this->criteria[0], "", array(
            "=",
            "!=",
            "<",
            "<=",
            ">",
            ">="
        ));
        $this->criteria[1] = CleanVariable(@$this->criteria[1], VAR_DIGIT);
    }
    function PrintForm() {
        if ($this->db->baseGetDBversion() >= 103) {
            if (!@is_array($this->criteria)) $this->criteria = array();
            echo '<SELECT NAME="sig_priority[0]">
                <OPTION VALUE=" " ' . @chk_select($this->criteria[0], "=") . '>__</OPTION>
                <OPTION VALUE="=" ' . @chk_select($this->criteria[0], "=") . '>==</OPTION>
                <OPTION VALUE="!=" ' . @chk_select($this->criteria[0], "!=") . '>!=</OPTION>
                <OPTION VALUE="<"  ' . @chk_select($this->criteria[0], "<") . '><</OPTION>
                <OPTION VALUE=">"  ' . @chk_select($this->criteria[0], ">") . '>></OPTION>
                <OPTION VALUE="<=" ' . @chk_select($this->criteria[0], "><=") . '><=</OPTION>
                <OPTION VALUE=">=" ' . @chk_select($this->criteria[0], ">=") . '>>=</SELECT>';
            echo '<SELECT NAME="sig_priority[1]">
                <OPTION VALUE="" ' . @chk_select($this->criteria[1], " ") . '>' . _DISPANYPRIO . '</OPTION>
 	        <OPTION VALUE="null" ' . @chk_select($this->criteria[1], "null") . '>-' . _UNCLASS . '-</OPTION>';
            $temp_sql = "select DISTINCT sig_priority from signature ORDER BY sig_priority ASC ";
            $tmp_result = $this->db->baseExecute($temp_sql);
            if ($tmp_result) {
                while ($myrow = $tmp_result->baseFetchRow()) echo '<OPTION VALUE="' . $myrow[0] . '" ' . chk_select(@$this->criteria[1], $myrow[0]) . '>' . $myrow[0];
                $tmp_result->baseFreeRows();
            }
            echo '</SELECT>&nbsp;&nbsp';
        }
    }
    function ToSQL() {
        /* convert this criteria to SQL */
    }
    function Description() {
        $tmp = "";
        if (!isset($this->criteria[1])) {
            $this->criteria = array(
                0 => '',
                1 => ''
            );
        }
        if ($this->db->baseGetDBversion() >= 103) {
            if ($this->criteria[1] != " " && $this->criteria[1] != "") {
                if ($this->criteria[1] == null) $tmp = $tmp . _SIGPRIO . ' = ' . '<I>' . _NONE . '</I><BR>';
                else $tmp = $tmp . _SIGPRIO . ' ' . htmlentities($this->criteria[0], ENT_COMPAT, "UTF-8") . " " . htmlentities($this->criteria[1], ENT_COMPAT, "UTF-8") . $this->cs->GetClearCriteriaString($this->export_name) . '<BR>';
            }
        }
        return $tmp;
    }
}; /* SignaturePriorityCriteria */
class AlertGroupCriteria extends SingleElementCriteria {
    function Init() {
        $this->criteria = "";
    }
    function Clear() {
        /* clears the criteria */
    }
    function SanitizeElement() {
        $this->criteria = CleanVariable($this->criteria, VAR_DIGIT);
    }
    function PrintForm() {
        echo '<SELECT NAME="ag">
             <OPTION VALUE=" " ' . chk_select($this->criteria, " ") . '>' . _DISPANYAG;
        $temp_sql = "SELECT ag_id, ag_name FROM acid_ag";
        $tmp_result = $this->db->baseExecute($temp_sql);
        if ($tmp_result) {
            while ($myrow = $tmp_result->baseFetchRow()) echo '<OPTION VALUE="' . $myrow[0] . '" ' . chk_select($this->criteria, $myrow[0]) . '>' . '[' . $myrow[0] . '] ' . $myrow[1];
            $tmp_result->baseFreeRows();
        }
        echo '</SELECT>&nbsp;&nbsp;';
    }
    function ToSQL() {
        /* convert this criteria to SQL */
    }
    function Description() {
        $tmp = "";
        if ($this->criteria != " " && $this->criteria != "") $tmp = $tmp . _ALERTGROUP . ' = [' . htmlentities($this->criteria, ENT_COMPAT, "UTF-8") . '] ' . GetAGNameByID($this->criteria, $this->db) . $this->cs->GetClearCriteriaString($this->export_name) . '<BR>';
        return $tmp;
    }
}; /* AlertGroupCriteria */
class PluginCriteria extends SingleElementCriteria {
    function Init() {
        $this->criteria = "";
    }
    function Clear() {
    }
    function SanitizeElement() {
        $this->criteria = CleanVariable($this->criteria, VAR_DIGIT | VAR_PUNC);
    }
    function PrintForm() {
    }
    function ToSQL() {
    }
    function Description() {
        $tmp = "";
        if ($this->criteria != " " && $this->criteria != "") $tmp = $tmp . _("Plugin") . ' = (' . GetPluginName($this->criteria, $this->db) .')'. $this->cs->GetClearCriteriaString($this->export_name) . '<BR>';
        return $tmp;
    }
}; /* PluginCriteria */
class SourceTypeCriteria extends SingleElementCriteria {
    function Init() {
        $this->criteria = "";
    }
    function Clear() {
    }
    function SanitizeElement() {
        $this->criteria = CleanVariable($this->criteria, VAR_ALPHA | VAR_SPACE | VAR_PUNC);
    }
    function PrintForm() {
    }
    function ToSQL() {
    }
    function Description() {
        $tmp = "";
        if ($this->criteria != " " && $this->criteria != "") $tmp = $tmp . _("Product Type") . ' = (' . $this->criteria .')'. $this->cs->GetClearCriteriaString($this->export_name) . '<BR>';
        return $tmp;
    }
}; /* SourceTypeCriteria */
class CategoryCriteria extends SingleElementCriteria {
	var $criteria = array();
    function Init() {
        $this->criteria = array(0,0);
    }
    function Clear() {
    }
    function SanitizeElement() {
        $this->criteria[0] = CleanVariable($this->criteria[0], VAR_DIGIT);
        $this->criteria[1] = CleanVariable($this->criteria[1], VAR_DIGIT);
    }
    function PrintForm() {
    }
    function ToSQL() {
    }
    function Description() {
        $tmp = "";
        if ($this->criteria[0] != 0) {
        	if ($this->criteria[1] != 0)
        		$tmp .= _("Event Category/SubCategory") . ' = (' . GetPluginCategoryName($this->criteria[0], $this->db) . '/' . GetPluginSubCategoryName($this->criteria, $this->db) .')';
        	else
        		$tmp .= _("Event Category") . ' = (' . GetPluginCategoryName($this->criteria[0], $this->db) .')';
        	$tmp .= $this->cs->GetClearCriteriaString($this->export_name) . '<BR>';
        }
        return $tmp;
    }
}; /* CategoryCriteria */
class PluginGroupCriteria extends SingleElementCriteria {
    function Init() {
        $this->criteria = "";
    }
    function Clear() {
    }
    function SanitizeElement() {
        $this->criteria = CleanVariable($this->criteria, VAR_DIGIT);
    }
    function PrintForm() {
    }
    function ToSQL() {
    }
    function Description() {
        $tmp = "";
        if ($this->criteria != " " && $this->criteria != "") $tmp = $tmp . _("Plugin group") . ' = (' . GetPluginGroupName($this->criteria, $this->db) .')'. $this->cs->GetClearCriteriaString($this->export_name) . '<BR>';
        return $tmp;
    }
}; /* PluginGroupCriteria */
class UserDataCriteria extends SingleElementCriteria {
    function Init() {
        $this->criteria = "";
    }
    function Clear() {
    }
    function SanitizeElement() {
        //$this->criteria = CleanVariable($this->criteria, VAR_DIGIT);
    }
    function PrintForm() {
    }
    function ToSQL() {
    }
    function Description() {
        $tmp = "";
        if ($this->criteria != " " && $this->criteria != "") $tmp = $tmp . _("User data") . ' = (' . $this->criteria .')'. $this->cs->GetClearCriteriaString($this->export_name) . '<BR>';
        return $tmp;
    }
}; /* UserDataCriteria */
class SensorCriteria extends SingleElementCriteria {
    function Init() {
        $this->criteria = "";
    }
    function Clear() {
        /* clears the criteria */
    }
    function SanitizeElement() {
        $this->criteria = CleanVariable($this->criteria, VAR_DIGIT | VAR_PUNC);
    }
    function PrintForm() {
        GLOBAL $db;
	    echo '<SELECT NAME="sensor" id="sensor">
             <OPTION VALUE=" " ' . chk_select($this->criteria, " ") . '>' . _DISPANYSENSOR;
        // Filter by user perms if no criteria
		$where_sensor = "";
		if (Session::allowedSensors() != "") {
			$user_sensors = explode(",",Session::allowedSensors());
			$snortsensors = GetSensorSids($db);
			$sensor_str = "";
			foreach ($user_sensors as $user_sensor)
				if (count($snortsensors[$user_sensor]) > 0) $sensor_str .= ($sensor_str != "") ? ",".implode(",",$snortsensors[$user_sensor]) : implode(",",$snortsensors[$user_sensor]);
			if ($sensor_str == "") $sensor_str = "0";
			$where_sensor = " WHERE sid in (" . $sensor_str . ")";
		}
		$temp_sql = "SELECT sid, hostname, interface, filter FROM sensor$where_sensor";
		$tmp_result = $this->db->baseExecute($temp_sql);
        $varjs = "var sensortext = Array(); var sensorvalue = Array();\n";
        $sensor_sid_names = array();
		if ($tmp_result->row) {
            $i = 0;
            while ($myrow = $tmp_result->baseFetchRow()) {
                $sname = GetSensorName($myrow[0], $this->db);
                $sensor_sid_names[$sname] .= (($sensor_sid_names[$sname] != "") ? "," : "").$myrow[0];
				//echo '<OPTION VALUE="' . $myrow[0] . '" ' . chk_select($this->criteria, $myrow[0]) . '>' . '[' . $myrow[0] . '] ' . $sname;
                //$varjs.= "sensortext[$i] = '$sname';\n";
                //$varjs.= "sensorvalue[$i] = '" . $myrow[0] . "';\n";
            }
			foreach ($sensor_sid_names as $name=>$sids) {
				echo '<OPTION VALUE="' . $sids . '" ' . chk_select($this->criteria, $sids) . '>' . $name;
                $varjs.= "sensortext[$i] = '$name';\n";
                $varjs.= "sensorvalue[$i] = '" . $sids . "';\n";
				$i++;
			}
            $tmp_result->baseFreeRows();
        }
        echo '</SELECT><script>' . $varjs . ' var num_sensors=' . $i . ';</script>&nbsp;&nbsp;';
    }
    function ToSQL() {
        /* convert this criteria to SQL */
    }
    function Description() {
        $tmp = "";
        //if ($this->criteria != " " && $this->criteria != "") $tmp = $tmp . _SENSOR . ' = [' . htmlentities($this->criteria, ENT_COMPAT, "UTF-8") . '] (' . GetSensorName($this->criteria, $this->db) .')'. $this->cs->GetClearCriteriaString($this->export_name) . '<BR>';
		if ($this->criteria != " " && $this->criteria != "") $tmp = $tmp . _SENSOR . ' = (' . GetSensorName($this->criteria, $this->db) .')'. $this->cs->GetClearCriteriaString($this->export_name) . '<BR>';
        return $tmp;
    }
}; /* SensorCriteria */
class TimeCriteria extends MultipleElementCriteria {
    /*
    * $time[MAX][10]: stores the date/time of the packet detection
    *  - [][0] : (                           [][5] : hour
    *  - [][1] : =, !=, <, <=, >, >=         [][6] : minute
    *  - [][2] : month                       [][7] : second
    *  - [][3] : day                         [][8] : (, )
    *  - [][4] : year                        [][9] : AND, OR
    *
    * $time_cnt : number of rows in the $time[][] structure
    */
    function Clear() {
        /* clears the criteria */
    }
    function SanitizeElement($i) {
        // Make copy of element array.
        $curArr = $this->criteria[$i];
        // Sanitize the element
        $this->criteria[$i][0] = @CleanVariable($curArr[0], VAR_OPAREN);
        $this->criteria[$i][1] = @CleanVariable($curArr[1], "", array(
            "=",
            "!=",
            "<",
            "<=",
            ">",
            ">="
        ));
        $this->criteria[$i][2] = @CleanVariable($curArr[2], VAR_DIGIT);
        $this->criteria[$i][3] = @CleanVariable($curArr[3], VAR_DIGIT);
        $this->criteria[$i][4] = @CleanVariable($curArr[4], VAR_DIGIT);
        $this->criteria[$i][5] = @CleanVariable($curArr[5], VAR_DIGIT);
        $this->criteria[$i][6] = @CleanVariable($curArr[6], VAR_DIGIT);
        $this->criteria[$i][7] = @CleanVariable($curArr[7], VAR_DIGIT);
        $this->criteria[$i][8] = @CleanVariable($curArr[8], VAR_OPAREN | VAR_CPAREN);
        $this->criteria[$i][9] = @CleanVariable($curArr[9], "", array(
            "AND",
            "OR"
        ));
        //print_r($this->criteria[$i]);
        // Destroy the old copy
        unset($curArr);
    }
    function PrintForm() {
        // add default criteria => today
        if ($this->criteria_cnt == 0) {
            $this->criteria = array();
            $this->criteria[0] = array(
                " ",
                ">=",
                date("m") ,
                date("d") ,
                date("Y") ,
                "",
                "",
                "",
                " ",
                " "
            );
            //$this->criteria_cnt = 1;
            
        }
        $this->criteria_cnt = 2;
        for ($i = 0; $i < $this->criteria_cnt; $i++) {
            if (!@is_array($this->criteria[$i])) $this->criteria = array();
            echo '<SELECT NAME="time[' . $i . '][0]"><OPTION VALUE=" " ' . chk_select(@$this->criteria[$i][0], " ") . '>__';
            echo '                               <OPTION VALUE="("  ' . chk_select(@$this->criteria[$i][0], "(") . '>(</SELECT>';
            echo '<SELECT NAME="time[' . $i . '][1]"><OPTION VALUE=" "  ' . chk_select(@$this->criteria[$i][1], " ") . '>' . _DISPTIME;
            echo '                               <OPTION VALUE="="  ' . chk_select(@$this->criteria[$i][1], "=") . '>=';
            echo '                               <OPTION VALUE="!=" ' . chk_select(@$this->criteria[$i][1], "!=") . '>!=';
            echo '                               <OPTION VALUE="<"  ' . chk_select(@$this->criteria[$i][1], "<") . '><';
            echo '                               <OPTION VALUE="<=" ' . chk_select(@$this->criteria[$i][1], "<=") . '><=';
            echo '                               <OPTION VALUE=">"  ' . chk_select(@$this->criteria[$i][1], ">") . '>>';
            echo '                               <OPTION VALUE=">=" ' . chk_select(@$this->criteria[$i][1], ">=") . '>>=</SELECT>';
            echo '<SELECT NAME="time[' . $i . '][2]"><OPTION VALUE=" "  ' . chk_select(@$this->criteria[$i][2], " ") . '>' . _DISPMONTH;
            echo '                               <OPTION VALUE="01" ' . chk_select(@$this->criteria[$i][2], "01") . '>' . _SHORTJAN;
            echo '                               <OPTION VALUE="02" ' . chk_select(@$this->criteria[$i][2], "02") . '>' . _SHORTFEB;
            echo '                               <OPTION VALUE="03" ' . chk_select(@$this->criteria[$i][2], "03") . '>' . _SHORTMAR;
            echo '                               <OPTION VALUE="04" ' . chk_select(@$this->criteria[$i][2], "04") . '>' . _SHORTAPR;
            echo '                               <OPTION VALUE="05" ' . chk_select(@$this->criteria[$i][2], "05") . '>' . _SHORTMAY;
            echo '                               <OPTION VALUE="06" ' . chk_select(@$this->criteria[$i][2], "06") . '>' . _SHORTJUN;
            echo '                               <OPTION VALUE="07" ' . chk_select(@$this->criteria[$i][2], "07") . '>' . _SHORTJLY;
            echo '                               <OPTION VALUE="08" ' . chk_select(@$this->criteria[$i][2], "08") . '>' . _SHORTAUG;
            echo '                               <OPTION VALUE="09" ' . chk_select(@$this->criteria[$i][2], "09") . '>' . _SHORTSEP;
            echo '                               <OPTION VALUE="10" ' . chk_select(@$this->criteria[$i][2], "10") . '>' . _SHORTOCT;
            echo '                               <OPTION VALUE="11" ' . chk_select(@$this->criteria[$i][2], "11") . '>' . _SHORTNOV;
            echo '                               <OPTION VALUE="12" ' . chk_select(@$this->criteria[$i][2], "12") . '>' . _SHORTDEC . '</SELECT>';
            echo '<INPUT TYPE="text" NAME="time[' . $i . '][3]" SIZE=2 VALUE="' . htmlspecialchars(@$this->criteria[$i][3]) . '">';
            echo '<SELECT NAME="time[' . $i . '][4]">' . dispYearOptions(@$this->criteria[$i][4]) . '</SELECT>';
            echo '<INPUT TYPE="text" NAME="time[' . $i . '][5]" SIZE=2 VALUE="' . htmlspecialchars(@$this->criteria[$i][5]) . '"><B>:</B>';
            echo '<INPUT TYPE="text" NAME="time[' . $i . '][6]" SIZE=2 VALUE="' . htmlspecialchars(@$this->criteria[$i][6]) . '"><B>:</B>';
            echo '<INPUT TYPE="text" NAME="time[' . $i . '][7]" SIZE=2 VALUE="' . htmlspecialchars(@$this->criteria[$i][7]) . '">';
            echo '<SELECT NAME="time[' . $i . '][8]"><OPTION VALUE=" " ' . chk_select(@$this->criteria[$i][8], " ") . '>__';
            echo '                               <OPTION VALUE="(" ' . chk_select(@$this->criteria[$i][8], "(") . '>(';
            echo '                               <OPTION VALUE=")" ' . chk_select(@$this->criteria[$i][8], ")") . '>)</SELECT>';
            if ($i == 0) {
                echo '<br><SELECT NAME="time[' . $i . '][9]">';
                echo '                               <OPTION VALUE="AND" ' . chk_select(@$this->criteria[$i][9], "AND") . '>' . _AND;
                echo '                               <OPTION VALUE="OR" ' . chk_select(@$this->criteria[$i][9], "OR") . '>' . _OR . '</SELECT>';
            }
            echo '<BR>';
        }
    }
    function ToSQL() {
        /* convert this criteria to SQL */
    }
    function Description() {
        $tmp = "";
        for ($i = 0; $i < $this->criteria_cnt; $i++) {
            if (isset($this->criteria[$i][1]) && $this->criteria[$i][1] != " ") {
                $tmp = $tmp . '' . htmlspecialchars($this->criteria[$i][0]) . ' time ' . htmlspecialchars($this->criteria[$i][1]) . ' [ ';
                /* date */
                if ($this->criteria[$i][2] == " " && $this->criteria[$i][3] == "" && $this->criteria[$i][4] == " ") $tmp = $tmp . " <I>any date</I>";
                else $tmp = $tmp . (($this->criteria[$i][2] == " ") ? "* / " : $this->criteria[$i][2] . " / ") . (($this->criteria[$i][3] == "") ? "* / " : $this->criteria[$i][3] . " / ") . (($this->criteria[$i][4] == " ") ? "*  " : $this->criteria[$i][4] . " ");
                $tmp = $tmp . '] [ ';
                /* time */
                if ($this->criteria[$i][5] == "" && $this->criteria[$i][6] == "" && $this->criteria[$i][7] == "") $tmp = $tmp . "<I>any time</I>";
                else $tmp = $tmp . (($this->criteria[$i][5] == "") ? "* : " : $this->criteria[$i][5] . " : ") . (($this->criteria[$i][6] == "") ? "* : " : $this->criteria[$i][6] . " : ") . (($this->criteria[$i][7] == "") ? "*  " : $this->criteria[$i][7] . " ");
                $tmp = $tmp . $this->criteria[$i][8] . '] ' . $this->criteria[$i][9];
                $tmp = $tmp;
            }
        }
        if ($tmp != "") $tmp = $tmp . $this->cs->GetClearCriteriaString($this->export_name) . '<BR>';
        return $tmp;
    }
}; /* TimeCriteria */
class IPAddressCriteria extends MultipleElementCriteria {
    /*
    * $ip_addr[MAX][10]: stores an ip address parameters/operators row
    *  - [][0] : (                          [][5] : octet3 of address
    *  - [][1] : source, dest               [][6] : octet4 of address
    *  - [][2] : =, !=                      [][7] : network mask
    *  - [][3] : octet1 of address          [][8] : (, )
    *  - [][4] : octet2 of address          [][9] : AND, OR
    *
    * $ip_addr_cnt: number of rows in the $ip_addr[][] structure
    */
    function IPAddressCriteria(&$db, &$cs, $export_name, $element_cnt) {
        $tdb = & $db;
        $cs = & $cs;
        parent::MultipleElementCriteria($tdb, $cs, $export_name, $element_cnt, array(
            "ip_src" => _SOURCE,
            "ip_dst" => _DEST,
            "ip_both" => _SORD
        ));
    }
    function Import() {
        parent::Import();
        require (dirname(__FILE__) . '/../base_conf.php');
        $vals = NULL;
        $empty = 1;
        /* expand IP into octets */
        $this->criteria = $_SESSION['ip_addr'];
		$this->criteria_cnt = $_SESSION['ip_addr_cnt'];
		
		for ($i = 0; $i < $this->criteria_cnt; $i++) {
            if ((isset($this->criteria[$i][3])) && (ereg("([0-9]*)\.([0-9]*)\.([0-9]*)\.([0-9]*)", $this->criteria[$i][3]))) {
                if (($use_ossim_session) && (Session::allowedNets())) {
                    require_once ('classes/Net.inc');
                    $domain = Session::allowedNets();
                    if ($domain != "") {
                        $tmp_myip = $this->criteria[$i][3];
                        $myip = strtok($tmp_myip, "/");
                        if (Net::isIpInNet($myip, $domain)) {
                            $tmp_ip_str = $this->criteria[$i][7] = $this->criteria[$i][3];
                            $this->criteria[$i][2] = "=";
                            $this->criteria[$i][3] = strtok($tmp_ip_str, ".");
                            $this->criteria[$i][4] = strtok(".");
                            $this->criteria[$i][5] = strtok(".");
                            $this->criteria[$i][6] = strtok("/");
                            $this->criteria[$i][10] = strtok("");
                            $empty = 0;
                            $vals[] = $this->criteria[$i];
                        }
                    }
                } else {
                    $tmp_ip_str = $this->criteria[$i][7] = $this->criteria[$i][3];
                    $this->criteria[$i][3] = strtok($tmp_ip_str, ".");
                    $this->criteria[$i][4] = strtok(".");
                    $this->criteria[$i][5] = strtok(".");
                    $this->criteria[$i][6] = strtok("/");
                    $this->criteria[$i][10] = strtok("");
                    $empty = 0;
                    $vals[] = $this->criteria[$i];
                }
            } elseif (is_array($this->criteria[$i]) && array_key_exists(7, $this->criteria[$i]) && ereg("([0-9]*)\.([0-9]*)\.([0-9]*)\.([0-9]*)", $this->criteria[$i][7])) {
                $empty = 0;
                $vals[] = $this->criteria[$i];
            }
        }
        //print_r ($this->criteria);
        $this->criteria = $vals;
        $this->criteria_cnt = count($vals);
        if (($use_ossim_session) && ($empty)) {
            $domain = Session::allowedNets();
            if ($domain != "") {
                $nets = explode(",", $domain);
                $this->criteria = Array();
                for ($i = 0; $i < count($nets); $i++) {
                    $tmp_ip_str = $tmp[7] = $nets[$i];
                    $tmp[0] = " ";
                    $tmp[1] = "ip_both";
                    $tmp[2] = "=";
                    $tmp[3] = strtok($tmp_ip_str, ".");
                    $tmp[4] = strtok(".");
                    $tmp[5] = strtok(".");
                    $tmp[6] = strtok("/");
                    $tmp[10] = strtok("");
                    $tmp[8] = " ";
                    if ($i == (count($nets) - 1)) $tmp[9] = " ";
                    else $tmp[9] = "OR";
                    $this->criteria[$this->criteria_cnt] = $tmp;
                    $this->criteria_cnt++;
                }
            }
        }
        $new = ImportHTTPVar("new", VAR_DIGIT);
        $submit = ImportHTTPVar("submit", VAR_ALPHA | VAR_SPACE);
        if (($new == 1) && ($submit == "")) {
            $this->criteria = NULL;
            $this->criteria_cnt = 1;
        }
        if ($this->criteria_cnt == "") {
            $this->criteria_cnt = 1;
        }
        //print_r ($this->criteria);
        $_SESSION['ip_addr'] = & $this->criteria;
        $_SESSION['ip_addr_cnt'] = & $this->criteria_cnt;
    }
    function Clear() {
        /* clears the criteria */
    }
    function SanitizeElement() {
        $i = 0;
        // Make copy of old element array
        $curArr = $this->criteria[$i];
        // Sanitize element
        $this->criteria[$i][0] = @CleanVariable($curArr[0], VAR_OPAREN);
        $this->criteria[$i][1] = @CleanVariable($curArr[1], "", array_keys($this->valid_field_list));
        $this->criteria[$i][2] = @CleanVariable($curArr[2], "", array(
            "=",
            "!=",
            "<",
            "<=",
            ">",
            ">="
        ));
        $this->criteria[$i][3] = @CleanVariable($curArr[3], VAR_DIGIT);
        $this->criteria[$i][4] = @CleanVariable($curArr[4], VAR_DIGIT);
        $this->criteria[$i][5] = @CleanVariable($curArr[5], VAR_DIGIT);
        $this->criteria[$i][6] = @CleanVariable($curArr[6], VAR_DIGIT);
        $this->criteria[$i][7] = @CleanVariable($curArr[7], VAR_DIGIT | VAR_PERIOD | VAR_FSLASH);
        $this->criteria[$i][8] = @CleanVariable($curArr[8], VAR_OPAREN | VAR_CPAREN);
        $this->criteria[$i][9] = @CleanVariable($curArr[9], "", array(
            "AND",
            "OR"
        ));
        $this->criteria[$i][10] = @CleanVariable($curArr[10], VAR_DIGIT);
        // Destroy copy
        unset($curArr);
    }
    function PrintForm() {
		//print_r(@$this->criteria);
		for ($i = 0; $i < $this->criteria_cnt; $i++) {
            if (!is_array(@$this->criteria[$i])) $this->criteria = array();
            echo '    <SELECT NAME="ip_addr[' . $i . '][0]"><OPTION VALUE=" " ' . chk_select(@$this->criteria[$i][0], " ") . '>__';
            echo '                                      <OPTION VALUE="(" ' . chk_select(@$this->criteria[$i][0], "(") . '>(</SELECT>';
            echo '    <SELECT NAME="ip_addr[' . $i . '][1]">
                    <OPTION VALUE=" "      ' . chk_select(@$this->criteria[$i][1], " ") . '>' . _DISPADDRESS . '
                    <OPTION VALUE="ip_src" ' . chk_select(@$this->criteria[$i][1], "ip_src") . '>' . _SHORTSOURCE . '
                    <OPTION VALUE="ip_dst" ' . chk_select(@$this->criteria[$i][1], "ip_dst") . '>' . _SHORTDEST . '
                    <OPTION VALUE="ip_both" ' . chk_select(@$this->criteria[$i][1], "ip_both") . '>' . _SHORTSOURCEORDEST . '
                   </SELECT>';
            echo '    <SELECT NAME="ip_addr[' . $i . '][2]">
                    <OPTION VALUE="="  ' . chk_select(@$this->criteria[$i][2], "=") . '>=
                    <OPTION VALUE="!=" ' . chk_select(@$this->criteria[$i][2], "!=") . '>!=
                   </SELECT>';
            if ($GLOBALS['ip_address_input'] == 2) echo '    <INPUT TYPE="text" NAME="ip_addr[' . $i . '][3]" SIZE=16 VALUE="' . htmlspecialchars(@$this->criteria[$i][7]) . '">';
            else {
                echo '    <INPUT TYPE="text" NAME="ip_addr[' . $i . '][3]" SIZE=3 VALUE="' . htmlspecialchars(@$this->criteria[$i][3]) . '"><B>.</B>';
                echo '    <INPUT TYPE="text" NAME="ip_addr[' . $i . '][4]" SIZE=3 VALUE="' . htmlspecialchars(@$this->criteria[$i][4]) . '"><B>.</B>';
                echo '    <INPUT TYPE="text" NAME="ip_addr[' . $i . '][5]" SIZE=3 VALUE="' . htmlspecialchars(@$this->criteria[$i][5]) . '"><B>.</B>';
                echo '    <INPUT TYPE="text" NAME="ip_addr[' . $i . '][6]" SIZE=3 VALUE="' . htmlspecialchars(@$this->criteria[$i][6]) . '"><!--<B>/</B>';
                echo '    <INPUT TYPE="text" NAME="ip_addr[' . $i . '][7]" SIZE=3 VALUE="' . htmlspecialchars(@$this->criteria[$i][7]) . '">-->';
            }
            echo '    <SELECT NAME="ip_addr[' . $i . '][8]"><OPTION VALUE=" " ' . chk_select(@$this->criteria[$i][8], " ") . '>__';
            echo '                                      <OPTION VALUE="(" ' . chk_select(@$this->criteria[$i][8], "(") . '>(';
            echo '                                      <OPTION VALUE=")" ' . chk_select(@$this->criteria[$i][8], ")") . '>)</SELECT>';
            if ($i < $this->criteria_cnt-1) {
				echo '    <SELECT NAME="ip_addr[' . $i . '][9]"><OPTION VALUE=" "   ' . chk_select(@$this->criteria[$i][9], " ") . '>__';
				echo '                                      <OPTION VALUE="OR" ' . chk_select(@$this->criteria[$i][9], "OR") . '>' . _OR;
				echo '                                      <OPTION VALUE="AND" ' . chk_select(@$this->criteria[$i][9], "AND") .'>' . _AND . '</SELECT>';
			}
            if ($i == $this->criteria_cnt - 1) echo '    <INPUT TYPE="submit" class="button" NAME="submit" VALUE="' . _ADDADDRESS . '">';
            echo '<BR>';
        }
    }
    function ToSQL() {
        /* convert this criteria to SQL */
    }
	function PrintElement($i,$clear=true) {
		$human_fields["ip_src"] = _SOURCE;
		$human_fields["ip_dst"] = _DEST;
		$human_fields["ip_both"] = _SORD;
		$human_fields[""] = "";
		$human_fields["LIKE"] = _CONTAINS;
		$human_fields["="] = "=";
		$tmp = "";
		if (isset($this->criteria[$i][3]) && $this->criteria[$i][3] != "") {
			$tmp = $tmp . $this->criteria[$i][3];
			if ($this->criteria[$i][4] != "") {
				$tmp = $tmp . "." . $this->criteria[$i][4];
				if ($this->criteria[$i][5] != "") {
					$tmp = $tmp . "." . $this->criteria[$i][5];
					if ($this->criteria[$i][6] != "") {
						if (($this->criteria[$i][3] . "." . $this->criteria[$i][4] . "." . $this->criteria[$i][5] . "." . $this->criteria[$i][6]) == NULL_IP) $tmp = " unknown ";
						else $tmp = $tmp . "." . $this->criteria[$i][6];
					} else $tmp = $tmp . '.*';
				} else $tmp = $tmp . '.*.*';
			} else $tmp = $tmp . '.*.*.*';
		}
		/* Make sure that the IP isn't blank */
		if ($tmp != "") {
			$mask = "";
			if ($this->criteria[$i][10] != "") $mask = "/" . $this->criteria[$i][10];
			$tmp = $this->criteria[$i][0] . $human_fields[($this->criteria[$i][1]) ] . $this->criteria[$i][2] . $tmp . $mask . ' ' . $this->criteria[$i][8] . ' ' . $this->criteria[$i][9]  . ($clear==true ? $this->cs->GetClearCriteriaString($this->export_name) : "") . "<BR>";
		}
        return $tmp;
    }
    function Description() {
		$tmp2 = "";
		if ($this->criteria_cnt > 0) {
			$tmp2 = $this->PrintElement(0,($this->criteria_cnt>1 ? false : true));
			if ($this->criteria_cnt > 2)
				$tmp2 .= "<font class='grisclaro'>[ ".($this->criteria_cnt-2)." more ... ]</font><br>".$this->PrintElement($this->criteria_cnt-1);
			else
				$tmp2 .= $this->PrintElement(1);
		}
        return $tmp2;
    }
}; /* IPAddressCriteria */
class IPFieldCriteria extends ProtocolFieldCriteria {
    /*
    * $ip_field[MAX][6]: stores all other ip fields parameters/operators row
    *  - [][0] : (                            [][3] : field value
    *  - [][1] : TOS, TTL, ID, offset, length [][4] : (, )
    *  - [][2] : =, !=, <, <=, >, >=          [][5] : AND, OR
    *
    * $ip_field_cnt: number of rows in the $ip_field[][] structure
    */
    function IPFieldCriteria(&$db, &$cs, $export_name, $element_cnt) {
        $tdb = & $db;
        $cs = & $cs;
        parent::ProtocolFieldCriteria($tdb, $cs, $export_name, $element_cnt, array(
            "ip_tos" => "TOS",
            "ip_ttl" => "TTL",
            "ip_id" => "ID",
            "ip_off" => "offset",
            "ip_csum" => "chksum",
            "ip_len" => "length"
        ));
    }
    function PrintForm() {
        parent::PrintForm($this->valid_field_list, _DISPFIELD, _ADDIPFIELD);
    }
    function ToSQL() {
        /* convert this criteria to SQL */
    }
    function Description() {
        return parent::Description(array_merge(array(
            "" => "",
            "LIKE" => _CONTAINS,
            "=" => "="
        ) , $this->valid_field_list));
    }
};
class TCPPortCriteria extends ProtocolFieldCriteria {
    /*
    * $tcp_port[MAX][6]: stores all port parameters/operators row
    *  - [][0] : (                            [][3] : port value
    *  - [][1] : Source Port, Dest Port       [][4] : (, )
    *  - [][2] : =, !=, <, <=, >, >=          [][5] : AND, OR
    *
    * $tcp_port_cnt: number of rows in the $tcp_port[][] structure
    */
    function TCPPortCriteria(&$db, &$cs, $export_name, $element_cnt) {
        $tdb = & $db;
        $cs = & $cs;
        parent::ProtocolFieldCriteria($tdb, $cs, $export_name, $element_cnt, array(
            "layer4_sport" => _SOURCEPORT,
            "layer4_dport" => _DESTPORT
        ));
    }
    function PrintForm() {
        parent::PrintForm($this->valid_field_list, _DISPPORT, _ADDTCPPORT);
    }
    function ToSQL() {
        /* convert this criteria to SQL */
    }
    function Description() {
        return parent::Description(array_merge(array(
            "" => "",
            "=" => "="
        ) , $this->valid_field_list));
    }
}; /* TCPPortCriteria */
class TCPFieldCriteria extends ProtocolFieldCriteria {
    /*
    * TCP Variables
    * =============
    * $tcp_field[MAX][6]: stores all other tcp fields parameters/operators row
    *  - [][0] : (                            [][3] : field value
    *  - [][1] : windows, URP                 [][4] : (, )
    *  - [][2] : =, !=, <, <=, >, >=          [][5] : AND, OR
    *
    * $tcp_field_cnt: number of rows in the $tcp_field[][] structure
    */
    function TCPFieldCriteria(&$db, &$cs, $export_name, $element_cnt) {
        $tdb = & $db;
        $cs = & $cs;
        parent::ProtocolFieldCriteria($tdb, $cs, $export_name, $element_cnt, array(
            "tcp_win" => "window",
            "tcp_urp" => "urp",
            "tcp_seq" => "seq #",
            "tcp_ack" => "ack",
            "tcp_off" => "offset",
            "tcp_res" => "res",
            "tcp_csum" => "chksum"
        ));
    }
    function PrintForm() {
        parent::PrintForm($this->valid_field_list, _DISPFIELD, _ADDTCPFIELD);
    }
    function ToSQL() {
        /* convert this criteria to SQL */
    }
    function Description() {
        return parent::Description(array_merge(array(
            "" => ""
        ) , $this->valid_field_list));
    }
}; /* TCPFieldCriteria */
class TCPFlagsCriteria extends SingleElementCriteria {
    /*
    * $tcp_flags[7]: stores all other tcp flags parameters/operators row
    *  - [0] : is, contains                   [4] : 8     (RST)
    *  - [1] : 1   (FIN)                      [5] : 16    (ACK)
    *  - [2] : 2   (SYN)                      [6] : 32    (URG)
    *  - [3] : 4   (PUSH)
    */
    function Init() {
        InitArray($this->criteria, $GLOBALS['MAX_ROWS'], TCPFLAGS_CFCNT, "");
    }
    function Clear() {
        /* clears the criteria */
    }
    function SanitizeElement() {
        $this->criteria = CleanVariable($this->criteria, VAR_DIGIT);
    }
    function PrintForm() {
        if (!is_array($this->criteria[0])) $this->criteria = array();
        echo '<TD><SELECT NAME="tcp_flags[0]"><OPTION VALUE=" " ' . chk_select($this->criteria[0], " ") . '>' . _DISPFLAGS;
        echo '                              <OPTION VALUE="is" ' . chk_select($this->criteria[0], "is") . '>' . _IS;
        echo '                              <OPTION VALUE="contains" ' . chk_select($this->criteria[0], "contains") . '>' . _CONTAINS . '</SELECT>';
        echo '   <FONT>';
        echo '    <INPUT TYPE="checkbox" NAME="tcp_flags[8]" VALUE="128" ' . chk_check($this->criteria[8], "128") . '> [RSV1] &nbsp';
        echo '    <INPUT TYPE="checkbox" NAME="tcp_flags[7]" VALUE="64"  ' . chk_check($this->criteria[7], "64") . '> [RSV0] &nbsp';
        echo '    <INPUT TYPE="checkbox" NAME="tcp_flags[6]" VALUE="32"  ' . chk_check($this->criteria[6], "32") . '> [URG] &nbsp';
        echo '    <INPUT TYPE="checkbox" NAME="tcp_flags[5]" VALUE="16"  ' . chk_check($this->criteria[5], "16") . '> [ACK] &nbsp';
        echo '    <INPUT TYPE="checkbox" NAME="tcp_flags[3]" VALUE="8"   ' . chk_check($this->criteria[4], "8") . '> [PSH] &nbsp';
        echo '    <INPUT TYPE="checkbox" NAME="tcp_flags[4]" VALUE="4"   ' . chk_check($this->criteria[3], "4") . '> [RST] &nbsp';
        echo '    <INPUT TYPE="checkbox" NAME="tcp_flags[2]" VALUE="2"   ' . chk_check($this->criteria[2], "2") . '> [SYN] &nbsp';
        echo '    <INPUT TYPE="checkbox" NAME="tcp_flags[1]" VALUE="1"   ' . chk_check($this->criteria[1], "1") . '> [FIN] &nbsp';
        echo '  </FONT>';
    }
    function ToSQL() {
        /* convert this criteria to SQL */
    }
    function Description() {
        $human_fields["1"] = "F";
        $human_fields["2"] = "S";
        $human_fields["4"] = "R";
        $human_fields["8"] = "P";
        $human_fields["16"] = "A";
        $human_fields["32"] = "U";
        $human_fields["64"] = "[R0]";
        $human_fields["128"] = "[R1]";
        $human_fields["LIKE"] = _CONTAINS;
        $human_fields["="] = "=";
        $tmp = "";
        if (isset($this->criteria[0]) && ($this->criteria[0] != " ") && ($this->criteria[0] != "")) {
            $tmp = $tmp . 'flags ' . $this->criteria[0] . ' ';
            for ($i = 8; $i >= 1; $i--) {
				if ($this->criteria[$i] == "") $tmp = $tmp . '-';
				elseif(!is_array($this->criteria[$i])) $tmp = $tmp . $human_fields[$this->criteria[$i]];
			}
            $tmp = $tmp . $this->cs->GetClearCriteriaString("tcp_flags") . '<BR>';
        }
        return $tmp;
    }
    function isEmpty() {
        if (strlen($this->criteria) != 0 && ($this->criteria[0] != "") && ($this->criteria[0] != " ")) return false;
        else return true;
    }
}; /* TCPFlagCriteria */
class UDPPortCriteria extends ProtocolFieldCriteria {
    /*
    * $udp_port[MAX][6]: stores all port parameters/operators row
    *  - [][0] : (                            [][3] : port value
    *  - [][1] : Source Port, Dest Port       [][4] : (, )
    *  - [][2] : =, !=, <, <=, >, >=          [][5] : AND, OR
    *
    * $udp_port_cnt: number of rows in the $udp_port[][] structure
    */
    function UDPPortCriteria(&$db, &$cs, $export_name, $element_cnt) {
        $tdb = & $db;
        $cs = & $cs;
        parent::ProtocolFieldCriteria($tdb, $cs, $export_name, $element_cnt, array(
            "layer4_sport" => _SOURCEPORT,
            "layer4_dport" => _DESTPORT
        ));
    }
    function PrintForm() {
        parent::PrintForm($this->valid_field_list, _DISPPORT, _ADDUDPPORT);
    }
    function ToSQL() {
        /* convert this criteria to SQL */
    }
    function Description() {
        return parent::Description(array_merge(array(
            "" => "",
            "=" => "="
        ) , $this->valid_field_list));
    }
}; /* UDPPortCriteria */
class UDPFieldCriteria extends ProtocolFieldCriteria {
    /*
    * $udp_field[MAX][6]: stores all other udp fields parameters/operators row
    *  - [][0] : (                            [][3] : field value
    *  - [][1] : length                       [][4] : (, )
    *  - [][2] : =, !=, <, <=, >, >=          [][5] : AND, OR
    *
    * $udp_field_cnt: number of rows in the $udp_field[][] structure
    */
    function UDPFieldCriteria(&$db, &$cs, $export_name, $element_cnt) {
        $tdb = & $db;
        $cs = & $cs;
        parent::ProtocolFieldCriteria($tdb, $cs, $export_name, $element_cnt, array(
            "udp_len" => "length",
            "udp_csum" => "chksum"
        ));
    }
    function PrintForm() {
        parent::PrintForm($this->valid_field_list, _DISPFIELD, _ADDUDPFIELD);
    }
    function ToSQL() {
        /* convert this criteria to SQL */
    }
    function Description() {
        return parent::Description(array_merge(array(
            "" => ""
        ) , $this->valid_field_list));
    }
}; /* UDPFieldCriteria */
class ICMPFieldCriteria extends ProtocolFieldCriteria {
    /*
    * $icmp_field[MAX][6]: stores all other icmp fields parameters/operators row
    *  - [][0] : (                            [][3] : field value
    *  - [][1] : code, length                 [][4] : (, )
    *  - [][2] : =, !=, <, <=, >, >=          [][5] : AND, OR
    *
    * $icmp_field_cnt: number of rows in the $icmp_field[][] structure
    */
    function ICMPFieldCriteria(&$db, &$cs, $export_name, $element_cnt) {
        $tdb = & $db;
        $cs = & $cs;
        parent::ProtocolFieldCriteria($tdb, $cs, $export_name, $element_cnt, array(
            "icmp_type" => "type",
            "icmp_code" => "code",
            "icmp_id" => "id",
            "icmp_seq" => "seq #",
            "icmp_csum" => "chksum"
        ));
    }
    function PrintForm() {
        parent::PrintForm($this->valid_field_list, _DISPFIELD, _ADDICMPFIELD);
    }
    function ToSQL() {
        /* convert this criteria to SQL */
    }
    function Description() {
        return parent::Description(array_merge(array(
            "" => ""
        ) , $this->valid_field_list));
    }
}; /* ICMPFieldCriteria */
class Layer4Criteria extends SingleElementCriteria {
    function Init() {
        $this->criteria = "";
    }
    function Clear() {
        /* clears the criteria */
    }
    function SanitizeElement() {
        $this->criteria = CleanVariable($this->criteria, "", array(
            "UDP",
            "TCP",
            "ICMP",
            "RawIP"
        ));
    }
    function PrintForm() {
        if ($this->criteria != "") echo '<INPUT TYPE="submit" class="button" NAME="submit" VALUE="' . _NOLAYER4 . '"> &nbsp';
        if ($this->criteria == "TCP") echo '  
           <INPUT TYPE="submit" class="button" NAME="submit" VALUE="UDP"> &nbsp
           <INPUT TYPE="submit" class="button" NAME="submit" VALUE="ICMP">';
        else if ($this->criteria == "UDP") echo '  
           <INPUT TYPE="submit" class="button" NAME="submit" VALUE="TCP"> &nbsp
           <INPUT TYPE="submit" class="button" NAME="submit" VALUE="ICMP">';
        else if ($this->criteria == "ICMP") echo '  
           <INPUT TYPE="submit" class="button" NAME="submit" VALUE="TCP"> &nbsp
           <INPUT TYPE="submit" class="button" NAME="submit" VALUE="UDP">';
        else echo '  
           <INPUT TYPE="submit" class="button" NAME="submit" VALUE="TCP"> &nbsp
           <INPUT TYPE="submit" class="button" NAME="submit" VALUE="UDP">
           <INPUT TYPE="submit" class="button" NAME="submit" VALUE="ICMP">';
    }
    function ToSQL() {
        /* convert this criteria to SQL */
    }
    function Description() {
        if ($this->criteria == "TCP") return _QCTCPCRIT;
        else if ($this->criteria == "UDP") return _QCUDPCRIT;
        else if ($this->criteria == "ICMP") return _QCICMPCRIT;
        else return _QCLAYER4CRIT;
    }
}; /* Layer4Criteria */
class DataCriteria extends MultipleElementCriteria {
    /*
    * $data_encode[2]: how the payload should be interpreted and converted
    *  - [0] : encoding type (hex, ascii)
    *  - [1] : conversion type (hex, ascii)
    *
    * $data[MAX][5]: stores all the payload related parameters/operators row
    *  - [][0] : (                            [][3] : (, )
    *  - [][1] : =, !=                        [][4] : AND, OR
    *  - [][2] : field value
    *
    * $data_cnt: number of rows in the $data[][] structure
    */
    var $data_encode;
    function DataCriteria(&$db, &$cs, $export_name, $element_cnt) {
        $tdb = & $db;
        $cs = & $cs;
        parent::MultipleElementCriteria($tdb, $cs, $export_name, $element_cnt, array(
            "LIKE" => _HAS,
            "NOT LIKE" => _HASNOT
        ));
        $this->data_encode = array();
    }
    function Init() {
        parent::Init();
        InitArray($this->data_encode, 2, 0, "");
    }
    function Import() {
        parent::Import();
        $this->data_encode = SetSessionVar("data_encode");
        $_SESSION['data_encode'] = & $this->data_encode;
    }
    function Clear() {
        /* clears the criteria */
    }
    function SanitizeElement($i) {
        $this->data_encode[0] = CleanVariable($this->data_encode[0], "", array(
            "hex",
            "ascii"
        ));
        $this->data_encode[1] = CleanVariable($this->data_encode[1], "", array(
            "hex",
            "ascii"
        ));
        // Make a copy of the element array
        $curArr = $this->criteria[$i];
        // Sanitize the array
        $this->criteria[$i][0] = CleanVariable($curArr[0], VAR_OPAREN);
        $this->criteria[$i][1] = CleanVariable($curArr[1], "", array_keys($this->valid_field_list));
        $this->criteria[$i][2] = CleanVariable($curArr[2], VAR_FSLASH | VAR_PERIOD | VAR_DIGIT | VAR_PUNC | VAR_LETTER);
        $this->criteria[$i][3] = CleanVariable($curArr[3], VAR_OPAREN | VAR_CPAREN);
        $this->criteria[$i][4] = CleanVariable($curArr[4], "", array(
            "AND",
            "OR"
        ));
        // Destroy the copy
        unset($curArr);
    }
    function PrintForm() {
        if (!is_array(@$this->criteria[0])) $this->criteria = array();
        echo '<B>' . _INPUTCRTENC . ':</B>';
        echo '<SELECT NAME="data_encode[0]"><OPTION VALUE=" "    ' . @chk_select($this->data_encode[0], " ") . '>' . _DISPENCODING;
        echo '                              <OPTION VALUE="hex"  ' . @chk_select($this->data_encode[0], "hex") . '>hex';
        echo '                              <OPTION VALUE="ascii"' . @chk_select($this->data_encode[0], "ascii") . '>ascii</SELECT>';
        echo '<B>' . _CONVERT2WS . ':</B>';
        echo '<SELECT NAME="data_encode[1]"><OPTION VALUE=" "    ' . @chk_select(@$this->data_encode[1], " ") . '>' . _DISPCONVERT2;
        echo '                              <OPTION VALUE="hex"  ' . @chk_select(@$this->data_encode[1], "hex") . '>hex';
        echo '                              <OPTION VALUE="ascii"' . @chk_select(@$this->data_encode[1], "ascii") . '>ascii</SELECT>';
        echo '<BR>';
        for ($i = 0; $i < $this->criteria_cnt; $i++) {
            echo '<SELECT NAME="data[' . $i . '][0]"><OPTION VALUE=" " ' . chk_select(@$this->criteria[$i][0], " ") . '>__';
            echo '                               <OPTION VALUE="("  ' . chk_select(@$this->criteria[$i][0], "(") . '>(</SELECT>';
            echo '<SELECT NAME="data[' . $i . '][1]"><OPTION VALUE=" "  ' . chk_select(@$this->criteria[$i][1], " ") . '>' . _DISPPAYLOAD;
            echo '                               <OPTION VALUE="LIKE"     ' . chk_select(@$this->criteria[$i][1], "LIKE") . '>' . _HAS;
            echo '                               <OPTION VALUE="NOT LIKE" ' . chk_select(@$this->criteria[$i][1], "NOT LIKE") . '>' . _HASNOT . '</SELECT>';
            echo '<INPUT TYPE="text" NAME="data[' . $i . '][2]" SIZE=45 VALUE="' . htmlspecialchars(@$this->criteria[$i][2]) . '">';
            echo '<SELECT NAME="data[' . $i . '][3]"><OPTION VALUE=" " ' . chk_select(@$this->criteria[$i][3], " ") . '>__';
            echo '                               <OPTION VALUE="(" ' . chk_select(@$this->criteria[$i][3], "(") . '>(';
            echo '                               <OPTION VALUE=")" ' . chk_select(@$this->criteria[$i][3], ")") . '>)</SELECT>';
            echo '<SELECT NAME="data[' . $i . '][4]"><OPTION VALUE=" "   ' . chk_select(@$this->criteria[$i][4], " ") . '>__';
            echo '                               <OPTION VALUE="OR" ' . chk_select(@$this->criteria[$i][4], "OR") . '>' . _OR;
            echo '                               <OPTION VALUE="AND" ' . chk_select(@$this->criteria[$i][4], "AND") . '>' . _AND . '</SELECT>';
            if ($i == $this->criteria_cnt - 1) echo '    <INPUT TYPE="submit" class="button" NAME="submit" VALUE="' . _ADDPAYLOAD . '">';
            echo '<BR>';
        }
    }
    function ToSQL() {
        /* convert this criteria to SQL */
    }
    function Description() {
        $human_fields["LIKE"] = _CONTAINS;
        $human_fields["NOT LIKE"] = _DOESNTCONTAIN;
        $human_fields[""] = "";
        $tmp = "";
        if ($this->data_encode[0] != " " && $this->data_encode[1] != " ") {
            $tmp = $tmp . ' (' . _DENCODED . ' ' . $this->data_encode[0];
            $tmp = $tmp . ' => ' . (($this->data_encode[1] == "") ? "ascii" : $this->data_encode[1]);
            $tmp = $tmp . ')<BR>';
        } else $tmp = $tmp . ' ' . _NODENCODED . '<BR>';
        for ($i = 0; $i < $this->criteria_cnt; $i++) {
            if ($this->criteria[$i][1] != " " && $this->criteria[$i][2] != "") $tmp = $tmp . $this->criteria[$i][0] . $human_fields[$this->criteria[$i][1]] . ' "' . $this->criteria[$i][2] . '" ' . $this->criteria[$i][3] . ' ' . $this->criteria[$i][4];
        }
        if ($tmp != "") $tmp = $tmp . $this->cs->GetClearCriteriaString($this->export_name) . '<BR>';
        return $tmp;
    }
};
class OssimPriorityCriteria extends SingleElementCriteria {
    function Init() {
        $this->criteria = "";
    }
    function Clear() {
        /* clears the criteria */
    }
    function SanitizeElement() {
        $this->criteria[0] = CleanVariable($this->criteria[0], "", array(
            "=",
            "!=",
            "<",
            "<=",
            ">",
            ">="
        ));
        $this->criteria[1] = CleanVariable($this->criteria[1], VAR_DIGIT, array(
            "null"
        ));
    }
    function PrintForm() {
        if ($this->db->baseGetDBVersion() >= 103) {
            echo '<SELECT NAME="ossim_priority[0]">
                <OPTION VALUE=" " ' . chk_select($this->criteria[0], "=") . '>__</OPTION>
                <OPTION VALUE="=" ' . chk_select($this->criteria[0], "=") . '>==</OPTION>
                <OPTION VALUE="!=" ' . chk_select($this->criteria[0], "!=") . '>!=</OPTION>
                <OPTION VALUE="<"  ' . chk_select($this->criteria[0], "<") . '><</OPTION>
                <OPTION VALUE=">"  ' . chk_select($this->criteria[0], ">") . '>></OPTION>
                <OPTION VALUE="<=" ' . chk_select($this->criteria[0], "><=") . '><=</OPTION>
                <OPTION VALUE=">=" ' . chk_select($this->criteria[0], ">=") . '>>=</SELECT>';
            echo '<SELECT NAME="ossim_priority[1]">
                <OPTION VALUE="" ' . chk_select($this->criteria[1], " ") . '>{ any Priority }</OPTION>
                <OPTION VALUE="0" ' . chk_select($this->criteria[1], "0") . '>0</OPTION>
                <OPTION VALUE="1" ' . chk_select($this->criteria[1], "1") . '>1</OPTION>
                <OPTION VALUE="2" ' . chk_select($this->criteria[1], "2") . '>2</OPTION>
                <OPTION VALUE="3" ' . chk_select($this->criteria[1], "3") . '>3</OPTION>
                <OPTION VALUE="4" ' . chk_select($this->criteria[1], "4") . '>4</OPTION>
                <OPTION VALUE="5" ' . chk_select($this->criteria[1], "5") . '>5</OPTION>';
            echo '</SELECT>&nbsp;&nbsp';
        }
    }
    function ToSQL() {
        /* convert this criteria to SQL */
    }
    function Description() {
        $tmp = "";
        if ($this->db->baseGetDBVersion() >= 103) {
            if ($this->criteria[1] != " " && $this->criteria[1] != "") {
                if ($this->criteria[1] == null) $tmp = $tmp . 'Ossim Priority = ' . '<I>none</I><BR>';
                else $tmp = $tmp . 'priority ' . $this->criteria[0] . " " . $this->criteria[1] . $this->cs->GetClearCriteriaString($this->export_name) . '<BR>';
            }
        }
        return $tmp;
    }
}; /* OssimPriorityCriteria */
class OssimRiskACriteria extends SingleElementCriteria {
    function Init() {
        $this->criteria = "";
    }
    function Clear() {
        /* clears the criteria */
    }
    function SanitizeElement() {
        $this->criteria = CleanVariable($this->criteria, VAR_DIGIT | VAR_LETTER);
    }
    function PrintForm() {
        echo '<SELECT NAME="ossim_risk_a">
             <OPTION VALUE=" " ' . chk_select($this->criteria, " ") . '>{ any risk }</OPTION>
	     <OPTION VALUE="low" ' . chk_select($this->criteria, "low") . '>Low</OPTION>
             <OPTION VALUE="medium" ' . chk_select($this->criteria, "medium") . '>Medium</OPTION>
	     <OPTION VALUE="high" ' . chk_select($this->criteria, "high") . '>High</OPTION>';
        echo '</SELECT>&nbsp;&nbsp';
    }
    function ToSQL() {
        /* convert this criteria to SQL */
    }
    function Description() {
        $tmp = "";
        if ($this->criteria != " " && $this->criteria != "") $tmp = $tmp . 'risk = [' . $this->criteria . '] ' . "" . $this->cs->GetClearCriteriaString($this->export_name) . '<BR>';
        return $tmp;
    }
}; /* OssimRiskACriteria */
class OssimRiskCCriteria extends SingleElementCriteria {
    function Init() {
        $this->criteria = "";
    }
    function Clear() {
        /* clears the criteria */
    }
    function SanitizeElement() {
        $this->criteria[0] = CleanVariable($this->criteria[0], "", array(
            "=",
            "!=",
            "<",
            "<=",
            ">",
            ">="
        ));
        $this->criteria[1] = CleanVariable($this->criteria[1], VAR_DIGIT, array(
            "null"
        ));
    }
    function PrintForm() {
        if ($this->db->baseGetDBVersion() >= 103) {
            echo '<SELECT NAME="ossim_risk_c[0]">
                <OPTION VALUE=" " ' . chk_select($this->criteria[0], "=") . '>__</OPTION>
                <OPTION VALUE="=" ' . chk_select($this->criteria[0], "=") . '>==</OPTION>
                <OPTION VALUE="!=" ' . chk_select($this->criteria[0], "!=") . '>!=</OPTION>
                <OPTION VALUE="<"  ' . chk_select($this->criteria[0], "<") . '><</OPTION>
                <OPTION VALUE=">"  ' . chk_select($this->criteria[0], ">") . '>></OPTION>
                <OPTION VALUE="<=" ' . chk_select($this->criteria[0], "><=") . '><=</OPTION>
                <OPTION VALUE=">=" ' . chk_select($this->criteria[0], ">=") . '>>=</SELECT>';
            echo '<SELECT NAME="ossim_risk_c[1]">
                <OPTION VALUE="" ' . chk_select($this->criteria[1], " ") . '>{ any Risk }</OPTION>
                <OPTION VALUE="0" ' . chk_select($this->criteria[1], "0") . '>0</OPTION>
                <OPTION VALUE="1" ' . chk_select($this->criteria[1], "1") . '>1</OPTION>
                <OPTION VALUE="2" ' . chk_select($this->criteria[1], "2") . '>2</OPTION>
                <OPTION VALUE="3" ' . chk_select($this->criteria[1], "3") . '>3</OPTION>
                <OPTION VALUE="4" ' . chk_select($this->criteria[1], "4") . '>4</OPTION>
                <OPTION VALUE="5" ' . chk_select($this->criteria[1], "5") . '>5</OPTION>
                <OPTION VALUE="6" ' . chk_select($this->criteria[1], "6") . '>6</OPTION>
                <OPTION VALUE="7" ' . chk_select($this->criteria[1], "7") . '>7</OPTION>
                <OPTION VALUE="8" ' . chk_select($this->criteria[1], "8") . '>8</OPTION>
                <OPTION VALUE="9" ' . chk_select($this->criteria[1], "9") . '>9</OPTION>
                <OPTION VALUE="10" ' . chk_select($this->criteria[1], "10") . '>10</OPTION>';
            echo '</;SELECT>&nbsp;&nbsp';
        }
    }
    function ToSQL() {
        /* convert this criteria to SQL */
    }
    function Description() {
        $tmp = "";
        if ($this->db->baseGetDBVersion() >= 103) {
            if ($this->criteria[1] != " " && $this->criteria[1] != "") {
                if ($this->criteria[1] == null) $tmp = $tmp . 'risk = ' . '<I>none</I><BR>';
                else $tmp = $tmp . 'risk ' . $this->criteria[0] . " " . $this->criteria[1] . $this->cs->GetClearCriteriaString($this->export_name) . '<BR>';
            }
        }
        return $tmp;
    }
}; /* OssimRiskCCriteria */
class OssimReliabilityCriteria extends SingleElementCriteria {
    function Init() {
        $this->criteria = "";
    }
    function Clear() {
        /* clears the criteria */
    }
    function SanitizeElement() {
        $this->criteria[0] = CleanVariable($this->criteria[0], "", array(
            "=",
            "!=",
            "<",
            "<=",
            ">",
            ">="
        ));
        $this->criteria[1] = CleanVariable($this->criteria[1], VAR_DIGIT, array(
            "null"
        ));
    }
    function PrintForm() {
        if ($this->db->baseGetDBVersion() >= 103) {
            echo '<SELECT NAME="ossim_reliability[0]">
                <OPTION VALUE=" " ' . chk_select($this->criteria[0], "=") . '>__</OPTION>
                <OPTION VALUE="=" ' . chk_select($this->criteria[0], "=") . '>==</OPTION>
                <OPTION VALUE="!=" ' . chk_select($this->criteria[0], "!=") . '>!=</OPTION>
                <OPTION VALUE="<"  ' . chk_select($this->criteria[0], "<") . '><</OPTION>
                <OPTION VALUE=">"  ' . chk_select($this->criteria[0], ">") . '>></OPTION>
                <OPTION VALUE="<=" ' . chk_select($this->criteria[0], "><=") . '><=</OPTION>
                <OPTION VALUE=">=" ' . chk_select($this->criteria[0], ">=") . '>>=</SELECT>';
            echo '<SELECT NAME="ossim_reliability[1]">
                <OPTION VALUE="" ' . chk_select($this->criteria[1], " ") . '>{ any Reliability }</OPTION>
                <OPTION VALUE="0" ' . chk_select($this->criteria[1], "0") . '>0</OPTION>
                <OPTION VALUE="1" ' . chk_select($this->criteria[1], "1") . '>1</OPTION>
                <OPTION VALUE="2" ' . chk_select($this->criteria[1], "2") . '>2</OPTION>
                <OPTION VALUE="3" ' . chk_select($this->criteria[1], "3") . '>3</OPTION>
                <OPTION VALUE="4" ' . chk_select($this->criteria[1], "4") . '>4</OPTION>
                <OPTION VALUE="5" ' . chk_select($this->criteria[1], "5") . '>5</OPTION>
                <OPTION VALUE="6" ' . chk_select($this->criteria[1], "6") . '>6</OPTION>
                <OPTION VALUE="7" ' . chk_select($this->criteria[1], "7") . '>7</OPTION>
                <OPTION VALUE="8" ' . chk_select($this->criteria[1], "8") . '>8</OPTION>
                <OPTION VALUE="9" ' . chk_select($this->criteria[1], "9") . '>9</OPTION>
                <OPTION VALUE="10" ' . chk_select($this->criteria[1], "10") . '>10</OPTION>';
            echo '</SELECT>&nbsp;&nbsp';
        }
    }
    function ToSQL() {
        /* convert this criteria to SQL */
    }
    function Description() {
        $tmp = "";
        if ($this->db->baseGetDBVersion() >= 103) {
            if ($this->criteria[1] != " " && $this->criteria[1] != "") {
                if ($this->criteria[1] == null) $tmp = $tmp . 'reliability = ' . '<I>none</I><BR>';
                else $tmp = $tmp . 'reliability ' . $this->criteria[0] . " " . $this->criteria[1] . $this->cs->GetClearCriteriaString($this->export_name) . '<BR>';
            }
        }
        return $tmp;
    }
}; /* OssimReliabilityCriteria */
class OssimAssetSrcCriteria extends SingleElementCriteria {
    function Init() {
        $this->criteria = "";
    }
    function Clear() {
        /* clears the criteria */
    }
    function SanitizeElement() {
        $this->criteria[0] = CleanVariable($this->criteria[0], "", array(
            "=",
            "!=",
            "<",
            "<=",
            ">",
            ">="
        ));
        $this->criteria[1] = CleanVariable($this->criteria[1], VAR_DIGIT, array(
            "null"
        ));
    }
    function PrintForm() {
        if ($this->db->baseGetDBVersion() >= 103) {
            echo '<SELECT NAME="ossim_asset_src[0]">
                <OPTION VALUE=" " ' . chk_select($this->criteria[0], "=") . '>__</OPTION>
                <OPTION VALUE="=" ' . chk_select($this->criteria[0], "=") . '>==</OPTION>
                <OPTION VALUE="!=" ' . chk_select($this->criteria[0], "!=") . '>!=</OPTION>
                <OPTION VALUE="<"  ' . chk_select($this->criteria[0], "<") . '><</OPTION>
                <OPTION VALUE=">"  ' . chk_select($this->criteria[0], ">") . '>></OPTION>
                <OPTION VALUE="<=" ' . chk_select($this->criteria[0], "><=") . '><=</OPTION>
                <OPTION VALUE=">=" ' . chk_select($this->criteria[0], ">=") . '>>=</SELECT>';
            echo '<SELECT NAME="ossim_asset_src[1]">
                <OPTION VALUE="" ' . chk_select($this->criteria[1], " ") . '>{ any Asset }</OPTION>
                <OPTION VALUE="0" ' . chk_select($this->criteria[1], "0") . '>0</OPTION>
                <OPTION VALUE="1" ' . chk_select($this->criteria[1], "1") . '>1</OPTION>
                <OPTION VALUE="2" ' . chk_select($this->criteria[1], "2") . '>2</OPTION>
                <OPTION VALUE="3" ' . chk_select($this->criteria[1], "3") . '>3</OPTION>
                <OPTION VALUE="4" ' . chk_select($this->criteria[1], "4") . '>4</OPTION>
                <OPTION VALUE="5" ' . chk_select($this->criteria[1], "5") . '>5</OPTION>';
            echo '</SELECT>&nbsp;&nbsp';
        }
    }
    function ToSQL() {
        /* convert this criteria to SQL */
    }
    function Description() {
        $tmp = "";
        if ($this->db->baseGetDBVersion() >= 103) {
            if ($this->criteria[1] != " " && $this->criteria[1] != "") {
                if ($this->criteria[1] == null) $tmp = $tmp . 'asset = ' . '<I>none</I><BR>';
                else $tmp = $tmp . 'asset ' . $this->criteria[0] . " " . $this->criteria[1] . $this->cs->GetClearCriteriaString($this->export_name) . '<BR>';
            }
        }
        return $tmp;
    }
}; /* OssimAssetSrcCriteria */
class OssimAssetDstCriteria extends SingleElementCriteria {
    function Init() {
        $this->criteria = "";
    }
    function Clear() {
        /* clears the criteria */
    }
    function SanitizeElement() {
        $this->criteria[0] = CleanVariable($this->criteria[0], "", array(
            "=",
            "!=",
            "<",
            "<=",
            ">",
            ">="
        ));
        $this->criteria[1] = CleanVariable($this->criteria[1], VAR_DIGIT, array(
            "null"
        ));
    }
    function PrintForm() {
        if ($this->db->baseGetDBVersion() >= 103) {
            echo '<SELECT NAME="ossim_asset_dst[0]">
                <OPTION VALUE=" " ' . chk_select($this->criteria[0], "=") . '>__</OPTION>
                <OPTION VALUE="=" ' . chk_select($this->criteria[0], "=") . '>==</OPTION>
                <OPTION VALUE="!=" ' . chk_select($this->criteria[0], "!=") . '>!=</OPTION>
                <OPTION VALUE="<"  ' . chk_select($this->criteria[0], "<") . '><</OPTION>
                <OPTION VALUE=">"  ' . chk_select($this->criteria[0], ">") . '>></OPTION>
                <OPTION VALUE="<=" ' . chk_select($this->criteria[0], "><=") . '><=</OPTION>
                <OPTION VALUE=">=" ' . chk_select($this->criteria[0], ">=") . '>>=</SELECT>';
            echo '<SELECT NAME="ossim_asset_dst[1]">
                <OPTION VALUE="" ' . chk_select($this->criteria[1], " ") . '>{ any Asset }</OPTION>
 	        <OPTION VALUE="0" ' . chk_select($this->criteria[1], "0") . '>0</OPTION>
		<OPTION VALUE="1" ' . chk_select($this->criteria[1], "1") . '>1</OPTION>
		<OPTION VALUE="2" ' . chk_select($this->criteria[1], "2") . '>2</OPTION>
		<OPTION VALUE="3" ' . chk_select($this->criteria[1], "3") . '>3</OPTION>
		<OPTION VALUE="4" ' . chk_select($this->criteria[1], "4") . '>4</OPTION>
		<OPTION VALUE="5" ' . chk_select($this->criteria[1], "5") . '>5</OPTION>';
            echo '</SELECT>&nbsp;&nbsp';
        }
    }
    function ToSQL() {
        /* convert this criteria to SQL */
    }
    function Description() {
        $tmp = "";
        if ($this->db->baseGetDBVersion() >= 103) {
            if ($this->criteria[1] != " " && $this->criteria[1] != "") {
                if ($this->criteria[1] == null) $tmp = $tmp . 'asset = ' . '<I>none</I><BR>';
                else $tmp = $tmp . 'asset ' . $this->criteria[0] . " " . $this->criteria[1] . $this->cs->GetClearCriteriaString($this->export_name) . '<BR>';
            }
        }
        return $tmp;
    }
}; /* OssimAssetDstCriteria */
class OssimTypeCriteria extends SingleElementCriteria {
    function Init() {
        $this->criteria = "";
    }
    function Clear() {
        /* clears the criteria */
    }
    function SanitizeElement() {
        $this->criteria = CleanVariable($this->criteria, VAR_DIGIT);
    }
    function PrintForm() {
        if ($this->db->baseGetDBVersion() >= 103) {
            echo '<SELECT NAME="ossim_type[1]">
                <OPTION VALUE="" ' . chk_select($this->criteria[1], " ") . '>{ any }</OPTION>
                <OPTION VALUE="2" ' . chk_select($this->criteria[1], "2") . '>Alarm</OPTION>';
            echo '</SELECT>&nbsp;&nbsp';
        }
    }
    function ToSQL() {
        /* convert this criteria to SQL */
    }
    function Description() {
        $tmp = "";
        if ($this->db->baseGetDBVersion() >= 103) {
            if ($this->criteria[1] != " " && $this->criteria[1] != "") {
                if ($this->criteria[1] == null) $tmp = $tmp . 'type = ' . '<I>none</I><BR>';
                else $tmp = $tmp . 'type ' . $this->criteria[0] . " " . $this->criteria[1] . $this->cs->GetClearCriteriaString($this->export_name) . '<BR>';
            }
        }
        return $tmp;
    }
}; /* OssimTypeCriteria */
?>