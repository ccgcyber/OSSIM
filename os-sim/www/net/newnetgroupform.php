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
Session::logcheck("MenuPolicy", "PolicyNetworks");
?>

<html>
<head>
  <title> <?php
echo gettext("OSSIM Framework"); ?> </title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
  <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
  <link rel="stylesheet" type="text/css" href="../style/style.css"/>
  <link type="text/css" rel="stylesheet" href="../style/jquery-ui-1.7.custom.css"/>
  <script type="text/javascript" src="../js/jquery-1.3.1.js"></script>
  <script type="text/javascript" src="../js/jquery.simpletip.js"></script>
  
  <script type="text/javascript">
  $(document).ready(function(){

        $(".sensor_info").simpletip({
                        position: 'top',
                        offset: [-60, 10],
                        content: '',
                        baseClass: 'ytooltip',
                        onBeforeShow: function() {
                                var txt = this.getParent().attr('txt');
                                this.update(txt);
                        }
        });

    });
  </script>
</head>
<body>
                                                                                
<?php
if (GET('withoutmenu') != "1") include ("../hmenu.php"); ?>

<?php
require_once ('ossim_db.inc');
require_once ('ossim_conf.inc');
require_once ('classes/Sensor.inc');
require_once ('classes/Net.inc');
require_once ('classes/Net_sensor_reference.inc');
require_once ('classes/RRD_config.inc');
$db = new ossim_db();
$conn = $db->connect();
$conf = $GLOBALS["CONF"];
$threshold = $conf->get_conf("threshold");
?>

<form method="post" action="newnetgroup.php">
<table align="center">
  <input type="hidden" name="insert" value="insert">
  <tr>
    <th> <?php
echo gettext("Name"); ?> </th>
    <td class="left"><input type="text" name="name" size="30"><span style="padding-left: 3px;">*</span></td>
  </tr>

  <tr>
    <th> <?php
		echo gettext("Sensors"); ?> 
		<a style="cursor:pointer; text-decoration: none;" class="sensor_info" txt="<div style='width: 150px; white-space: normal; font-weight: normal;'>Define which sensors has visibility of this host</div>">
			<img src="../pixmaps/help.png" width="16" border="0" align="absmiddle"/>
		</a><br/>
		<font size="-2">
		  <a href="../sensor/newsensorform.php">
		  <?php echo gettext("Insert new sensor"); ?> ?</a>
		</font>
    </th>
    <td class="left">
<?php
/* ===== Networks ==== */
$i = 1;
if ($network_list = Net::get_list($conn)) {
    foreach($network_list as $network) {
        $network_name = $network->get_name();
        $network_ips = $network->get_ips();
        if ($i == 1) {
?>
        <input type="hidden" name="<?php echo "nnets"; ?>"
            value="<?php echo count($network_list); ?>">
<?php
        }
        $name = "mboxs" . $i;
?>
        <input type="checkbox" name="<?php echo $name; ?>"
            value="<?php echo $network_name; ?>"/>
            <?php echo $network_name . " (" . $network_ips . ")<br>"; ?>
      
<?php
        $i++;
    }
}
?>
    </td>
  </tr>

  <tr>
    <th> <?php
echo gettext("Description"); ?> </th>
    <td class="left">
      <textarea name="descr" rows="3" cols="40"></textarea>
    </td>
  </tr>


  <tr><td style="text-align: left; border:none; padding-top:3px;"><a onclick="$('.advanced').toggle()" style="cursor:pointer;"><img border="0" align="absmiddle" src="../pixmaps/arrow_green.gif">Advanced</a></td></tr>

  
  <tr class="advanced" style="display:none;">
    <th> <?php
echo gettext("RRD Profile"); ?> <br/>
        <font size="-2">
          <a href="../rrd_conf/new_rrd_conf_form.php">
	  <?php
echo gettext("Insert new profile"); ?> ?</a>
        </font>
    </th>
    <td class="left">
      <select name="rrd_profile">
<?php
foreach(RRD_Config::get_profile_list($conn) as $profile) {
    if (strcmp($profile, "global")) {
        echo "<option value=\"$profile\">$profile</option>\n";
    }
}
?>
		<option value="" selected>
	<?php
echo gettext("None"); ?> </option>
      </select>
    </td>
  </tr>

  <tr class="advanced" style="display:none;">
    <th> <?php
echo gettext("Threshold C"); ?> </th>
    <td class="left">
      <input type="text" value="<?php echo $threshold ?>" name="threshold_c" size="11">
    </td>
  </tr>

  <tr class="advanced" style="display:none;">
    <th> <?php
echo gettext("Threshold A"); ?> </th>
    <td class="left">
      <input type="text" value="<?php echo $threshold ?>" name="threshold_a" size="11">
    </td>
  </tr>


  <!--
    <tr>
    <th> <?php
echo gettext("Scan options"); ?> </th>
    <td class="left">
        <input type="checkbox" name="nessus" value="1">
	<?php
echo gettext("Enable nessus scan"); ?> </input>
    </td> 
  </tr>-->


  
  <tr>
    <td colspan="2" align="center" style="border-bottom: none; padding: 10px;">
      <input type="submit" value="<?=_("OK")?>" class="btn" style="font-size:12px">
      <input type="reset" value="<?=_("reset")?>" class="btn" style="font-size:12px">
    </td>
  </tr>
  <tr><td colspan='2'><p align="center" style="font-style: italic;"><?php echo gettext("Values marked with (*) are mandatory"); ?></p></td></tr>
</table>
</form>
<?php
$db->close($conn);
?>
</body>
</html>
