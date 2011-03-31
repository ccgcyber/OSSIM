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
// menu authentication
require_once ('classes/Session.inc');
Session::logcheck("MenuPolicy", "ToolsScan");
ini_set("max_execution_time","1200");
ob_implicit_flush();
?>

<html>
<head>
  <title> <?php
echo gettext("OSSIM Framework"); ?> </title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
  <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
  <link rel="stylesheet" type="text/css" href="../style/style.css"/>
  <script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
  <script type="text/javascript">
	function stop_nmap(asset) {
		$.ajax({
			type: "POST",
			url: "do_scan.php?only_stop=1&net="+asset,
			success: function(msg){
				$('#loading').html("");
			}
		});
	}
  </script>
</head>
<body style="background-color:#FAFAFA">

<?php
require_once 'classes/Security.inc';
$net = GET('net');
$full_scan = GET('full_scan');
$timing_template = GET('timing_template');
$only_stop = GET('only_stop');
$only_status = GET('only_status');
ossim_valid($net, OSS_ALPHA, OSS_PUNC, OSS_NULLABLE, 'illegal:' . _("Net"));
ossim_valid($full_scan, OSS_ALPHA, OSS_SCORE, OSS_NULLABLE, 'illegal:' . _("full scan"));
ossim_valid($timing_template, OSS_ALPHA, OSS_PUNC, OSS_NULLABLE, 'illegal:' . _("timing_template"));
ossim_valid($only_stop, OSS_DIGIT, OSS_NULLABLE, 'illegal:' . _("only stop"));
ossim_valid($only_status, OSS_DIGIT, OSS_NULLABLE, 'illegal:' . _("only status"));
if (ossim_error()) {
    die(ossim_error());
}
require_once ('classes/Scan.inc');

if ($only_stop) {
	$scan = new Scan($net);
	$scan->stop_nmap($net);
	exit;
}
if ($only_status) {
	session_write_close();
	$scanning_nets = Scan::scanning_what();
	if (count($scanning_nets) > 0) {
		// print html
		foreach($scanning_nets as $net) {
			echo _("Scanning network") . " ($net), " . _(" locally, please wait") . "...<br><div id='loading_$net'><img src='../pixmaps/loading.gif' align='absmiddle' width='16'> <input type='button' class='button' onclick='stop_nmap(\"$net\")' value='"._("Stop Scan")."'></div><br>\n";
		}
		?><script type="text/javascript">parent.doIframe();</script><?php
		// change status
		while(Scan::scanning_now()) {
			foreach($scanning_nets as $net) {
				$tmp_file = sprintf(NMAP_ROOT_TMP_FILE, str_replace("/","_",$net));
	       		if (file_exists($tmp_file)) {
					$lines = file($tmp_file);
					$perc = 0;
					foreach ($lines as $line) {
						if (preg_match("/About\s+(\d+\.\d+)\%/",$line,$found)) {
							$perc = $found[1];
						}
					}
					if ($perc > 0) {
						?><script type="text/javascript">document.getElementById('loading_<?php echo $net?>').innerHTML = "Scan: <?php echo $found[1] ?>%";</script><?php
					}
				}
	        }
	        sleep(3);
		}
	} else {
		echo "No nmap process found.";
	}
	exit;
}

$rscan = new RemoteScan($net,($full_scan=="full") ? "root" : "ping");
if ($rscan->available_scan()) { // $full_scan!="full" && 
	
	// try remote nmap
	echo _("Scanning network") . " ($net), " . _(" with a remote sensor, please wait") . "...<br/>\n";
	$rscan->do_scan(FALSE);
	if ($rscan->err()!="") 
		echo _("Failed remote network scan: ") . "<font color=red>".$rscan->err() ."</font><br/>\n";
	else
		$rscan->save_scan();
	
} else {
?><script type="text/javascript">parent.document.getElementById('scan_button').disabled = true</script><?php
	echo _("Unable to launch remote network scan: ") . "<font color=red>".$rscan->err() ."</font><br/>\n"; // if ($full_scan!="full") 
	echo _("Scanning network") . " ($net), " . _(" locally, please wait") . "...<br><div id='loading'><img src='../pixmaps/loading.gif' align='absmiddle' width='16'> <input type='button' class='button' onclick='stop_nmap(\"$net\")' value='"._("Stop Scan")."'></div><br>\n";
	?><script type="text/javascript">parent.doIframe();</script><?php
	// try local nmap
	$scan = new Scan($net);
	$scan->append_option($timing_template);
	// $full_scan can be: null, "fast" or "full"
	if ($full_scan) {
	    if ($full_scan == "fast") {
	        $scan->append_option("-F");
	        $scan->do_scan(TRUE);
	    } else {
	        $scan->do_scan(TRUE);
	    }
	} else {
	    $scan->do_scan(FALSE);
	}
	//$scan->save_scan();
	
}
echo gettext("Scan completed") . ".<br/><br/>";
if (count($scan->scan) > 0) { echo "<a href=\"index.php#results\" target=\"_parent\">" . gettext("Click here to show the results") . "</a>"; }
?>
<script type="text/javascript">$('#loading').html("");parent.document.getElementById('scan_button').disabled = false</script>
</body>
</html>


