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
* - Host()
* - get_ip()
* - get_hostname()
* - get_asset()
* - get_threshold_c()
* - get_threshold_a()
* - get_rrd_profile()
* - get_alert()
* - get_persistence()
* - get_nat()
* - get_descr()
* - get_foundrows()
* - get_os()
* - get_mac_address()
* - get_mac_vendor()
* - get_sensors()
* - get_list()
* - insert()
* - update()
* - delete()
* - get_ips_and_hostname()
* - ip2hostname()
* - ipthresh_c()
* - ipthresh_a()
* - ip_asset()
* - in_host()
* - get_related_sensors()
* Classes list:
* - Host
*/
require_once 'ossim_conf.inc';
require_once 'classes/Host_sensor_reference.inc';
require_once 'classes/Host_qualification.inc';
require_once 'classes/Host_mac.inc';
require_once 'classes/Host_os.inc';
require_once 'classes/Host_services.inc';
require_once 'classes/Net.inc';
require_once 'classes/Session.inc';
require_once 'classes/Log_action.inc';
require_once 'ossim_db.inc';
require_once 'classes/WebIndicator.inc';
class Host {
    var $ip;
    var $hostname;
    var $lat;
    var $lon;
    var $asset;
    var $threshold_c;
    var $threshold_a;
    var $rrd_profile;
    var $alert;
    var $persistence;
    var $nat;
    var $descr;

    function Host($ip, $hostname, $asset, $threshold_c, $threshold_a, $rrd_profile, $alert, $persistence, $nat, $descr, $foundrows = 0, $lat = "", $lon = "") {
        $this->ip = htmlentities($ip);
        $this->hostname = htmlentities($hostname);
        $this->asset = intval($asset);
        $this->threshold_c = intval($threshold_c);
        $this->threshold_a = intval($threshold_a);
        $this->rrd_profile = htmlentities($rrd_profile);
        $this->alert = intval($alert);
        $this->persistence = intval($persistence);
        $this->nat = htmlentities($nat);
        $this->descr = htmlentities($descr);
        $this->foundrows = $foundrows;
		$this->lat = $lat;
		$this->lon = $lon;
    }
    function get_ip() {
        return $this->ip;
    }
    function get_hostname() {
        return $this->hostname;
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
    function get_coordinates(){
		$coordinates = array();
		$coordinates['lat'] = $this->lat;
		$coordinates['lon'] = $this->lon;
		return $coordinates;
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
    function get_nat() {
        return $this->nat;
    }
    function get_descr() {
        return $this->descr;
    }
    function get_foundrows() {
        return $this->foundrows;
    }
    function get_os($conn) {
        if ($os = Host_os::get_ip_data($conn, $this->ip)) {
            return $os["os"];
        }
        return "Unknown";
    }
    function get_mac_address($conn) {
        if ($mac = Host_mac::get_ip_data($conn, $this->ip)) {
            return $mac["mac"];
        }
        return "";
    }
    function get_mac_vendor($conn) {
        if ($mac = Host_mac::get_ip_data($conn, $this->ip)) {
            return $mac["vendor"];
        }
        return "";
    }
    function get_sensors($conn) {
        return Host_sensor_reference::get_list($conn, $this->ip);
    }
    function get_list($conn, $where = "", $order = "") {
        mysql_escape_string($where);
        mysql_escape_string($order);
        $iffoundrows = preg_match("/LIMIT/", $order) ? true : false;
        $select = ($iffoundrows) ? "SQL_CALC_FOUND_ROWS *" : "*";
        $query = OssimQuery("SELECT $select FROM host $where $order");
		//echo $query;
	   $list = array();
        if (!$rs = & $conn->Execute($query)) {
            print $conn->ErrorMsg();
        } else {
            $foundrows = 0;
            if ($iffoundrows) {
                if (!$rf = & $conn->Execute("SELECT FOUND_ROWS() as total")) print $conn->ErrorMsg();
                else $foundrows = $rf->fields["total"];
            }
            while (!$rs->EOF) {
                if (Session::hostAllowed($conn, $rs->fields["ip"])) {
                    $list[] = new Host($rs->fields["ip"], $rs->fields["hostname"], $rs->fields["asset"], $rs->fields["threshold_c"], $rs->fields["threshold_a"], $rs->fields["rrd_profile"], $rs->fields["alert"], $rs->fields["persistence"], $rs->fields["nat"], $rs->fields["descr"], $foundrows,$rs->fields["lat"],$rs->fields["lon"]);
                }
                $rs->MoveNext();
            }
        }
        return $list;
    }
	function get_list_pag ($conn, $where = "", $order = "") {
		// User sensor filtering
		$sensor_where = "";
		$sensor_join = "";
		if (Session::allowedSensors() != "") {
			$user_sensors = explode(",",Session::allowedSensors());
			$sensor_str = "";
			foreach ($user_sensors as $user_sensor) if ($user_sensor != "")
				$sensor_str .= (($sensor_str != "") ? "," : "")."'".$user_sensor."'";
			if ($sensor_str == "") $sensor_str = "AND 0";
			$sensor_where = "ip in (select hs.host_ip FROM host_sensor_reference hs,sensor s WHERE hs.sensor_name=s.name AND s.ip in(" . $sensor_str . "))";
		}
		if ($sensor_where != "") {
			if (preg_match ("/where/i",$where)) {
				$where = preg_replace("/where/i","where ".$sensor_where." AND ",$where);
			}
			else {
				$where = "where ".$sensor_where;
			}
		}
		
		mysql_escape_string($where);
        mysql_escape_string($order);
        $iffoundrows = preg_match("/LIMIT/", $order) ? true : false;
        $select = ($iffoundrows) ? "SQL_CALC_FOUND_ROWS *" : "*";
        
		$query = OssimQuery("SELECT $select FROM host $where $order");
	    //echo "SELECT $select FROM host h $where $order<br>";
		$list = array();
        if (!$rs = & $conn->Execute($query)) {
            print $conn->ErrorMsg();
        } else {
            $foundrows = 0;
            if ($iffoundrows) {
                if (!$rf = & $conn->Execute("SELECT FOUND_ROWS() as total")) print $conn->ErrorMsg();
                else $foundrows = $rf->fields["total"];
            }
            while (!$rs->EOF) {
                $list[] = new Host($rs->fields["ip"], $rs->fields["hostname"], $rs->fields["asset"], $rs->fields["threshold_c"], $rs->fields["threshold_a"], $rs->fields["rrd_profile"], $rs->fields["alert"], $rs->fields["persistence"], $rs->fields["nat"], $rs->fields["descr"], $foundrows);
                $rs->MoveNext();
            }
        }
        return $list;
	}
    function insert($conn, $ip, $hostname, $asset, $threshold_c, $threshold_a, $rrd_profile, $alert, $persistence, $nat, $sensors, $descr, $os = "Unknown", $mac = "", $mac_vendor = "", $lat="", $lon="") {

        settype($asset, "int");  
        settype($threshold_c, "int");
        settype($threshold_a, "int");
        settype($alert, "int");
        settype($persistence, "int");
        $sql = "INSERT INTO host (ip, hostname, asset, threshold_c, threshold_a, rrd_profile, 
                alert, persistence, nat, descr, lat, lon) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = array(
            $ip,
            $hostname,
            $asset,
            $threshold_c,
            $threshold_a,
            $rrd_profile,
            $alert,
            $persistence,
            $nat,
            $descr, $lat, $lon
        );
        if ($conn->Execute($sql, $params) === false) {
            print 'error inserting host: ' . $conn->ErrorMsg() . '<BR>';
            exit;
        } else {
            Host_sensor_reference::insert($conn, $ip, $sensors);
        }
        require_once 'classes/Sensor.inc';
        $sensor_list = Sensor::get_list($conn);
        if (is_array($sensor_list)) {
            foreach($sensor_list as $aux) {
                $db_sensor_list[$aux->get_name() ] = $aux->get_ip();
            }
        }
        foreach($sensors as $sensor) {
            if ($os != "Unknown") {
                $sql = "INSERT INTO host_os (ip, os, date, anom, sensor)
                    VALUES (inet_aton(?), ?, CURRENT_TIMESTAMP, '0', inet_aton(?))";
                $params = array(
                    $ip,
                    $os,
                    $db_sensor_list[$sensor]
                );
                $conn->Execute($sql, $params);
            }
            if ($mac != "") {
                $sql = "INSERT INTO host_mac (ip, mac, date, vendor, anom, sensor) 
                    VALUES (inet_aton(?), ?, CURRENT_TIMESTAMP, ?, '0', inet_aton(?))";
                $params = array(
                    $ip,
                    $mac,
                    $mac_vendor,
                    $db_sensor_list[$sensor]
                );
                $conn->Execute($sql, $params);
            }
        }
        $infolog = array(
            $hostname,
            "[" . $ip . "]"
        );
        Log_action::log(24, $infolog);
        WebIndicator::set_on("Reload_hosts");
        WebIndicator::set_on("ReloadPolicy");
    }
    function update($conn, $ip, $hostname, $asset, $threshold_c, $threshold_a, $rrd_profile, $alert, $persistence, $nat, $sensors, $descr, $os = "Unknown", $mac = "", $mac_vendor = "", $lat="", $lon="") {
        settype($asset, "int");
        settype($threshold_c, "int");
        settype($threshold_a, "int");
        settype($alert, "int");
        settype($persistence, "int");
        $sql = "UPDATE host SET hostname = ?, ip = ?, asset = ?, threshold_c = ?,
                threshold_a = ?, rrd_profile = ?, alert = ?, persistence = ?, nat = ?,
                descr = ?, lat = ?, lon = ? WHERE ip = ?";
        //printr($lat); exit;
        $conn->Debug = 1;
        $params = array(
            $hostname,
            $ip,
            $asset,
            $threshold_c,
            $threshold_a,
            $rrd_profile,
            $alert,
            $persistence,
            $nat,
            $descr,
			$lat,
			$lon,
            $ip
        );
        if ($conn->Execute($sql, $params) === false) {
            print 'error updating: ' . $conn->ErrorMsg() . '<BR>';
            exit;
        }
        /* update sensor info */
        Host_sensor_reference::delete($conn, $ip);
        Host_sensor_reference::insert($conn, $ip, $sensors);
        require_once 'classes/Sensor.inc';
        $sensor_list = Sensor::get_list($conn);
        if (is_array($sensor_list)) {
            foreach($sensor_list as $aux) {
                $db_sensor_list[$aux->get_name() ] = $aux->get_ip();
            }
        }
        foreach($sensors as $sensor) {
            if ($os != "Unknown") {
                $sql = "INSERT INTO host_os (ip, os, date, anom, sensor) VALUES 
                        (inet_aton(?), ?, CURRENT_TIMESTAMP, '0', inet_aton(?))";
                $params = array(
                    $ip,
                    $os,
                    $db_sensor_list[$sensor]
                );
                $conn->Execute($sql, $params);
            }
            if ($mac != "") {
                $sql = "INSERT INTO host_mac (ip, mac, date, vendor, anom, sensor) VALUES
                        (inet_aton(?), ?, CURRENT_TIMESTAMP, ?, '0', inet_aton(?))";
                $params = array(
                    $ip,
                    $mac,
                    $mac_vendor,
                    $db_sensor_list[$sensor]
                );
                $conn->Execute($sql, $params);
            }
        }
        $infolog = array(
            $hostname,
            "[" . $ip . "]"
        );
        Log_action::log(26, $infolog);
        WebIndicator::set_on("Reload_hosts");
        WebIndicator::set_on("ReloadPolicy");
    }
    function delete($conn, $ip) {
		Host_sensor_reference::delete($conn, $ip);
        Host_qualification::delete($conn, $ip);
        Host_services::delete($conn, $ip);
        $params = array(
            $ip
        );
        // Business Processes
        $sql = "DELETE FROM bp_asset_member WHERE member=? AND member_type='host'";
        if (!$conn->Execute($sql, $params)) {
            die('error deleting: ' . $conn->ErrorMsg());
        }
        // Host ids
        $sql = "DELETE FROM host_ids WHERE ip=?";
        if (!$conn->Execute($sql, $params)) {
            die('error deleting: ' . $conn->ErrorMsg());
        }
        // Host group reference
        $sql = "DELETE FROM host_group_reference WHERE host_ip=?";
        if (!$conn->Execute($sql, $params)) {
            die('error deleting: ' . $conn->ErrorMsg());
        }
        // Host sensor reference
        $sql = "DELETE FROM host_sensor_reference WHERE host_ip=?";
        if (!$conn->Execute($sql, $params)) {
            die('error deleting: ' . $conn->ErrorMsg());
        }
        // Host scan
        $sql = "DELETE FROM host_scan WHERE inet_ntoa(host_ip) = ?";
        if (!$conn->Execute($sql, $params)) {
            die('error deleting: ' . $conn->ErrorMsg());
        }
        // Finally hosts table
        $sql = "DELETE FROM host WHERE ip = ?";
        if (!$conn->Execute($sql, $params)) {
            die('error deleting: ' . $conn->ErrorMsg());
        }
        $infolog = array(
            $ip
        );
        Log_action::log(25, $infolog);
        WebIndicator::set_on("Reload_hosts");
        WebIndicator::set_on("ReloadPolicy");
    }
	function can_delete($conn, $ip) {
		$sql = "SELECT count(*) as num FROM policy_host_reference WHERE host_ip = ?";
		$params = array(
            $ip
        );
        if (!$rs = & $conn->Execute($sql, $params)) {
            print 'error deleting: ' . $conn->ErrorMsg() . '<BR>';
            exit;
        }
		if ($rs->fields['num'] == '0') return 1;
		else return 0;
	}
    // get lists of sensors/hosts 
    function get_ips_and_hostname($conn,$checkperm=false) {
        $allowed_sensors = array();
        $asensors_str = Session::allowedSensors();
		if ($checkperm && $asensors_str != "") $allowed_sensors = explode(",",$asensors_str);
		$sensors = array();
        $sql = "SELECT ip,name FROM sensor";
        if (!$rs = $conn->Execute($sql)) {
            print 'error: ' . $conn->ErrorMsg() . '<BR>';
            exit;
        }
        // get hash with ip-name
        while (!$rs->EOF) {
            if (!$checkperm || ($checkperm && (empty($allowed_sensors) || in_array($rs->fields["ip"],$allowed_sensors))))
                $sensors[$rs->fields["ip"]] = $rs->fields["name"];
            $rs->MoveNext();
        }
        $hosts = array();
        $sql = "SELECT ip,hostname FROM host";
        if (!$rs = $conn->Execute($sql)) {
            print 'error: ' . $conn->ErrorMsg() . '<BR>';
            exit;
        }
        // get hash with ip-name
        while (!$rs->EOF) {
            if (!$checkperm || ($checkperm && Session::hostAllowed($conn, $rs->fields["ip"])))
                $hosts[$rs->fields["ip"]] = $rs->fields["hostname"];
            $rs->MoveNext();
        }
        return array(
            $sensors,
            $hosts
        );
    }
    function ip2hostname($conn, $ip, $is_sensor = false, $force_no_dns = false) {
        if ($is_sensor == true) {
            $sql = "SELECT * FROM sensor WHERE ip = ?";
            $params = array(
                $ip
            );
            if (!$rs = $conn->Execute($sql, $params)) {
                print 'error: ' . $conn->ErrorMsg() . '<BR>';
                exit;
            }
            if (!$rs->EOF) {
                return $rs->fields["name"];
            }
        }
        $sql = "SELECT * FROM host WHERE ip = ?";
        $params = array(
            $ip
        );
        if (!$rs = $conn->Execute($sql, $params)) {
            print 'error: ' . $conn->ErrorMsg() . '<BR>';
            exit;
        }
        if (!$rs->EOF) {
            return $rs->fields["hostname"];
        } else {
            $config = $GLOBALS["CONF"];
            $use_resolv = $config->get_conf("use_resolv");
            if ($use_resolv and preg_match("/\d+\.\d+\.\d+\.\d+/", $ip) and $force_no_dns == false) return gethostbyaddr($ip);
            else return $ip;
        }
    }
	function hostname2ip($conn, $hostname, $resolv=false) {
        $sql = "SELECT * FROM host WHERE hostname = ?";
        $params = array(
            $hostname
        );
        if (!$rs = $conn->Execute($sql, $params)) {
            print 'error: ' . $conn->ErrorMsg() . '<BR>';
            exit;
        }
        if (!$rs->EOF) {
            return $rs->fields["ip"];
        } else {
             $sql = "SELECT * FROM server WHERE name = ?";
			$params = array(
				$hostname
			);
			if (!$rs = $conn->Execute($sql, $params)) {
				print 'error: ' . $conn->ErrorMsg() . '<BR>';
				exit;
			}
			if (!$rs->EOF) {
				return $rs->fields["ip"];
			}
            elseif ($resolv) {
                return trim(`/usr/bin/dig $hostname A +short | /usr/bin/tail -1`); 
            }
			else return "";
        }
    }
    function ipthresh_c($conn, $ip) {
        $sql = "SELECT * FROM host WHERE ip = ?";
        $params = array(
            $ip
        );
        if (!$rs = $conn->Execute($sql, $params)) {
            print 'error: ' . $conn->ErrorMsg() . '<BR>';
            exit;
        }
        if (!$rs->EOF) return $rs->fields["threshold_c"];
        else {
            $net = Net::GetClosestNet($conn, $ip);
            return Net::netthresh_c($conn, $net);
        }
    }
    function ipthresh_a($conn, $ip) {
        $sql = "SELECT * FROM host WHERE ip = ?";
        $params = array(
            $ip
        );
        if (!$rs = $conn->Execute($sql, $params)) {
            print 'error: ' . $conn->ErrorMsg() . '<BR>';
            exit;
        }
        if (!$rs->EOF) return $rs->fields["threshold_a"];
        else {
            $net = Net::GetClosestNet($conn, $ip);
            return Net::netthresh_a($conn, $net);
        }
    }
    function ip_asset($conn, $ip) {
        $sql = "SELECT * FROM host WHERE ip = ?";
        $params = array(
            $ip
        );
        if (!$rs = $conn->Execute($sql, $params)) {
            print 'error: ' . $conn->ErrorMsg() . '<BR>';
            exit;
        }
        if (!$rs->EOF) return $rs->fields["asset"];
        else {
            $net = Net::GetClosestNet($conn, $ip);
            $ass = Net::net_asset($conn, $net);
            return Net::net_asset($conn, $net);
        }
    }
    function in_host($conn, $ip) {
        $sql = "SELECT * FROM host WHERE ip = ?";
        $params = array(
            $ip
        );
        if (!$rs = & $conn->Execute($sql, $params)) {
            print $conn->ErrorMsg();
        }
        return $rs->fields[0];
    }

    function get_related_sensors($conn, $ip) {
        $sensors = array();
        /* read from HOST table */
        $sql = "SELECT sensor.ip AS sensor FROM sensor, host_sensor_reference
                WHERE host_sensor_reference.host_ip = ? AND
                sensor.name = host_sensor_reference.sensor_name ORDER BY sensor.priority DESC,sensor.name ASC";
        $params = array(
            $ip
        );
        if (!$rs = & $conn->Execute($sql, $params)) {
            print $conn->ErrorMsg();
        } elseif (!$rs->EOF) {
            $sensors[] = $rs->fields["sensor"];
        }
        /* read from NET table */
        if ($net_list = Net::get_list($conn)) {
            foreach($net_list as $net) {
                $net_name = $net->get_name();
                $ips = $net->get_ips();
                if (Net::isIpInNet($ip, $ips)) {
                    $sql = "SELECT sensor.ip AS sensor 
                        FROM sensor, net_sensor_reference
                        WHERE name = sensor_name AND net_name = ?";
                    $params = array(
                        $net_name
                    );
                    if (!$rs = & $conn->Execute($sql, $params)) {
                        print $conn->ErrorMsg();
                    } elseif (!$rs->EOF) {
                        $sensors[] = $rs->fields["sensor"];
                    }
                }
            }
        }
        return $sensors;
    }
    function ip2ulong($strIP) {
    	return sprintf("%u",ip2long($strIP)); 
    }
    // 
    function get_apps($conn,$ip) {
    	if (!is_numeric($ip)) $ip = Host::ip2ulong($ip);
    	$apps = array();
        $sql = "SELECT app FROM host_apps WHERE ip=?";
        if (!$rs = & $conn->Execute($sql, array($ip))) {
            print $conn->ErrorMsg();
        } else {
        	while (!$rs->EOF) {
            	$apps[] = $rs->fields["app"];
            	$rs->MoveNext();
           	}
        }
        return $apps;
    }
}
?>