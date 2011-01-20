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
* - cmpf()
* Classes list:
*/
require_once 'classes/Security.inc';
require_once ('classes/Host.inc');
require_once ('classes/Host_os.inc');
require_once ('classes/Host_services.inc');
require_once ('classes/Host_mac.inc');
require_once ('classes/Util.inc');
require_once ('ossim_db.inc');

Session::logcheck("MenuPolicy", "PolicyHosts");

$key = GET('key');
ossim_valid($key, OSS_NULLABLE, OSS_TEXT, OSS_PUNC, 'illegal:' . _("key"));

$buffer = "";

$db = new ossim_db();
$conn = $db->connect();

if($key=="") {
    $props = Host::get_properties_types($conn);
    $buffer .= "[ {title: '"._("Asset by Property")."', isFolder: true, key:'main', icon:'../../pixmaps/theme/any.png', expand:true, children:[\n";
    foreach ($props as $prop) {
        switch (strtolower($prop["name"])) {
            case "software": $png = "software";
            break;
            case "operating-system": $png = "host_os";
            break;
            case "cpu": $png = "cpu";
            break;
            case "services": $png = "ports";
            break;
            case "ram": $png = "ram";
            break;
            case "department": $png = "host_group";
            break;
            case "macaddress": $png = "mac";
            break;
            case "workgroup": $png = "net_group";
            break;
            case "role": $png = "server_role";
            break;
        }
        $buffer .= "{ key:'p".$prop["id"]."', isFolder:true, isLazy:true, expand:false, icon:'../../pixmaps/theme/$png.png', title:'"._($prop["name"])."' },\n";
        }
        $buffer .= "{ key:'all', isFolder:true, isLazy:true, icon:'../../pixmaps/theme/host_add.png', title:'"._("All Hosts")."' }\n";
        $buffer .= "] } ]";
}
else if(preg_match("/p_(.*)/",$key,$found)) {
    

}

if ($buffer=="" || $buffer=="[  ]")
    $buffer = "[{title:'"._("No Properties found")."'}]";
    
echo $buffer;
$db->close($conn);
?>