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
require_once ('classes/Session.inc');
Session::logcheck("MenuPolicy", "PolicyHosts");
?>

<html>
<head>
  <title> <?php
echo gettext("OSSIM Framework"); ?> </title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
  <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
  <link rel="stylesheet" type="text/css" href="../style/style.css"/>
</head>
<body>

  <h1> <?php
echo gettext("Delete host group"); ?> </h1>

<?php
require_once 'classes/Security.inc';
$name = GET('name');
ossim_valid($name, OSS_ALPHA, OSS_SPACE, OSS_PUNC, OSS_SQL, 'illegal:' . _("name"));
$type = GET('type');
ossim_valid($type, OSS_NULLABLE, 'groupAndHosts', 'illegal:' . _("type"));
if (ossim_error()) {
    die(ossim_error());
}
if (!GET('confirm')) {
?>
    <p> <?php
    echo gettext("Are you sure"); ?> ?</p>
    <p><a
      href="<?php
    echo $_SERVER["SCRIPT_NAME"] . "?name=$name&confirm=yes"; ?>">
      <?php
    echo gettext("Yes"); ?> </a>
      &nbsp;&nbsp;&nbsp;<a href="hostgroup.php">
      <?php
    echo gettext("No"); ?> </a>
    </p>
<?php
    exit();
}
require_once 'ossim_db.inc';
require_once 'classes/Host.inc';
require_once 'classes/Host_group.inc';
require_once 'classes/Host_group_scan.inc';
require_once 'classes/Host_group_reference.inc';
$db = new ossim_db();
$conn = $db->connect();
$hosts_list = Host_group_reference::get_list($conn, $name, "2007");
$i = 0;
if (Host_group::can_delete($conn,$name)) {
	$hostip=array();
	foreach($hosts_list as $host){
		$hostip[$i++] = $host->get_host_ip();
	}
	
	foreach($hostip as $host) {
		if (Host_group_scan::can_delete_host_from_nagios($conn, $host, $name)) {
			require_once 'classes/NagiosConfigs.inc';
			$q = new NagiosAdm();
			$q->delHost(new NagiosHost($host, $host, ""));
			$q->close();
		}
	}
	
	if (Host_group_scan::in_host_group_scan($conn, $name, 2007)){
		Host_group_scan::delete($conn, $name, 2007);
	}
	
	// get list host and Delete
	if($type=='groupAndHosts'){
		require_once 'classes/Host_scan.inc';
		$host_list=Host_group::get_hosts($conn, $name);
		foreach($host_list as $host){
			$ip=$host->get_host_ip();
			if (Host::can_delete($conn,$ip)) {
				if (Host_scan::in_host_scan($conn, $ip, 2007)){
					Host_scan::delete($conn, $ip, 2007);
				}
				Host_scan::delete($conn, $ip, 3001);
				Host::delete($conn, $ip);
			}
		}
	}
	//
	Host_group_scan::delete($conn, $name, 3001);
	Host_group::delete($conn, $name);
} else {
	echo "ERROR_CANNOT";
}
$db->close($conn);
?>

    <p> <?php
echo gettext("Host group deleted"); ?> </p>
    <p><a href="hostgroup.php">
    <?php
echo gettext("Back"); ?> </a></p>
    <?php
exit(); ?>

</body>
</html>

