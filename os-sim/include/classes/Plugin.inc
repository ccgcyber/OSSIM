<?php
/*****************************************************************************
*
*    License:
*
*   Copyright (c) 2003-2006 ossim.net
*   Copyright (c) 2007-2009 AlienVault
*   All rights reserved.
*
*   This package is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; version 2 dated June, 1991.
*   You may not use, modify or distribute this program under any other version
*   of the GNU General Public License.
*
*   This package is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*   along with this package; if not, write to the Free Software
*   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,
*   MA  02110-1301  USA
*
*
* On Debian GNU/Linux systems, the complete text of the GNU General
* Public License can be found in `/usr/share/common-licenses/GPL-2'.
*
* Otherwise you can read it here: http://www.gnu.org/licenses/gpl-2.0.txt
****************************************************************************/
/**
* Class and Function List:
* Function list:
* - Plugin()
* - get_id()
* - get_type()
* - get_name()
* - get_description()
* - get_foundrows()
* - get_list()
* Classes list:
* - Plugin
*/
require_once ("ossim_db.inc");
class Plugin {
    var $id;
    var $type;
    var $name;
    var $description;
    function Plugin($id, $type, $name, $description, $foundrows = 0) {
        $this->id = $id;
        $this->type = $type;
        $this->name = $name;
        $this->description = $description;
        $this->foundrows = $foundrows;
    }
    function get_id() {
        return $this->id;
    }
    function get_type() {
        return $this->type;
    }
    function get_name() {
        return $this->name;
    }
    function get_description() {
        return $this->description;
    }
    function get_foundrows() {
        return $this->foundrows;
    }
    function get_list($conn, $args = "") {
        $iffoundrows = preg_match("/LIMIT/", $args) ? true : false;
        $select = ($iffoundrows) ? "SQL_CALC_FOUND_ROWS *" : "*";
        $query = OssimQuery("SELECT $select FROM plugin $args");
        if (!$rs = & $conn->Execute($query)) {
            print $conn->ErrorMsg();
        } else {
            $foundrows = 0;
            if ($iffoundrows) {
                if (!$rf = & $conn->Execute("SELECT FOUND_ROWS() as total")) print $conn->ErrorMsg();
                else $foundrows = $rf->fields["total"];
            }
            $list = array();
            while (!$rs->EOF) {
                $list[] = new Plugin($rs->fields["id"], $rs->fields["type"], $rs->fields["name"], $rs->fields["description"], $foundrows);
                $rs->MoveNext();
            }
        }
        return $list;
    }
    
    function get_id_and_name($conn, $args = "") {
        $list = array();
        $query = OssimQuery("SELECT id, name FROM plugin $args");
        if (!$rs = & $conn->Execute($query)) {
            print $conn->ErrorMsg();
        } else {
            while (!$rs->EOF) {
                $list[$rs->fields["id"]]=$rs->fields["name"];
                $rs->MoveNext();
            }
        }
        return $list;
    }
    
	function get_name_by_id($conn,$id) {
		$query = OssimQuery("SELECT name FROM plugin WHERE id=$id");
		if (!$rs = & $conn->Execute($query)) {
			print $conn->ErrorMsg();
			return 0;
		} else {
			return $rs->fields["name"];
		}
	}
	function get_latest_SIM_Event ($conn,$id,$name) {
		// get plugin sids
		$sids = array();
		$temp_sql = OssimQuery("SELECT sid,hostname FROM sensor WHERE hostname like '%-$name'");
		if (!$rs = & $conn->Execute($temp_sql)) {
			print $conn->ErrorMsg();
			return array();
		} else {
			while (!$rs->EOF) {
				$sids[] = $rs->fields["sid"];
				$rs->MoveNext();
			}
		}
		$sql = OssimQuery("select acid_event.*,plugin_sid.name as sig_name from acid_event left join ossim.plugin_sid on plugin_sid.plugin_id=acid_event.plugin_id and plugin_sid.sid=acid_event.plugin_sid where acid_event.plugin_id=$id order by acid_event.timestamp desc LIMIT 1");
		if (!$rs = & $conn->Execute($sql)) {
			print $conn->ErrorMsg();
			return array();
		} else {
			$rs->fields["sids"] = implode(",",$sids);
			return $rs->fields;
		}
	}
}
?>