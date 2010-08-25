<?php
/*****************************************************************************
*
*    License:
*
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
* - ip_max_occurrences()
* - event_max_occurrences()
* - event_max_risk()
* - port_max_occurrences()
* - less_stable_services()
* Classes list:
*/
session_start();
ob_start();
require_once ('classes/Session.inc');
require_once ('classes/Host.inc');
Session::logcheck("MenuReports", "ReportsHostReport");
require_once 'classes/Security.inc';
$host = GET('host');
$hostname = GET('hostname');
$greybox = GET('greybox');
$greybox = 0;
ossim_valid($host, OSS_IP_ADDRCIDR, OSS_NULLABLE, 'illegal:' . _("Host"));
ossim_valid($hostname, OSS_ALPHA, OSS_SPACE, OSS_PUNC, OSS_NULLABLE, 'illegal:' . _("Hostname"));
ossim_valid($greybox, OSS_ALPHA, OSS_NULLABLE, 'illegal:' . _("greybox"));
if (ossim_error()) {
    die(ossim_error());
}
//
require_once 'ossim_db.inc';
require_once 'ossim_conf.inc';
// Database Object
$db = new ossim_db();
$conn = $db->connect();
$conn_snort = $db->snort_connect();


if ($host == "" && GET('hostname') != "") $host = Host::hostname2ip($conn,GET('hostname'));

if ($host == "" && GET('netname') != "") {
	$aux_list = Net::get_list($conn,"WHERE name='".GET('netname')."'");
	$host = preg_replace("/,.*/","",$aux_list[0]->get_ips());
}

$hostname = "Host";
$_SESSION['host_report'] = $host;

$network = 0;
if (preg_match("/\/\d+/",$host)) {
	$network = 1;
}

if ($network) {
	require_once 'classes/Net.inc';
	require_once 'classes/Net_scan.inc';
	$netaux = Net::get_list($conn,"WHERE name='".(Net::get_name_by_ip($conn, $host))."'");
	$net = $netaux[0];
	$notfound = 0;
	if (count($netaux) < 1) {
		$notfound = 1;
	}
	if (!$greybox) {
		if (!$notfound) $name = $net->get_name();
	}
	if ($name == $host) $title = "Network Report: $host";
	else $title = "Network Report: $name($host)";

	$title_graph = preg_replace ("/Network Report: /","",$title);
	
} else {
	if (!$greybox) {
		$hostname = Host::ip2hostname($conn,$host);
	}
	if ($hostname == $host) $title = "Host Report: $host";
	else $title = "Host Report: $hostname($host)";

	$title_graph = preg_replace ("/Host Report: /","",$title);
}

$user = Session::get_session_user();
$conf = $GLOBALS['CONF'];
include ("geoip.inc");
$gi = geoip_open("/usr/share/geoip/GeoIP.dat", GEOIP_STANDARD);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <title> <?php echo $title ?> </title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
  <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
  <link rel="stylesheet" type="text/css" href="../style/style.css"/>
  <link rel="stylesheet" type="text/css" href="../style/top.css">
  <link rel="stylesheet" type="text/css" href="../style/greybox.css"/>
  
<style type="text/css">
.level11  {  background:url(../pixmaps/statusbar/level11.gif) top left;background-repeat:no-repeat;width:86;height:29;padding-left:5px  }
.level10  {  background:url(../pixmaps/statusbar/level10.gif) top left;background-repeat:no-repeat;width:86;height:29;padding-left:5px  }
.level9  {  background:url(../pixmaps/statusbar/level9.gif) top left;background-repeat:no-repeat;width:86;height:29;padding-left:5px  }
.level8  {  background:url(../pixmaps/statusbar/level8.gif) top left;background-repeat:no-repeat;width:86;height:29;padding-left:5px  }
.level7  {  background:url(../pixmaps/statusbar/level7.gif) top left;background-repeat:no-repeat;width:86;height:29;padding-left:5px  }
.level6  {  background:url(../pixmaps/statusbar/level6.gif) top left;background-repeat:no-repeat;width:86;height:29;padding-left:5px  }
.level5  {  background:url(../pixmaps/statusbar/level5.gif) top left;background-repeat:no-repeat;width:86;height:29;padding-left:5px  }
.level4  {  background:url(../pixmaps/statusbar/level4.gif) top left;background-repeat:no-repeat;width:86;height:29;padding-left:5px  }
.level3  {  background:url(../pixmaps/statusbar/level3.gif) top left;background-repeat:no-repeat;width:86;height:29;padding-left:5px  }
.level2  {  background:url(../pixmaps/statusbar/level2.gif) top left;background-repeat:no-repeat;width:86;height:29;padding-left:5px  }
.level1  {  background:url(../pixmaps/statusbar/level1.gif) top left;background-repeat:no-repeat;width:86;height:29;padding-left:5px  }
.level0  {  background:url(../pixmaps/statusbar/level0.gif) top left;background-repeat:no-repeat;width:86;height:29;padding-left:5px  }
.tag_cloud { padding: 3px; text-decoration: none; }
.tag_cloud:link  { color: #17457C; }
.tag_cloud:visited { color: #17457C; }
.tag_cloud:hover { color: #ffffff; background: #17457C; }
.tag_cloud:active { color: #ffffff; background: #ACFC65; }
a {
	font-size:10px;
}
</style>
<script type="text/javascript" src="../js/jquery-1.3.1.js"></script>
<script src="../forensics/js/jquery.flot.pack.js" language="javascript" type="text/javascript"></script>
<script type="text/javascript" src="../js/jquery.progressbar.min.js"></script>
<script type="text/javascript" src="../js/greybox.js"></script>
<script src="../js/jquery.simpletip.js" type="text/javascript"></script>
<script type="text/javascript">
	var url = new Array(50)
	function showTooltip(x, y, contents, link) {
		if (typeof(url[link]) != "undefined") {
			$('<div id="tooltip" class="tooltipLabel"><a href="' + url[link] + '" style="font-size:10px;">' + contents + '<a></div>').css( {
				position: 'absolute',
				display: 'none',
				top: y + 5,
				left: x + 8,
				border: '1px solid #ADDF53',
				padding: '1px 2px 1px 2px',
				'background-color': '#CFEF95',
				opacity: 0.80
			}).appendTo("body").fadeIn(200);
		} else {
			$('<div id="tooltip" class="tooltipLabel">' + contents + '</div>').css( {
				position: 'absolute',
				display: 'none',
				top: y + 5,
				left: x + 8,
				border: '1px solid #ADDF53',
				padding: '1px 2px 1px 2px',
				'background-color': '#CFEF95',
				opacity: 0.80
			}).appendTo("body").fadeIn(200);
		}
	}
</script>

<? $noready=1; include ("../host_report_menu.php") ?>

<script type="text/javascript">
	$(document).ready(function(){
		var graphs = 0;
		$('#loading').toggle();
		$('#host_report<? if ($greybox) echo "_mini" ?>').toggle();
		$.ajax({
			type: "GET",
			url: "ntop_graph.php?n=1&host=<?=$host?>&title=<?=$title_graph?>",
			data: "",
			success: function(msg){
				//alert (msg);
				if (msg != "") {
					document.getElementById('graph1').innerHTML = msg;
					$("a.greybox").click(function(){
						var t = this.title || $(this).text() || this.href;
						var h = ($(this).attr('gbh')) ? $(this).attr('gbh') : 300;
						var w = ($(this).attr('gbw')) ? $(this).attr('gbw') : 370;
						GB_show(t,this.href,h,w);
						return false;
					});
				}
				else document.getElementById('graph1').innerHTML = '<table align="center" class="noborder"><tr><td class="nobborder" style="text-align:center"><?=gettext("No data Available")?></td></tr></table>';
				graphs++;
			}
		});
		<? if (!$network) { ?>
		$.ajax({
			type: "GET",
			url: "ntop_graph.php?n=2&host=<?=$host?>&title=<?=$title_graph?>",
			data: "",
			success: function(msg){
				//alert (msg);
				if (msg != "") {
					document.getElementById('graph2').innerHTML = msg;
					$("a.greybox").click(function(){
						var t = this.title || $(this).text() || this.href;
						var h = ($(this).attr('gbh')) ? $(this).attr('gbh') : 300;
						var w = ($(this).attr('gbw')) ? $(this).attr('gbw') : 370;
						GB_show(t,this.href,h,w);
						return false;
					});
				}
				else document.getElementById('graph2').innerHTML = '<table align="center" class="noborder"><tr><td class="nobborder" style="text-align:center"><?=gettext("No data Available")?></td></tr></table>';
				graphs++;
			}
		});
		<? } ?>
		$('.HostReportMenu').contextMenu({
			menu: 'myMenu'
		},
			function(action, el, pos) {
				var aux = $(el).attr('id').split(/;/);
				var ip = aux[0];
				var hostname = aux[1];
				var url = "../report/host_report.php?host="+ip+"&hostname="+hostname+"&greybox=0";
				if (hostname == ip) var title = "Host Report: "+ip;
				else var title = "Host Report: "+hostname+"("+ip+")";
				//GB_show(title,url,'90%','95%');
				var wnd = window.open(url,'hostreport_'+ip,'fullscreen=yes,scrollbars=yes,location=no,toolbar=no,status=no,directories=no');
				wnd.focus()
			}
		);
		<? if (!$network) { ?>
		$("a.greybox_whois").click(function(){
			var t = "<?=_("Who is '")?>"+this.title+"'";
			var h = 120;
			var w = 400;
			GB_show(t,this.href,h,w);
			return false;
		});
		<? } else { ?>
		$(".scriptinfo_net").simpletip({
			position: 'left',
            baseClass: 'gtooltip',
			onBeforeShow: function() { 
				var data = this.getParent().attr('data');
				this.update(data);
			}
		});
		<? } ?>
		$(".scriptinfo").simpletip({
			position: 'bottom',
			onBeforeShow: function() { 
				var ip = this.getParent().attr('ip');
				this.load('whois.php?ip=' + ip);
			}
		});
	});
</script>

</head>
<body style="margin:0px">
<? if (!Session::hostAllowed($conn, $host)) { ?>
<h1>HOST <?=$host?> <?=gettext("not allowed")?></h1>
</body>
</html>
<? exit; } ?>
<? if ($notfound) { ?>
<h1>NETWORK <?=$host?> <?=gettext("not found")?></h1>
</body>
</html>
<? exit; } ?>
<form><input type="hidden" name="cursor"></form>
<? //include("../hmenu.php") ?>
<? if (1==2) { ?><h1 style="height:23px;padding-top:5px;font-size:16px;margin:0px"><?=$title?></h1><? } ?>
<div id="loading" style="position:absolute;top:40%;left:40%">
	<table class="noborder" style="background-color:white">
		<tr>
			<td class="nobborder" style="text-align:center">
				<span class="progressBar" id="pbar"></span>
			</td>
		</tr>
		<tr>
			<td class="nobborder" id="progressText" style="text-align:center"><?=gettext("Loading Report. Please, wait a few seconds...")?></td>
		</tr>
	</table>
</div>
<script type="text/javascript">
	$("#pbar").progressBar();
	$("#pbar").progressBar(10);
	$("#progressText").html('Loading <b>Report</b>. <br>Please, wait a few seconds...');
</script>
<?
ob_flush();
flush();
usleep(500000);
?>
<div id="host_report<? if ($greybox) echo "_mini" ?>" style="display:none">
<table class="noborder" cellpadding="2" cellspacing="5" width="100%" height="100%">
	<tr>
		<td>
			<table style="background-color:#617F57" height="100%" cellpadding="5">
				<tr><td colspan="3" style="font-size:18px;font-weight:bold;color:#EEEEEE;text-align:left;padding-left:10px"><?=gettext("General Data")?>: <?=preg_replace("/\(/","<font style='font-size:14px'><i> - (",preg_replace("/\)/",")</i></font>",$title_graph))?></td></tr>
				<tr>
					<td class="nobborder" valign="top" width="50%"><? include ("host_report_status.php") ?></td>
					<td valign="top" class="nobborder" width="<?=($network) ? "20%" : "50%"?>"><? if ($network) include ("net_report_inventory.php"); else include ("host_report_inventory.php") ?></td>
					<? if ($network) { ?><td valign="top" class="nobborder"><? include ("net_report_network.php") ?></td><? } ?>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table style="background-color:#727385" height="100%" cellpadding="5">
				<tr><td colspan="3" style="font-size:18px;font-weight:bold;color:#EEEEEE;text-align:left;padding-left:10px">SIEM</td></tr>
				<tr>
					<td class="nobborder" valign="top" width="30%"><? include ("host_report_tickets.php") ?></td>
					<td class="nobborder" valign="top" width="45%"><? include ("host_report_alarms.php") ?></td>
					<td class="nobborder" valign="top" width="25%"><? include ("host_report_vul.php") ?></td>
				</tr>
				<tr><td colspan="3" class="nobborder"><? include ("host_report_sim.php") ?></td></tr>
			</table>
		</td>
	</tr>
	<script type="text/javascript">$("#pbar").progressBar(90);$("#progressText").html('<b><?=gettext("Generating Report")?></b>...');</script><?
	ob_flush();
	flush();
	usleep(500000);
	?><script type="text/javascript">$("#pbar").progressBar(95);</script><?
	ob_flush();
	flush();
	usleep(500000);
	?>
	<tr>
		<td>
			<table style="background-color:#8F6259" height="100%" cellpadding="5">
				<tr><td style="font-size:18px;font-weight:bold;color:#EEEEEE;text-align:left;padding-left:10px">Logger</td></tr>
				<tr><td class="nobborder"><? include ("host_report_sem.php") ?></td></tr>
				<script type="text/javascript">$("#pbar").progressBar(99);$("#progressText").html('<b><?=gettext("Finishing")?></b>...');</script>
				<?
ob_flush();
flush();
usleep(500000);
?>
			</table>
		</td>
	</tr>
</table>
</div>
</body>
<?
$db->close($conn);
$db->close($conn_snort);
?>
</html>
<?
ob_end_flush();
?>
