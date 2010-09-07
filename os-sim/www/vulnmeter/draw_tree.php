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
* Classes list:
*/
require_once 'classes/Security.inc';
require_once ('classes/Session.inc');
Session::logcheck("MenuEvents", "EventsVulnerabilities");

$key = GET('key');
$page = intval(GET('page'));
ossim_valid($key, OSS_NULLABLE, OSS_TEXT, OSS_PUNC, 'illegal:' . _("key"));
ossim_valid($page, OSS_NULLABLE, OSS_DIGIT, 'illegal:' . _("page"));
if (ossim_error()) {
    die(ossim_error());
}
if ($page == "" || $page<=0) $page = 1;
$maxresults = 200;
$to = $page * $maxresults;
$from = $to - $maxresults;
$nextpage = $page + 1;

$cachefile = "/var/ossim/sessions/".$_SESSION["_user"]."_vulnmeter_".base64_encode($key)."_$page.json";
if (file_exists($cachefile)) {
    readfile($cachefile);
    exit;
}
require_once ('classes/Host.inc');
require_once ('classes/Host_group.inc');
require_once ('classes/Net.inc');
require_once ('classes/Net_group.inc');
require_once ('ossim_db.inc');
$db = new ossim_db();
$conn = $db->connect();

$ossim_hosts = $all_hosts = array();
$total_hosts = 0;
$ossim_nets = array();
$all_cclass_hosts = array();
$buffer = "";
if ($host_list = Host::get_list($conn, "", "ORDER BY hostname")) foreach($host_list as $host) if ($filter == "" || ($filter != "" && (preg_match("/$filter/i", $host->get_ip()) || preg_match("/$filter/i", $host->get_hostname())))) {
    $ossim_hosts[$host->get_ip() ] = $host->get_hostname();
    $all_hosts[$host->get_ip() ] = 1;
    $cclass = preg_replace("/(\d+\.)(\d+\.)(\d+)\.\d+/", "\\1\\2\\3", $host->get_ip());
    $all_cclass_hosts[$cclass][] = $host->get_ip();
    $total_hosts++;
}
if ($hg_list = Host_group::get_list($conn, "ORDER BY name")) {
    foreach($hg_list as $hg) {
        $hg_hosts = $hg->get_hosts($conn, $hg->get_name());
        foreach($hg_hosts as $hosts) {
            $ip = $hosts->get_host_ip();
            unset($all_hosts[$ip]);
        }
    }
}
$net_list = Net::get_list($conn, "ORDER BY name");

if ($key == "hostgroup") {
    if (count($hg_list)>0) {
        $buffer .= "[";
        $j = 0;
        foreach($hg_list as $hg) {
            if($j>=$from && $j<$to) {
                $hg_name = $hg->get_name();
                $li = "key:'hostgroup_$hg_name', isLazy:true , url:'NODES:$hg_name', icon:'../../pixmaps/theme/host_group.png', title:'$hg_name'\n";
                $buffer .= (($j>$from) ? "," : "") . "{ $li }\n";
            }
            $j++;
        }
        if ($j>$to) {
            $li = "key:'hostgroup', page:'$nextpage', isFolder:true, isLazy:true, icon:'../../pixmaps/theme/host_group.png', title:'"._("next")." $maxresults "._("host group")."'";
            $buffer .= ",{ $li }\n";
        }
        $buffer .= "]";
    }
}
else if (preg_match("/hostgroup_(.*)/",$key,$found)) {
    if ($hg_hosts = $hg->get_hosts($conn, $found[1])) {
        $k = 1;
        $j = 0;
        $html = "";
        $buffer .= "[";
        foreach($hg_hosts as $hosts) {
            if($j>=$from && $j<$to) {
                $host_ip = $hosts->get_host_ip();
                if (isset($ossim_hosts[$host_ip])) { // test filter
                    $html.= "{ key:'$key.$k', url:'$host_ip', icon:'../../pixmaps/theme/host.png', title:'$host_ip <font style=\"font-size:80%\">(" . $ossim_hosts[$host_ip] . ")</font>' },\n";
                    $k++;
                }
            }
            $j++;
        }
        if ($j>$to) {
            $html.= "{ key:'$key', page:'$nextpage', isFolder:true, isLazy:true, icon:'../../pixmaps/theme/host_group.png', title:'"._("next")." $maxresults "._("hosts")."' }";
        }
        if ($html != "") $buffer .= preg_replace("/,$/", "", $html);
        $buffer .= "]";
    }
}
else if ($key == "net") {
    if (count($net_list)>0) {
        $buffer .= "[";
        $j = 0;
        foreach($net_list as $net) {
            if($j>=$from && $j<$to) {
                $net_name = $net->get_name();
                $ips = $net->get_ips();
                $li = "key:'net_$net_name', isLazy:true, url:'".$net->get_ips()."', icon:'../../pixmaps/theme/net.png', title:'$net_name <font style=\"font-size:80%\">(".$ips.")</font>'\n";
                $buffer .= (($j>$from) ? "," : "") . "{ $li }\n";
            }
            $j++;
        }
        if ($j>$to) {
            $li = "key:'net', page:'$nextpage', isFolder:true, isLazy:true, icon:'../../pixmaps/theme/net.png', title:'"._("next")." $maxresults "._("nets")."'";
            $buffer .= ",{ $li }\n";
        }
        $buffer .= "]";
    }
}
else if (preg_match("/net_(.*)/",$key,$found)){
	$hostin = array();
	if ($net_list1 = Net::get_list($conn, "WHERE name='".$found[1]."'")) {
		require_once("classes/CIDR.inc");
		foreach($net_list1 as $net) {
		    $net_name = $net->get_name();
		    $nets_ips = explode(",",$net->get_ips());
		    foreach ($nets_ips as $net_ips) {
			    $net_range = CIDR::expand_CIDR($net_ips,"SHORT","IP");
				$host_list_aux = Host::get_list($conn,"WHERE inet_aton(ip)>=inet_aton('".$net_range[0]."') && inet_aton(ip)<=inet_aton('".$net_range[1]."')");
				foreach ($host_list_aux as $h) {
					$hostin[$h->get_ip()] = $h->get_hostname();
				}
			}
			/*
			foreach($ossim_hosts as $ip => $hname) if ($net->isIpInNet($ip, $net_ips)) {
		        $hostin[$ip] = $hname;
		    }
			*/
		}
	}
    $k = 0;
    $buffer .= "[";
    $html = "";
    foreach($hostin as $ip => $host_name) {
        if($k>=$from && $k<$to) {
            $html.= "{ key:'$key.$k', url:'$ip', icon:'../../pixmaps/theme/host.png', title:'$ip <font style=\"font-size:80%\">($host_name)</font>' },\n";
        }
        $k++;
    }
    if ($k>$to) {
        $html.= "{ key:'$key', page:'$nextpage', isFolder:true, isLazy:true, icon:'../../pixmaps/theme/host.png', title:'"._("next")." $maxresults "._("hosts")."' }";
    }
    if ($html != "") $buffer .= preg_replace("/,$/", "", $html);
    $buffer .= "]";
}
else if ($key=="netgroup") {
    if ($net_group_list = Net_group::get_list($conn, "ORDER BY name")) {
        $buffer .= "[";
        $j = 0;
        foreach($net_group_list as $net_group) {
            if($j>=$from && $j<$to) {
                $net_group_name = $net_group->get_name();
                $nets = $net_group->get_networks($conn, $net_group_name);
                $li = "key:'netgroup_$net_group_name', isLazy:true , url:'NODES:$net_group_name', icon:'../../pixmaps/theme/net_group.png', title:'$net_group_name'\n";
                $buffer .= (($j>$from) ? "," : "") . "{ $li }\n";
            }
            $j++;
        }
        if ($j>$to) {
            $li = "key:'$key', page:'$nextpage', isFolder:true, isLazy:true, icon:'../../pixmaps/theme/net_group.png', title:'"._("next")." $maxresults "._("net groups")."'";
            $buffer .= ",{ $li }\n";
        }
        $buffer .= "]";
    }
}
else if (preg_match("/netgroup_(.*)/",$key,$found)){
    $buffer .= "[";
    $html = "";
    $nets = Net_group::get_networks($conn, $found[1]);
    $k = 0;
    foreach($nets as $net) {
        if($k>=$from && $k<$to) {
            $net_name = $net->get_net_name();
            //if (isset($ossim_nets[$net_name]) && count($ossim_nets[$net_name]) > 0) {
                $html.= "{ key:'$key.$k', url:'".$net->get_net_ips($conn)."', icon:'../../pixmaps/theme/net.png', title:'$net_name' },\n";
            //}
        }
        $k++;
    }
    if ($k>$to) {
        $html.= "{ key:'$key', page:'$nextpage', isFolder:true, isLazy:true, icon:'../../pixmaps/theme/net.png', title:'"._("next")." $maxresults "._("nets")."' }";
    }
    if ($html != "") $buffer .= preg_replace("/,$/", "", $html);
    $buffer .= "]";
}
else if ($key=="all"){
    $buffer .= "[";
    $j = 0;
    foreach($all_cclass_hosts as $cclass => $hg) {
        if($j>=$from && $j<$to) {
            $li = "key:'all_$cclass', isLazy:true, url:'NODES:$cclass', icon:'../../pixmaps/theme/host_add.png', title:'$cclass <font style=\"font-weight:normal;font-size:80%\">(" . count($hg) . " "._("hosts").")</font>'\n";
            $buffer .= (($j>$from) ? "," : "") . "{ $li }\n";
        }
        $j++;
    }
    if ($j>$to) {
        $buffer.= ", { key:'$key', page:'$nextpage', isFolder:true, isLazy:true, icon:'../../pixmaps/theme/host_add.png', title:'"._("next")." $maxresults "._("c-class")."' }";
    }
    $buffer .= "]";
}

else if (preg_match("/all_(.*)/",$key,$found)){
    $html="";
    $buffer .= "[";
    $j = 1;
    $i = 0;
    foreach($all_cclass_hosts as $cclass => $hg) if ($found[1]==$cclass) {
        foreach($hg as $ip) {
            if($i>=$from && $i<$to) {
            $hname = ($ip == $ossim_hosts[$ip]) ? $ossim_hosts[$ip] : "$ip <font style=\"font-size:80%\">(" . $ossim_hosts[$ip] . ")</font>";
            $html.= "{ key:'$key.$j', url:'$ip', icon:'../../pixmaps/theme/host.png', title:'$hname' },\n";
            }
            $i++;
        }
        $j++;
    }
    
    if ($i>$to) {
        $html .= "{ key:'$key', page:'$nextpage', isFolder:true, isLazy:true, icon:'../../pixmaps/theme/host_group.png', title:'"._("next")." $maxresults "._("hosts")."' },";
    }
    
    if ($html != "") $buffer .= preg_replace("/,$/", "", $html);
    $buffer .= "]";
}
else if ($key!="all") {
    $buffer .= "[ {title: '"._("ANY")."', key:'key1', icon:'../../pixmaps/theme/any.png', expand:true, children:[\n";
    $buffer .= "{ key:'hostgroup', page:'', isFolder:true, isLazy:true, icon:'../../pixmaps/theme/host_group.png', title:'"._("Host Group")."'},\n";
    $buffer .= "{ key:'net', page:'', isFolder:true, isLazy:true, icon:'../../pixmaps/theme/net.png', title:'"._("Networks")."'},\n";
    $buffer .= "{ key:'netgroup', page:'', isFolder:true, isLazy:true, icon:'../../pixmaps/theme/net_group.png', title:'"._("Network Groups")."'},\n";
    $buffer .= "{ key:'all', page:'', isFolder:true, isLazy:true , icon:'../../pixmaps/theme/host.png', title:'"._("All Hosts")." <font style=\"font-weight:normal;font-size:80%\">(" . $total_hosts . " "._("hosts").")</font>'}\n";
    $buffer .= "] } ]";
}

if ($buffer=="" || $buffer=="[]")
    $buffer = "[{title:'"._("No Hosts Found")."'}]";

echo $buffer;
error_reporting(0);
$f = fopen($cachefile,"w");
fputs($f,$buffer);
fclose($f);

?>