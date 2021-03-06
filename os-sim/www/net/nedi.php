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
//error_reporting(E_NOTICE);
require_once ('classes/Session.inc');
require_once ('classes/CIDR.inc');
Session::logcheck("MenuConfiguration", "NetworkDiscovery");

// load column layout
require_once ('../conf/layout.php');

$update = $_GET["update"];
ossim_valid($update, OSS_NULLABLE, OSS_DIGIT, 'illegal:' . _("update"));
if (ossim_error()) {
    die(ossim_error());
}

$nedi_autodiscovery = intval($_GET["nedi_autodiscovery"]);
ossim_valid($nedi_autodiscovery, OSS_NULLABLE, OSS_DIGIT, 'illegal:' . _("nedi autodiscovery"));
if (ossim_error()) {
    die(ossim_error());
}

$category    = "policy";
$name_layout = "nedi_layout";
$layout      = load_layout($name_layout, $category);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title> <?php echo gettext("OSSIM Framework"); ?> </title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta http-equiv="Pragma" content="no-cache"/>
	<link rel="stylesheet" type="text/css" href="../style/style.css"/>
	<link rel="stylesheet" type="text/css" href="../style/flexigrid.css"/>
	<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="../js/jquery.flexigrid.js"></script>
	<script type="text/javascript" src="../js/urlencode.js"></script>
	<style type='text/css'>
		table, th, tr, td {
			background:transparent;
			border-radius: 0px;
			-moz-border-radius: 0px;
			-webkit-border-radius: 0px;
			border:none;
			padding:0px; margin:0px;
		}
		input[type=text], select {
			border-radius: 0px;
			-moz-border-radius: 0px;
			-webkit-border-radius: 0px;
			border: 1px solid #8F8FC6;
			font-size:12px; font-family:arial; vertical-align:middle;
			padding:0px; margin:0px;
		}
	</style>
</head>
<body>

<?php
$db     = new ossim_db();
$dbconn = $db->connect();

if (intval($update)==1) {
    $query = "UPDATE config SET value = '$nedi_autodiscovery' WHERE conf='nedi_autodiscovery'";
    $result = $dbconn->Execute($query);
}

$query = "SELECT value FROM config WHERE conf='nedi_autodiscovery'";
$result = $dbconn->Execute($query);

include ("../hmenu.php");
echo "<form action=\"nedi.php\" method=\"get\">";
echo "<input type=\"hidden\" name=\"update\" value=\"1\"/>";
echo "<center><table class=\"transparent\">";
echo "<tr>";
echo "      <td class=\"nobborder\" style=\"text-align:left;\" nowrap>";
echo "      <input type=\"checkbox\" name=\"nedi_autodiscovery\" value=\"1\" ".(($result->fields['value']=="1")? "checked":"")."/>";
echo "      &nbsp;"._("Nedi Autodiscovery");
echo "      </td>";
echo "      <td class=\"nobborder\" style=\"text-align:left;padding-left:10px;\">";
echo "      <input type=\"submit\" class='button' value=\""._("Update")."\"/>";
echo "      </td>";
echo "</tr>";
echo "</table></center>";
echo "</form><br/>";
?>
	<div  id="headerh1" style="width:100%;height:1px">&nbsp;</div>
	
	<table id="flextable" style="display:none"></table>

	
	<script type='text/javascript'>
	function get_width(id) {
		if (typeof(document.getElementById(id).offsetWidth)!='undefined') 
			return document.getElementById(id).offsetWidth-20;
		else
			return 700;
	}
	
	function get_height() {
	   return parseInt($(document).height()) - 250;
	}	
	
	function action(com,grid) {
		var items = $('.trSelected', grid);
		if (com=='<?php echo gettext("Delete selected"); ?>') {
			//Delete host by ajax
			if (typeof(items[0]) != 'undefined') {
				$("#flextable").changeStatus('<?php echo _("Deleting device")?>...',false);
				$.ajax({
						type: "GET",
						url: "deletedevice.php?confirm=yes&ip="+urlencode(items[0].id.substr(3)),
						data: "",
						success: function(msg) {
							if(msg.match("ERROR_CANNOT")) alert("<?php echo _("Sorry, cannot delete this host because it belongs to a policy")?>");
							else $("#flextable").flexReload();
						}
				});
			}
			else alert('<?php echo _("You must select a device")?>');
		}
		else if (com=='<?php echo _("Modify"); ?>') {
			if (typeof(items[0]) != 'undefined') document.location.href = 'modifydevice.php?ip='+urlencode(items[0].id.substr(3))
			else alert('<?php echo _("You must select a device")?>');
		}
		else if (com=='<?php echo _("New"); ?>') {
			document.location.href = 'newdevice.php'
		}
	}
    
	function linked_to(rowid) {
        document.location.href = 'modifydevice.php?ip='+urlencode(rowid);
    }	
	
	function save_layout(clayout) {
		$("#flextable").changeStatus('<?php echo _("Saving column layout")?>...',false);
		$.ajax({
				type: "POST",
				url: "../conf/layout.php",
				data: { name:"<?php echo $name_layout ?>", category:"<?php echo $category ?>", layout:serialize(clayout) },
				success: function(msg) {
					$("#flextable").changeStatus(msg,true);
				}
		});
	}
    
	$("#flextable").flexigrid({
        url: 'getdevice.php',
        dataType: 'xml',
        colModel : [
			<?php
				$default = array(
					"ip" => array(
						_("Device Address"),
						100,
						'true',
						'left',
						false
					) ,
					"community" => array(
						_("SNMP Community"),
						100,
						'true',
						'center',
						false
					) ,
					"description" => array(
						_("Description"),
						300,
						'true',
						'center',
						false
					)
				);
				list($colModel, $sortname, $sortorder, $height) = print_layout($layout, $default, "community", "asc", 300);
				echo "$colModel\n";
			?>
			],
		buttons : [
			{name: '<?php echo _("New")?>', bclass: 'add', onpress : action},
			{separator: true},
			{name: '<?php echo _("Modify")?>', bclass: 'modify', onpress : action},
			{separator: true},
			{name: '<?php echo _("Delete selected")?>', bclass: 'delete', onpress : action},
			{separator: true}
			],
		searchitems : [
			{display: '<?php echo _("IP")?>', name : 'ip', isdefault: true},
            {display: '<?php echo _("Community")?>', name : 'community'}
			],
		sortname: "<?php echo $sortname ?>",
		sortorder: "<?php echo $sortorder ?>",
		usepager: true,
		title: '<?php echo _("Devices")?>',
		pagestat: '<?php echo _("Displaying {from} to {to} of {total} devices")?>',
		nomsg: '<?php echo _("No Devices")?>',
		useRp: true,
		rp: 20,
		//contextMenu: 'myMenu',
		showTableToggleBtn: true,
		singleSelect: true,
		width: get_width('headerh1'),
		height: get_height(),
		onColumnChange: save_layout,
		onDblClick: linked_to,
		onEndResize: save_layout
	});   
	
	</script>

</body>
</html>
<?php $db->close($dbconn); ?>
