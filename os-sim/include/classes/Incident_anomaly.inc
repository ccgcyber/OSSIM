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
* - Incident_anomaly()
* - get_id()
* - get_incident_id()
* - get_anom_type()
* - get_ip()
* - get_data_orig()
* - get_data_new()
* - get_list()
* Classes list:
* - Incident_anomaly
*/
require_once ("ossim_db.inc");
class Incident_anomaly {
    var $id;
    var $incident_id;
    var $anom_type;
    var $ip;
    var $data_orig;
    var $data_new;
    function Incident_anomaly($id, $incident_id, $anom_type, $ip, $data_orig, $data_new) {
        $this->id = intval($id);
        $this->incident_id = intval($incident_id);
        $this->anom_type = $anom_type;
        $this->ip = $ip;
        $this->data_orig = $data_orig;
        $this->data_new = $data_new;
    }
    function get_id() {
        return $this->id;
    }
    function get_incident_id() {
        return $this->incident_id;
    }
    function get_anom_type() {
        return $this->anom_type;
    }
    function get_ip() {
        return $this->ip;
    }
    function get_data_orig() {
        return $this->data_orig;
    }
    function get_data_new() {
        return $this->data_new;
    }
    function get_list($conn, $args) {
        $list = array();
        $query = OssimQuery("SELECT * FROM incident_anomaly $args");
        if (!$rs = & $conn->Execute($query)) {
            print $conn->ErrorMsg();
        } else {
            while (!$rs->EOF) {
                $list[] = new Incident_anomaly($rs->fields["id"], $rs->fields["incident_id"], $rs->fields["anom_type"], $rs->fields["ip"], $rs->fields["data_orig"], $rs->fields["data_new"]);
                $rs->MoveNext();
            }
        }
        return $list;
    }
}
?>