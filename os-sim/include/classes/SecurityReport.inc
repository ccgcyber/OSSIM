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
* - SecurityReport()
* - AttackHost()
* - Events()
* - Ports()
* - EventsByRisk()
* - Truncate()
* Classes list:
* - SecurityReport
*/
require_once ('ossim_db.inc');
require_once ('classes/Log_action.inc');
class SecurityReport {
    var $ossim_conn;
    var $snort_conn;
    function SecurityReport() {
        $db = new ossim_db();
        $this->ossim_conn = $db->connect();
        $this->snort_conn = $db->snort_connect();
    }
    function AttackHost($conn, $ip_target, $limit = 10, $type = "event", $from_date, $to_date, $assets=array(), $sourcetype='', $category=0, $subcategory=0) {
        require_once ('classes/Host.inc');
        require_once ('classes/Net.inc');
        require_once ('classes/Session.inc');
		require_once ('classes/Event_viewer.inc');
        $list = array();
        $temp_conn = $this->snort_conn;
        
		$from_date = preg_replace ("/(\d\d)\/(\d\d)\/(\d\d\d\d)/","\\3-\\2-\\1",$from_date);
		$to_date = preg_replace ("/(\d\d)\/(\d\d)\/(\d\d\d\d)/","\\3-\\2-\\1",$to_date);
		
		// User sensor filtering
		$sensor_str_alarm = "";
		$sensor_str_event = "";
		if (Session::allowedSensors() != "") {
			$user_sensors = explode(",",Session::allowedSensors());
			foreach ($user_sensors as $user_sensor) if ($user_sensor!="")
				$sensor_str .= ($sensor_str == "") ? "e.sensor LIKE '".$user_sensor."%'" : " OR e.sensor LIKE '".$user_sensor."%'";
			//echo "<br><br><br><br>SENSOR:$sensor_str de ".Session::allowedSensors();
			$sensor_str_alarm = " AND ($sensor_str) AND e.id=a.event_id";
			// snort
			$snortsensors = Event_viewer::GetSensorSids($temp_conn);
			$sensor_str = "";
			foreach ($user_sensors as $user_sensor)
				if (count($snortsensors[$user_sensor]) > 0) $sensor_str .= ($sensor_str != "") ? ",".implode(",",$snortsensors[$user_sensor]) : implode(",",$snortsensors[$user_sensor]);
			if ($sensor_str == "") $sensor_str = "0";
			$sensor_str_event = " AND acid_event.sid in (" . $sensor_str . ")";
		}
		
		if ($sensor_str != "") {
			$sensor_join = " ,event e";
			$sql_where .= $sensor_str;
		} else $sensor_join = "";
		
		if (!strcmp($ip_target, "ip_src")) {
            if ($type == "alarm") $target = "src_ip";
            $title = "Attacker hosts";
        } elseif (!strcmp($ip_target, "ip_dst")) {
            if ($type == "alarm") $target = "dst_ip";
            $title = "Attacked hosts";
        }
        switch ($type) {
            case "alarm":
                if (!strcmp($ip_target, "ip_src")) {
                    $ip_target = "src_ip";
                }
                if (!strcmp($ip_target, "ip_dst")) {
                    $ip_target = "dst_ip";
                }
                //
                list($join2,$where2) = SecurityReport::make_join_where("a.",$ip_target,$assets,$sourcetype,$category,$subcategory);
                //
                $query = OssimQuery("SELECT count(a.$ip_target) AS occurrences,
                  a.$ip_target as ip FROM alarm a $join2 $sensor_join where a.$ip_target != 0 AND a.timestamp >= '$from_date 00:00:00' AND a.timestamp <= '$to_date 23:59:59'$sensor_str_alarm $where2
                  GROUP BY a.$ip_target ORDER BY occurrences DESC LIMIT $limit");
                $temp_conn = $this->ossim_conn;
                break;

            case "event":
            default:
                //
                list($join2,$where2) = SecurityReport::make_join_where("acid_event.",$ip_target,$assets,$sourcetype,$category,$subcategory);
                //            
                $query = OssimQuery("SELECT count($ip_target) AS occurrences,
                  $ip_target as ip FROM acid_event $join2 where ossim_risk_c >= 0 AND timestamp >= '$from_date 00:00:00' AND timestamp <= '$to_date 23:59:59' AND ossim_risk_a >= 0$sensor_str_event $where2
                  GROUP BY ip ORDER BY occurrences DESC LIMIT $limit");
                $db = new ossim_db();
				$temp_conn = $db->snort_connect();
				//$temp_conn = $this->snort_conn;
        }
		//echo "SQL:$query";
		//exit;
		if (!$rs = & $temp_conn->Execute($query)) {
			print $temp_conn->ErrorMsg();
        } else {
			$i = 0;
            while (!$rs->EOF) {
				// sensor filter in query
				//if (Session::hostAllowed($conn, $rs->fields["ip"])) {
				if ($rs->fields['ip'] == 0) { $rs->MoveNext(); continue; }
				$ip_address = (is_numeric($rs->fields["ip"]))? long2ip($rs->fields["ip"]) : $rs->fields["ip"];
				$tmp_array = array(
					$ip_address,
					$rs->fields["occurrences"]
				);
				array_push($list, $tmp_array);
				$i+= 1;
                //}
                $rs->MoveNext();
                /* check limit */
                if ($i >= $limit) {
                    $rs->EOF = true;
                }
            }
        }
		if ($type == "event") $db->close($temp_conn);
        return $list;
    }
    function Events($limit = 10, $type = "event", $from_date, $to_date, $assets=array(), $sourcetype='', $category=0, $subcategory=0) {
        require_once ('classes/Event_viewer.inc');
		
		$list = array();
        $temp_conn = $this->snort_conn;
		
		$from_date = preg_replace ("/(\d\d)\/(\d\d)\/(\d\d\d\d)/","\\3-\\2-\\1",$from_date);
		$to_date = preg_replace ("/(\d\d)\/(\d\d)\/(\d\d\d\d)/","\\3-\\2-\\1",$to_date);
		
		// User sensor filtering
		$sensor_str_alarm = "";
		$sensor_str_event = "";
		if (Session::allowedSensors() != "") {
			$user_sensors = explode(",",Session::allowedSensors());
			foreach ($user_sensors as $user_sensor) if ($user_sensor!="")
				$sensor_str .= ($sensor_str == "") ? "e.sensor LIKE '".$user_sensor."%'" : " OR e.sensor LIKE '".$user_sensor."%'";
			//echo "<br><br><br><br>SENSOR:$sensor_str de ".Session::allowedSensors();
			$sensor_str_alarm = " AND ($sensor_str) AND e.id=a.event_id";
			// snort
			$snortsensors = Event_viewer::GetSensorSids($temp_conn);
			$sensor_str = "";
			foreach ($user_sensors as $user_sensor)
				if (count($snortsensors[$user_sensor]) > 0) $sensor_str .= ($sensor_str != "") ? ",".implode(",",$snortsensors[$user_sensor]) : implode(",",$snortsensors[$user_sensor]);
			if ($sensor_str == "") $sensor_str = "0";
			$sensor_str_event = " AND acid_event.sid in (" . $sensor_str . ")";
		}
		
		if ($sensor_str != "") {
			$sensor_join = " ,event e";
			$sql_where .= $sensor_str;
		} else $sensor_join = "";
		
        switch ($type) {
            case "alarm":
                //
                list($join2,$where2) = SecurityReport::make_join_where("a.","src_ip;dst_ip",$assets,$sourcetype,$category,$subcategory);
                //
                $query = "SELECT count(name) AS occurrences, name as sig_name
                            FROM alarm a $join2, plugin_sid$sensor_join
                            WHERE plugin_sid.plugin_id = a.plugin_id AND a.timestamp >= '$from_date 00:00:00' AND a.timestamp <= '$to_date 23:59:59'
                                  AND plugin_sid.sid = a.plugin_sid$sensor_str_alarm $where2
                            GROUP BY name
                            ORDER BY occurrences 
                            DESC LIMIT $limit";
                $temp_conn = $this->ossim_conn;
                break;

            case "event":
            default:
                //
                list($join2,$where2) = SecurityReport::make_join_where("acid_event.","ip_dst;ip_src",$assets,$sourcetype,$category,$subcategory,1);
                // 
                $query = "SELECT count(plugin_sid) AS occurrences, plugin_sid.name as sig_name
                            FROM acid_event LEFT JOIN ossim.plugin_sid ON plugin_sid.plugin_id=acid_event.plugin_id AND plugin_sid.sid=acid_event.plugin_sid $join2
                            WHERE timestamp >= '$from_date 00:00:00' AND timestamp <= '$to_date 23:59:59'$sensor_str_event
                            $where2
                            GROUP BY sig_name
                            ORDER BY occurrences
                            DESC LIMIT $limit";
                $db = new ossim_db();
				$temp_conn = $db->snort_connect();
				//$temp_conn = $this->snort_conn;
        }
		//echo $query;
		//exit;
        if (!$rs = & $temp_conn->Execute($query)) {
            print $temp_conn->ErrorMsg();
        } else {
            while (!$rs->EOF) {
                $tmp_array = array(
                    $rs->fields["sig_name"],
                    $rs->fields["occurrences"]
                );
                array_push($list, $tmp_array);
                $rs->MoveNext();
            }
        }
		if ($type == "event") $db->close($temp_conn);
        return $list;
    }
    function Ports($limit = 10, $type = "event", $from_date, $to_date, $assets=array(), $sourcetype='', $category=0, $subcategory=0) {
        require_once ('classes/Port.inc');
        require_once ('classes/Event_viewer.inc');
		$list = array();
        $temp_conn = $this->snort_conn;
		
		$from_date = preg_replace ("/(\d\d)\/(\d\d)\/(\d\d\d\d)/","\\3-\\2-\\1",$from_date);
		$to_date = preg_replace ("/(\d\d)\/(\d\d)\/(\d\d\d\d)/","\\3-\\2-\\1",$to_date);
		
		// User sensor filtering
		$sensor_str_alarm = "";
		$sensor_str_event = "";
		if (Session::allowedSensors() != "") {
			$user_sensors = explode(",",Session::allowedSensors());
			foreach ($user_sensors as $user_sensor) if ($user_sensor!="")
				$sensor_str .= ($sensor_str == "") ? "e.sensor LIKE '".$user_sensor."%'" : " OR e.sensor LIKE '".$user_sensor."%'";
			//echo "<br><br><br><br>SENSOR:$sensor_str de ".Session::allowedSensors();
			$sensor_str_alarm = " AND ($sensor_str) AND e.id=a.event_id";
			// snort
			$snortsensors = Event_viewer::GetSensorSids($temp_conn);
			$sensor_str = "";
			foreach ($user_sensors as $user_sensor)
				if (count($snortsensors[$user_sensor]) > 0) $sensor_str .= ($sensor_str != "") ? ",".implode(",",$snortsensors[$user_sensor]) : implode(",",$snortsensors[$user_sensor]);
			if ($sensor_str == "") $sensor_str = "0";
			$sensor_str_event = " AND acid_event.sid in (" . $sensor_str . ")";
		}
		
		if ($sensor_str != "") {
			$sensor_join = " ,event e";
			$sql_where .= $sensor_str;
		} else $sensor_join = "";
		
        switch ($type) {
            case "alarm":
                //
                list($join2,$where2) = SecurityReport::make_join_where("a.","dst_ip",$assets,$sourcetype,$category,$subcategory);
                //
                $query = OssimQuery("SELECT count(a.dst_port) AS occurrences, a.dst_port as layer4_dport
                  FROM alarm a $join2 $sensor_join WHERE a.timestamp >= '$from_date 00:00:00' AND a.timestamp <= '$to_date 23:59:59' AND a.dst_port<>0$sensor_str_alarm $where2 GROUP BY a.dst_port ORDER BY occurrences DESC LIMIT $limit");
                $temp_conn = $this->ossim_conn;
                break;

            case "event":
            default:
                //
                list($join2,$where2) = SecurityReport::make_join_where("acid_event.","ip_dst",$assets,$sourcetype,$category,$subcategory);
                // 
                $query = OssimQuery("SELECT count(layer4_dport) AS occurrences, layer4_dport
                  FROM acid_event $join2 WHERE timestamp >= '$from_date 00:00:00' AND timestamp <= '$to_date 23:59:59' AND layer4_dport<>0$sensor_str_event $where2
                  GROUP BY layer4_dport
                  ORDER BY occurrences DESC LIMIT $limit");
                $db = new ossim_db();
				$temp_conn = $db->snort_connect();
				//$temp_conn = $this->snort_conn;
        }
		//echo $query;
		//exit;
        if (!$rs = & $temp_conn->Execute($query)) {
            print $temp_conn->ErrorMsg();
        } else {
            while (!$rs->EOF) {
                $port = $rs->fields["layer4_dport"];
                $service = "unknown";
                $occurrences = $rs->fields["occurrences"];
                if ($port != "NULL" and $occurrences != 0) {
                    if ($port_list = Port::get_list($this->ossim_conn, "WHERE port_number = $port")) {
                        $service = $port_list[0]->get_service();
                    }
                    $tmp_array = array(
                        $port,
                        $service,
                        $occurrences
                    );
                    array_push($list, $tmp_array);
                }
                $rs->MoveNext();
            }
        }
		if ($type == "event") $db->close($temp_conn);
        return $list;
    }
    function EventsByRisk($limit = 10, $type = "event", $from_date, $to_date, $assets=array(), $sourcetype='', $category=0, $subcategory=0) {
        require_once ('classes/Event_viewer.inc');
		
		$list = array();
        $temp_conn = $this->snort_conn;
        
		$from_date = preg_replace ("/(\d\d)\/(\d\d)\/(\d\d\d\d)/","\\3-\\2-\\1",$from_date);
		$to_date = preg_replace ("/(\d\d)\/(\d\d)\/(\d\d\d\d)/","\\3-\\2-\\1",$to_date);
		
		// User sensor filtering
		$sensor_str_alarm = "";
		$sensor_str_event = "";
		if (Session::allowedSensors() != "") {
			// ossim
			$user_sensors = explode(",",Session::allowedSensors());
			foreach ($user_sensors as $user_sensor) if ($user_sensor!="")
				$sensor_str .= ($sensor_str == "") ? "e.sensor LIKE '".$user_sensor."%'" : " OR e.sensor LIKE '".$user_sensor."%'";
			$sensor_str_alarm = " AND ($sensor_str) AND e.id=a.event_id";
			// snort
			$snortsensors = Event_viewer::GetSensorSids($temp_conn);
			$sensor_str = "";
			foreach ($user_sensors as $user_sensor)
				if (count($snortsensors[$user_sensor]) > 0) $sensor_str .= ($sensor_str != "") ? ",".implode(",",$snortsensors[$user_sensor]) : implode(",",$snortsensors[$user_sensor]);
			if ($sensor_str == "") $sensor_str = "0";
			$sensor_str_event = " AND acid_event.sid in (" . $sensor_str . ")";
		}
		
		if ($sensor_str != "") {
			$sensor_join = " ,event e";
			$sql_where .= $sensor_str;
		} else $sensor_join = "";
		
		switch ($type) {
            case "alarm":
                //
                list($join2,$where2) = SecurityReport::make_join_where("a.","src_ip;dst_ip",$assets,$sourcetype,$category,$subcategory);
                //
                $query = OssimQuery("SELECT name as sig_name, a.risk as ossim_risk_a FROM alarm a $join2, plugin_sid$sensor_join
                  WHERE  a.timestamp >= '$from_date 00:00:00' AND a.timestamp <= '$to_date 23:59:59' AND plugin_sid.plugin_id = a.plugin_id AND plugin_sid.sid = a.plugin_sid$sensor_str_alarm $where2
                  GROUP BY name ORDER BY risk DESC LIMIT $limit");
                $temp_conn = $this->ossim_conn;
                break;

            case "event":
            default:
                //
                list($join2,$where2) = SecurityReport::make_join_where("acid_event.","ip_dst;ip_src",$assets,$sourcetype,$category,$subcategory,1);
                // 
                $query = OssimQuery("SELECT plugin_sid.name as sig_name, ossim_risk_a
                  FROM acid_event $join2 LEFT JOIN ossim.plugin_sid ON plugin_sid.plugin_id=acid_event.plugin_id AND plugin_sid.sid=acid_event.plugin_sid WHERE timestamp >= '$from_date 00:00:00' AND timestamp <= '$to_date 23:59:59' $sensor_str_event
                  $where2 GROUP BY sig_name
                  ORDER BY ossim_risk_a DESC LIMIT $limit");
                $db = new ossim_db();
				$temp_conn = $db->snort_connect();
				//$temp_conn = $this->snort_conn;
        }
		//echo $query;
		//exit;
        if (!$rs = & $temp_conn->Execute($query)) {
            print $temp_conn->ErrorMsg();
        } else {
            while (!$rs->EOF) {
                $tmp_array = array(
                    $rs->fields["sig_name"],
                    $rs->fields["ossim_risk_a"]
                );
                array_push($list, $tmp_array);
                $rs->MoveNext();
            }
        }
		if ($type == "event") $db->close($temp_conn);
        return $list;
    }
    function Truncate($str, $length = 10, $trailing = '...') {
        // take off chars for the trailing
        $length-= strlen($trailing);
        if (strlen($str) > $length) {
            // string exceeded length, truncate and add trailing dots
            return substr($str, 0, $length) . $trailing;
        } else {
            // string was already short enough, return the string
            $res = $str;
        }
        return $res;
    }
    // 
    // special sql join and where
    //
    function make_join_where($prefix,$field,$assets,$sourcetype,$category,$subcategory,$leftjoin=0) {
    	$join = "";
    	$where = "";
    	// assets
    	if (is_array($assets)) {
    		foreach ($assets as $range => $asset_name) {
    			$ips = explode(",",$range);
    			$fields = explode(";",$field);
    			foreach ($fields as $field) {
    				$pfield = ($prefix!="") ? $prefix.$field : $field;
					if (count($ips)>1)
						$where .= " OR ($pfield>=".$ips[0]." AND $pfield<=".$ips[1].")";
					elseif (preg_match("/\d+\.\d+\.\d+\.\d+/",$ips[0])) // not numeric
						$where .= " OR inet_ntoa($pfield)='".$ips[0]."'";
					else
						$where .= " OR $pfield=".$ips[0];
				}
    		}
    		if ($where!="") $where = " AND (".preg_replace("/^ OR /","",$where).")";
    	}
    	// sourcetype
    	if ($sourcetype!="") {
    		$join .= " LEFT JOIN ossim.plugin ON plugin.id=".$prefix."plugin_id";
    		$where .= " AND plugin.source_type='".str_replace("'","\'",$sourcetype)."'";
    	}
    	// category & subcategory
    	if ($category!=0) {
    		if (!$leftjoin) $join .= " LEFT JOIN ossim.plugin_sid ON plugin_sid.plugin_id=".$prefix."plugin_id AND plugin_sid.sid=".$prefix."plugin_sid";
    		$where .= " AND plugin_sid.category_id=$category";
    		if ($subcategory!=0) $where .= " AND plugin_sid.subcategory_id=$subcategory";
    	}
    	return array($join,$where);
    }
}
?>