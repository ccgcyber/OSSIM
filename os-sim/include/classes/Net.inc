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
* - Net()
* - get_name()
* - get_name_by_ip()
* - get_ips()
* - get_asset()
* - get_threshold_c()
* - get_threshold_a()
* - get_rrd_profile()
* - get_alert()
* - get_persistence()
* - get_descr()
* - get_foundrows()
* - get_sensors()
* - get_list()
* - get_all()
* - insert()
* - update()
* - delete()
* - netthresh_c()
* - net_asset()
* - netthresh_a()
* - isIpInNet()
* - expand_CIDR()
* - enable_plugin()
* - disable_plugin()
* - isIpInAnyNet()
* - GetClosestNet()
* - check_orphan()
* - get_related_sensors()
* Classes list:
* - Net
*/
require_once 'classes/Net_qualification.inc';
require_once 'classes/Net_vulnerability.inc';
require_once 'classes/Net_sensor_reference.inc';
require_once 'classes/Net_group.inc';
require_once 'classes/Control_panel_net.inc';
require_once 'classes/Session.inc';
require_once 'ossim_conf.inc';
require_once 'classes/Log_action.inc';
require_once ("ossim_db.inc");
require_once 'classes/WebIndicator.inc';
class Net {
    var $name;
    var $ips;
    var $asset;
    var $threshold_c;
    var $threshold_a;
    var $rrd_profile;
    var $alert;
    var $persistence;
    var $descr;
    function Net($name, $ips, $asset, $threshold_c, $threshold_a, $rrd_profile, $alert, $persistence, $descr, $foundrows = 0) {
        $this->name = $name;
        $this->ips = $ips;
        $this->asset = $asset;
        $this->threshold_c = $threshold_c;
        $this->threshold_a = $threshold_a;
        $this->rrd_profile = $rrd_profile;
        $this->alert = $alert;
        $this->persistence = $persistence;
        $this->descr = $descr;
        $this->foundrows = $foundrows;
    }
    function get_name() {
        return $this->name;
    }
    function get_ips() {
        return $this->ips;
    }
    function get_asset() {
        return $this->asset;
    }
    function get_threshold_c() {
        return $this->threshold_c;
    }
    function get_threshold_a() {
        return $this->threshold_a;
    }
    function get_rrd_profile() {
        return $this->rrd_profile;
    }
    function get_alert() {
        return $this->alert;
    }
    function get_persistence() {
        return $this->persistence;
    }
    function get_descr() {
        return $this->descr;
    }
    function get_foundrows() {
        return $this->foundrows;
    }
    function get_sensors($conn) {
        return Net_sensor_reference::get_list($conn, $this->name);
    }
	function get_name_by_ip($conn,$ip) {
		$name = $ip;
		$query = OssimQuery("SELECT name FROM net WHERE ips like '%$ip%'");
        if (!$rs = & $conn->Execute($query)) {
            print $conn->ErrorMsg();
        } else {
			$name = $rs->fields["name"];
		}
		return $name;
	}
	function get_ips_by_name($conn,$name) {
		$ips = "";
		$query = OssimQuery("SELECT ips FROM net WHERE name='$name'");
        if (!$rs = & $conn->Execute($query)) {
            print $conn->ErrorMsg();
        } else {
			$ips = $rs->fields["ips"];
		}
		return $ips;
	}
    
    function get_list($conn, $args = "") {
        $list = array();
        $iffoundrows = preg_match("/LIMIT/", $args) ? true : false;
        $select = ($iffoundrows) ? "SQL_CALC_FOUND_ROWS *" : "*";
        $query = OssimQuery("SELECT $select FROM net $args");
        if (!$rs = & $conn->Execute($query)) {
            print $conn->ErrorMsg();
        } else {
            $foundrows = 0;
            if ($iffoundrows) {
                if (!$rf = & $conn->Execute("SELECT FOUND_ROWS() as total")) print $conn->ErrorMsg();
                else $foundrows = $rf->fields["total"];
            }
            while (!$rs->EOF) {
                if (Session::netAllowed($conn, $rs->fields["name"])) {
                    $list[] = new Net($rs->fields["name"], $rs->fields["ips"], $rs->fields["asset"], $rs->fields["threshold_c"], $rs->fields["threshold_a"], $rs->fields["rrd_profile"], $rs->fields["alert"], $rs->fields["persistence"], $rs->fields["descr"], $foundrows);
                }
                $rs->MoveNext();
            }
        }
        return $list;
    }
    
    function get_list_pag($conn, $args = "") {
        $list = array(); $i=0;
        $from = 0; $max = 999999999;
        if (preg_match("/LIMIT (\d+),\s*(\d+)/", $args, $found)) {
            $args = preg_replace("/LIMIT \d+,\s*\d+/","",$args);
            $from = $found[1]; $max = $found[2];
        }
        $query = OssimQuery("SELECT * FROM net $args");
        
        if (!$rs = & $conn->Execute($query)) {
            print $conn->ErrorMsg();
        } else {
            $foundrows = $rs->RecordCount();
            while (!$rs->EOF) {
				if (Session::netAllowed($conn, $rs->fields["name"])) {
					if ($i>=$from && $i<$max+$from) {
                        $list[] = new Net($rs->fields["name"], $rs->fields["ips"], $rs->fields["asset"], $rs->fields["threshold_c"], $rs->fields["threshold_a"], $rs->fields["rrd_profile"], $rs->fields["alert"], $rs->fields["persistence"], $rs->fields["descr"], $foundrows);
                    }
                    $i++;
                }
                $rs->MoveNext();
            }
        }
        return array($list,$i);
    }


    function get_all($conn,$checkperm=false) {
        $list = array();
        $sql = "SELECT * FROM net ORDER BY name ASC";
        if (!$rs = $conn->Execute($sql)) {
            die($conn->ErrorMsg());
        }
        while (!$rs->EOF) {
            if (!$checkperm || ($checkperm && Session::netAllowed($conn, $rs->fields["name"])))
                $list[] = new Net($rs->fields["name"], $rs->fields["ips"], $rs->fields["asset"], $rs->fields["threshold_c"], $rs->fields["threshold_a"], $rs->fields["rrd_profile"], $rs->fields["alert"], $rs->fields["persistence"], $rs->fields["descr"]);
            $rs->MoveNext();
        }
        return $list;
    }
    function insert($conn, $name, $ips, $asset, $threshold_c, $threshold_a, $rrd_profile, $alert, $persistence, $sensors, $descr) {
        settype($asset, "int");
        settype($threshold_c, "int");
        settype($threshold_a, "int");
        settype($alert, "int");
        settype($persistence, "int");
        $rrd_profile = htmlentities($rrd_profile);
        $persistence = htmlentities($persistence);
        $name = str_replace(" ", "_", $name);
        $ips = str_replace(" ", "", $ips);
        $sql = "INSERT INTO net (name, ips, asset, threshold_c, threshold_a, rrd_profile, alert, persistence, descr) VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = array(
            $name,
            $ips,
            $asset,
            $threshold_c,
            $threshold_a,
            $rrd_profile,
            $alert,
            $persistence,
            $descr
        );
        if ($conn->Execute($sql, $params) === false) {
            print 'error inserting: ' . $conn->ErrorMsg() . '<BR>';
            exit;
        }
        Net_qualification::insert($conn, $name, 1, 1);
        Net_vulnerability::insert($conn, $name, 0);
        Net_sensor_reference::insert($conn, $name, $sensors);
        $infolog = array(
            $name
        );
        Log_action::log(27, $infolog);
        WebIndicator::set_on("Reload_nets");
        WebIndicator::set_on("ReloadPolicy");
    }
    function update($conn, $name, $ips, $asset, $threshold_c, $threshold_a, $rrd_profile, $alert, $persistence, $sensors, $descr) {
        $skip_group_update = 1;
        Net::delete($conn, $name, $skip_group_update);
        Net::insert($conn, $name, $ips, $asset, $threshold_c, $threshold_a, $rrd_profile, $alert, $persistence, $sensors, $descr);
        $infolog = array(
            $name
        );
        Log_action::log(28, $infolog);
        WebIndicator::set_on("Reload_nets");
        WebIndicator::set_on("ReloadPolicy");
    }
    function delete($conn, $name, $skip_group_update = 0) {
        $sql = "DELETE FROM net WHERE name = ?";
        $params = array(
            $name
        );
        if ($conn->Execute($sql, $params) === false) {
            print 'error deleting: ' . $conn->ErrorMsg() . '<BR>';
            exit;
        }
        Control_panel_net::delete($conn, $name);
        Net_qualification::delete($conn, $name);
        Net_vulnerability::delete($conn, $name);
        if ($skip_group_update != 1) {
            Net_group_reference::check_orphan($conn, $name);
        }
        Net_sensor_reference::delete($conn, $name);
        $infolog = array(
            $name
        );
        Log_action::log(29, $infolog);
        WebIndicator::set_on("Reload_nets");
        WebIndicator::set_on("ReloadPolicy");
    }
	function can_delete($conn, $name) {
		$sql = "SELECT count(*) as num FROM policy_net_reference WHERE net_name = ?";
		$params = array(
            $name
        );
        if (!$rs = & $conn->Execute($sql, $params)) {
            print 'error deleting: ' . $conn->ErrorMsg() . '<BR>';
            exit;
        }
		if ($rs->fields['num'] == '0') return 1;
		else return 0;
	}
    function netthresh_c($conn, $name) {
        $sql = "SELECT * FROM net WHERE name = ?";
        $params = array(
            $name
        );
        if (!$rs = $conn->Execute($sql, $params)) {
            print 'error: ' . $conn->ErrorMsg() . '<BR>';
            exit;
        }
        if (!$rs->EOF) return $rs->fields["threshold_c"];
        else {
            /* net is not in db, return default threshold */
            $conf = $GLOBALS["CONF"];
            return $conf->get_conf("threshold");
        }
    }
	function get_descr_by_name($conn, $name) {
        $sql = "SELECT * FROM net WHERE name = ?";
        $params = array(
            $name
        );
        if (!$rs = $conn->Execute($sql, $params)) {
            print 'error: ' . $conn->ErrorMsg() . '<BR>';
            exit;
        }
        if (!$rs->EOF) return ($rs->fields["description"] != "") ? $rs->fields["description"] : "No description found";
        else {
            return "No description found";
        }
    }
    function net_asset($conn, $name) {
        $sql = "SELECT * FROM net WHERE name = ?";
        $params = array(
            $name
        );
        if (!$rs = $conn->Execute($sql, $params)) {
            print 'error: ' . $conn->ErrorMsg() . '<BR>';
            exit;
        }
        if ($name == "") {
            /* Return default asset: 2 */
            return 2;
        }
        if (!$rs->EOF) {
            return $rs->fields["asset"];
        } else {
            /* Return default asset: 2 */
            return 2;
        }
    }
    function netthresh_a($conn, $name) {
        $sql = "SELECT * FROM net WHERE name = ?";
        $params = array(
            $name
        );
        if (!$rs = $conn->Execute($sql, $params)) {
            print 'error: ' . $conn->ErrorMsg() . '<BR>';
            exit;
        }
        if (!$rs->EOF) return $rs->fields["threshold_a"];
        else {
            /* net is not in db, return default threshold */
            $conf = $GLOBALS["CONF"];
            return $conf->get_conf("threshold");
        }
    }
    /*
    * PRE:
    *   ip in ipv4 format (X.X.X.X)
    *   nets in net/mask format (X.X.X.X/X,X.X.X.X/X,...)
    */
    function isIpInNet($ip, $nets) {
        $net_list = explode(",", $nets);
        foreach($net_list as $n) {
			list($net, $mask) = split("/", $n);
            $val1 = ip2long($ip);
            $val2 = ip2long($net);
            if (($val1 >> (32 - $mask)) == ($val2 >> (32 - $mask))) return true;
        }
        return false;
    }
    function expand_CIDR($nets,$return_type="FULL",$return_format="IP") {
        require_once ('classes/CIDR.inc');
        $ip_range = array();
        if (!is_array($net_list = explode(",", $nets))) return NULL;
        foreach($net_list as $n) {
            if (is_array($cidr_range = CIDR::expand_CIDR($n, $return_type, $return_format)))
				if ($return_type == "SHORT" && $return_format == "LONG") {
					$ip_range[0] = ($cidr_range[0] < $ip_range[0] || $ip_range[0] < 1) ? $cidr_range[0] : $ip_range[0];
					$ip_range[1] = ($cidr_range[1] > $ip_range[1]) ? $cidr_range[1] : $ip_range[1];
				} else {
					$ip_range = array_merge($ip_range, $cidr_range);
				}
        }
        return $ip_range;
    }
    function enable_plugin($conn, $net_name, $plugin) {
        $plugin = intval($plugin);
        $sql = "INSERT INTO net_scan (net_name, plugin_id, plugin_sid) VALUES (?, ?, 0)";
        $params = array(
            $net_name,
            $plugin
        );
        if ($conn->Execute($sql, $params) === false) {
            print 'error inserting: ' . $conn->ErrorMsg() . '<BR>';
            exit;
        }
    }
    function disable_plugin($conn, $net_name, $plugin) {
        $plugin = intval($plugin);
        $sql = "DELETE FROM net_scan WHERE net_name = ? AND plugin_id = ?";
        $params = array(
            $net_name,
            $plugin
        );
        if ($conn->Execute($sql, $params) === false) {
            print 'error deleting: ' . $conn->ErrorMsg() . '<BR>';
            exit;
        }
    }
    function isIpInAnyNet($conn, $ip) {
        $net_list = Net::get_list($conn);
        if (!is_array($net_list)) return false;
        foreach($net_list as $n) {
            list($net, $mask) = split("/", $n->get_ips());
            $val1 = ip2long($ip);
            $val2 = ip2long($net);
            if (($val1 >> (32 - $mask)) == ($val2 >> (32 - $mask))) return true;
        }
        return false;
    }
    function GetClosestNet($conn, $ip, $return_type=0) {
        $net_list = Net::get_list($conn);
        $narrowest_mask = 0;
        $narrowest_net = "";
        if (!is_array($net_list)) return false;
        foreach($net_list as $n) {
            list($net, $mask) = split("/", $n->get_ips());
            $val1 = ip2long($ip);
            $val2 = ip2long($net);
            if (($val1 >> (32 - $mask)) == ($val2 >> (32 - $mask))) {
                if ($mask > $narrowest_mask) {
                    $narrowest_mask = $mask;
                    $narrowest_net = ($return_type) ? $n->get_ips() : $n->get_name();
                }
            }
        }
        if ($narrowest_mask) {
            return $narrowest_net;
        }
        return false;
    }
    function check_orphan($conn) {
        $sql = "SELECT * FROM net_group_reference LEFT JOIN net
                ON net.name = net_group_reference.net_name WHERE descr IS NULL";
        if (!$rs = & $conn->Execute($sql)) {
            print $conn->ErrorMsg();
        } else {
            while (!$rs->EOF) {
                Net_sensor_reference::delete($conn, $rs->fields["net_name"], $rs->fields["sensor_name"]);
                $rs->MoveNext();
            }
        }
    }
    function get_related_sensors($conn, $net) {
        $sensors = array();
        /* read from net table */
        $sql = "SELECT sensor.ip AS sensor FROM sensor, net_sensor_reference
                WHERE net_sensor_reference.net_name = ? AND
                sensor.name = net_sensor_reference.sensor_name ORDER BY sensor.priority DESC,sensor.name ASC";
        $params = array(
            $net
        );
        if (!$rs = & $conn->Execute($sql, $params)) {
            print $conn->ErrorMsg();
        } else {
             while (!$rs->EOF) {
                 $sensors[] = $rs->fields["sensor"];
                 $rs->MoveNext();
             }
        }
        return $sensors;
    }
}
?>