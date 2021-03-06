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
* - check_perms()
* Classes list:
*/
require_once ('classes/Session.inc');
Session::useractive("login.php");
//Session::logcheck("MenuConfiguration", "ConfigurationUsers");
require_once ('ossim_acl.inc');
require_once ("classes/Security.inc");
require_once ("languages.inc");
require_once ('classes/Net.inc');
require_once ('classes/Sensor.inc');
require_once ('ossim_db.inc');

// Get password length
$pass_length_min = ($conf->get_conf("pass_length_min", FALSE)) ? $conf->get_conf("pass_length_min", FALSE) : 7;
$pass_length_max = ($conf->get_conf("pass_length_max", FALSE)) ? $conf->get_conf("pass_length_max", FALSE) : 255;

$pass_length_max = ( $pass_length_max < $pass_length_min || $pass_length_max < 1 ) ? 255 : $pass_length_max;



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title> <?php echo gettext("OSSIM Framework"); ?> </title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
	<meta http-equiv="Pragma" content="no-cache"/>
	<link rel="stylesheet" type="text/css" href="../style/style.css"/>
	<link rel="stylesheet" type="text/css" href="../style/tree.css" />
	<link rel="stylesheet" type="text/css" href="../style/greybox.css"/>
	
	<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="../js/greybox.js"></script>
	<script type="text/javascript" src="../js/jquery.checkboxes.js"></script>
	<script type="text/javascript" src="../js/jquery.pstrength.js"></script>
	<script type="text/javascript" src="../js/jquery-ui-1.7.custom.min.js"></script>
	<script type="text/javascript" src="../js/jquery.dynatree.js"></script>
	<script type="text/javascript" src="../js/combos.js"></script>
	<script type="text/javascript">
		function kdbperms (users) {
		document.fmodify.knowledgedb_perms.value = users;
		}
	</script>
		
	<script type="text/javascript">
		var checks = new Array;
		checks['nets'] = 0;
		checks['sensor'] = 0;
		checks['perms'] = 0;
		function checkall(col) {
			if (checks[col]) 
			{
				$("#fmodify").unCheckCheckboxes("."+col, true)
				checks[col] = 0;
			} 
			else 
			{
				$("#fmodify").checkCheckboxes("."+col, true)
				checks[col] = 1;
			}
		}
		
		function checkpasscomplex(pass) {
			<?php if ($conf->get_conf("pass_complex", FALSE) == "yes") { ?>
			var counter = 0;
			if (pass.match(/[a-z]/)) { counter++; }
			if (pass.match(/[A-Z]/)) { counter++; }
			if (pass.match(/[0-9]/)) { counter++; }
			if (pass.match(/[\!\"\�\$\%\&\/\(\)\|\#\~\�\�\.\,\?\=\-\_\<\>]/)) { counter++; }
			return (counter < 3) ? 0 : 1;
			<?php } else { ?>
			return 1;
			<?php } ?>
		}
		
		function checkpasslength() {
			if ($('#pass1').val() != "") {
				if ($('#pass1').val().length < <?php echo $pass_length_min ?>) {
					alert("<?=_("Minimum password size is ").$pass_length_min._(" characters")?>");
					return 0;
				}
				else if ($('#pass1').val().length > <?php echo $pass_length_max ?>) {
					alert("<?=_("Maximum password size is ").$pass_length_max._(" characters")?>");
					return 0;
				} else return 1;
			} else return 1;
		}

		function checkpass() {
			if (document.fmodify.pass1.value != "" && !checkpasscomplex(document.fmodify.pass1.value)) {
				alert("<?=_("Password is not strong enough. Check the password policy configuration for more details")?>");
				return 0;
			}
			else if (document.fmodify.pass1.value != document.fmodify.pass2.value) {
				alert("<?=_("Mismatches in passwords")?>");
				return 0;
			} else return 1;
		}

		function checkemail() {
			var str = document.getElementById('email').value;
			if (str == "" || str.match(/.+\@.+\..+/)) {
				document.getElementById('msg_email').style.display = "none";
				return 1;
			} else {
				document.getElementById('msg_email').style.display = "inline";
				return 0;
			}
		}

		function formsubmit() {
			if (checkpasslength() && checkpass() && checkemail()) {
				
				if ( document.getElementById('nets') != null )
					selectall('nets');
				document.fmodify.submit();
			}
		}

		function load_tree(filter, entity) {
	        combo = 'nets';

	        $("#nets_tree").remove();
	        $('#td_nets').append('<div id="nets_tree" style="width:100%"></div>');

	        $("#nets_tree").dynatree({
	            initAjax: { url: "../net/draw_nets.php", data: {filter: filter, entity: entity} },
	            clickFolderMode: 2,
	            onActivate: function(dtnode) {
	                    if (!dtnode.hasChildren()) {
	                        // add from a final node
	                        addto(combo,dtnode.data.title,dtnode.data.key)
	                    } else {
	                        // simulate expand and load
	                        addnodes = true;
	                        dtnode.toggleExpand();
	                    }
	            },
	            onDeactivate: function(dtnode) {},
	            onLazyRead: function(dtnode){
	                dtnode.appendAjax({
	                    url: "../net/draw_nets.php",
	                    data: {key: dtnode.data.key, filter:filter, entity: entity}
	                });
	            }
	        });
	    }
		
		$(document).ready(function(){
			GB_TYPE = 'w';
			$("a.greybox").click(function(){
				var t = this.title || $(this).text() || this.href;
				GB_show(t,this.href,340,"70%");
				return false;
			});
			load_tree('','');
		});
		
		
	</script>
	
	<style type='text/css'>
		#container_center { margin: 20px auto; width: 90%;}
		#fprofile { width: 500px;}
		#fprofile td input[type='text'], #fprofile td input[type='password']{width: 100%;}
		.bold {font-weight: bold;}
		.ossim_success {width:auto;}
	</style>
	
</head>

<body onload="$('#pass1').pstrength()">

<?php

$user        = GET('user');
$networks    = GET('networks');
$sensors     = GET('sensors');
$perms       = GET('perms');
$copy_panels = intval(GET('copy_panels'));
$frommenu    = GET('frommenu');
$success     = GET('success');


ossim_valid($user, OSS_USER, OSS_NULLABLE, 'illegal:' . _("User name"));
ossim_valid($user, OSS_USER, 'illegal:' . _("User name"));
ossim_valid($networks, OSS_ALPHA, OSS_PUNC, OSS_NULLABLE, 'illegal:' . _("Networks"));
ossim_valid($sensors, OSS_ALPHA, OSS_PUNC, OSS_NULLABLE, 'illegal:' . _("Sensors"));
ossim_valid($perms, OSS_ALPHA, OSS_PUNC, OSS_NULLABLE, 'illegal:' . _("Perms"));
ossim_valid($frommenu, OSS_DIGIT, OSS_NULLABLE, 'illegal:' . _("Frommenu"));
ossim_valid($success, OSS_DIGIT, OSS_NULLABLE, 'illegal:' . _("Success"));

if (ossim_error()) {
    die(ossim_error());
}

if (!Session::am_i_admin() && Session::get_session_user() != $user)
	die(_("You don't have permission to see this page"));

function check_perms($user, $mainmenu, $submenu)
{
    $gacl = $GLOBALS['ACL'];
    return $gacl->acl_check($mainmenu, $submenu, ACL_DEFAULT_USER_SECTION, $user);
}

$db   = new ossim_db();
$conn = $db->connect();

if ($user_list = Session::get_list($conn, "WHERE login = '$user'")) {
    $user = $user_list[0];
}

$net_list = Net::get_all($conn);
$sensor_list = Sensor::get_all($conn, "ORDER BY name ASC");

include ("../hmenu.php");

?>



<div id='container_center'>
	<form name="fmodify" id="fmodify" method="post" action="modifyuser.php">
	
	<table align="center" class='transparent'>
			
		<? if ($success) { ?>
		<tr><td class="center nobborder" style="color:green; padding-bottom:5px;"><div class='ossim_success'><?php echo _("Successfully Saved")?></div></td></tr>
		<? } ?>
		
		<tr><td class="center nobborder"><table class='noborder' width='100%'><tr><th style='padding:5px;'><?php echo _("User Profile")?></th></tr></table></td></tr>
				
		<tr>
			<td class="center nobborder">
				<table align="center" id='fprofile'>
				
					<input type="hidden" name="frommenu" value="<?=$frommenu?>"/>
					<input type="hidden" name="last_pass_change" value="<?=$user->get_last_pass_change()?>"/>
					<input type="hidden" name="insert" value="insert" />
					<input type="hidden" name="user" value="<?php echo $user->get_login() ?>" />
					<tr>
						<th> <?php echo gettext("User login") . required(); ?> </th>
						<td class="left"><strong><?php echo $user->get_login(); ?></strong></td>
					</tr>
					
					<tr>
						<th> <?php echo gettext("User name"). required(); ?> </th>
						<td class="left"><input type="text" name="name" value="<?php echo $user->get_name(); ?>" /></td>
					</tr>
					
					<tr>
						<th> <?php echo gettext("User email"); ?> <img src="../pixmaps/email_icon.gif"></th>
						<td class="left"><input type="text" name="email" id="email" onblur="checkemail()" value="<?php echo $user->get_email(); ?>" />
						<div id="msg_email" style="display:none;border:2px solid red;padding-left:3px;padding-right:3px"><?php echo _("Incorrect email") ?></div>
						</td>
					</tr>
	  
					<tr>
						<th> <?php echo gettext("User language"); ?></th>
						<td class="left">
							<?php
							$lform = "<select name=\"language\">";
							foreach($languages['type'] as $option_value => $option_text)
							{
								$lform.= "<option ";
								if ($user->get_language() == $option_value) $lform.= " selected='selected' ";
									$lform.= "value=\"$option_value\">$option_text</option>";
							}
							$lform.= "</select>";
							echo $lform;
							?>
						</td>
					</tr>
		
					<tr>
						<th><?php echo _("Timezone")?></th>
						<? 
							$tzlist = timezone_identifiers_list();
							sort($tzlist); $utz = $user->get_timezone();
							if ($utz=="0" || $utz=="") $utz="UTC";
						?>
						<td class="nobborder">
							<select name="tzone" id="tzone">
							<?  foreach($tzlist as $tz) if ($tz!="localtime")
									echo "<option value='$tz'".(($utz==$tz) ? " selected='selected'": "").">$tz</option>\n";
							?>
							</select>
						</td>
					</tr>
		
					<tr>
						<th> <?php echo gettext("Company"); ?> </th>
						<td class="left"><input type="text" name="company" value="<?php echo $user->get_company(); ?>" /></td>
					</tr>
					
					<tr>
						<th> <?php echo gettext("Department"); ?> </th>
						<td class="left"><input type="text" name="department" value="<?php echo $user->get_department(); ?>" /></td>
					</tr>
					
					<tr>
						<th><?php echo _("Ask to change password at next login") ?></th>
						<td align="center">
							<input type="radio" name="first_login" value="1"/><span><?php echo _("Yes");?></span>
							<input type="radio" name="first_login" value="0" checked='checked'/><span><?php echo _("No"); ?></span> 
						</td>
					</tr>

					<?php 
					if ($user->get_login() != ACL_DEFAULT_OSSIM_ADMIN && ($_SESSION['_user'] == ACL_DEFAULT_OSSIM_ADMIN || $_SESSION['_is_admin'])) { ?>
					<tr>
						<th><?php echo _("Global Admin") ?></th>
						<td align="center">
							<input type="radio" name="is_admin" value="1" <?php if ($user->get_is_admin()) echo "checked='checked'"?>/><span><?php echo _("Yes");?></span>
							<input type="radio" name="is_admin" value="0" <?php if (!$user->get_is_admin()) echo "checked='checked'"?>/><span><?php echo _("No");?></span> 
						</td>
					</tr>
					<?php
					}
			
					if ($user->get_login() != 'admin') { ?>
						<!--
						<tr>
						<th><?php echo _("Pre-set executive panels to admin panels") ?></th>
							<td align="center">
						   <input type="radio" name="copy_panels" value="1" <? if ($copy_panels) echo "checked='checked'" ?>/><?php echo _("Yes");?>
						   <input type="radio" name="copy_panels" value="0" <? if (!$copy_panels) echo "checked='checked'" ?>/><?php echo _("No");?>
							</td>
						</tr>
						-->
					<?php
					} 
					else
					{ ?>
						<!--<input type="hidden"  name="copy_panels" value="1" checked='checked'>-->
					<?php
					} ?>
					
					<tr>
						<td> <?php echo ($_SESSION['_user'] != $user->get_login()) ? $_SESSION['_user']." ".gettext("password") : gettext("Current password"); ?> </td>
						<td class="left"><input type="password" name="oldpass"/></td>
					</tr>
					<tr>
						<td> <?php echo gettext("Enter new password"); ?> </td>
						<td class="left"><input type="password" name="pass1" id="pass1"/></td>
					</tr>
					
					<tr>
						<td class="nobborder" style="padding:0px"></td>
						<td class="nobborder" style="padding:0px"><div id="pass1_text"></div><div id="pass1_bar"></div></td>
					</tr>
					
					<tr>
						<td> <?php echo gettext("Retype new password"); ?> </td>
						<td class="left"><input type="password" name="pass2" id="pass2" /></td>
					</tr>
					
					<tr>
						<td colspan='2' class="center nobborder" style="padding-top:10px;">
							<input type="button" onclick="formsubmit()" class="button" value="<?php echo _("Ok"); ?>"/>
							<input type="reset" class="button" value="<?php echo _("Reset"); ?>"/>
						</td>
					</tr>
				</table>	
				
			</td>
		</tr>
	
	</table>
	
	<? if (Session::am_i_admin() && $user->get_login() != Session::get_session_user()) { ?>

	<br/>
	  
	<table align="center" cellspacing='8'>
		<tr>
			<th> <?php echo gettext("Allowed nets");?></th>
			<td class="nobborder"></td>
			<th> <?php echo gettext("Allowed sensors");?></th>
			<td class="nobborder"></td>
			<th colspan='2'><?php echo gettext("Allowed Sections"); ?> </th>
		</tr>
		
		<tr>
			<td class="nobborder" valign="top" style="padding-top:8px">
				<table>
					 <tr>
                     	<td class="left nobborder">
							<select style="width:100%;height:90%" multiple="multiple" size="19" name="nets[]" id="nets">
                            <?php
                            /* ===== Networks ==== */
                            foreach($net_list as $net) 
							{
                            	$net_name = $net->get_name();
                                $net_ips  = $net->get_ips();
                                $cidrs    = explode(",",$net_ips);
                                foreach($cidrs as $cidr)
								{
                                	if (false !== strpos(Session::allowedNets($user->get_login()) , $net_ips)) {
                                			echo "<option value='$net_name'>$net_name ($net_ips)</option>";
                                	}
								}
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                    	<td class="nobborder" style="text-align:right">
                        <input type="button" value=" [X] " onclick="deletefrom('nets')" class="lbutton"/>
                        <input type="button" style="margin-right:0px;" value="<?php echo gettext("Clean All Nets");?>" onclick="deleteall('nets')" class="lbutton"/>
                        </td>
                    </tr>
                    <tr>
                        <td class="left nobborder">
                            <i><?php echo gettext("NOTE: No selection allows ALL") . " " . gettext("nets"); ?></i>
                        </td>
                    </tr>
                    <tr>
                        <td class="left nobborder" style="padding-top:10px;">
                            <div>
                                <div style="float:left">
                                    <?php echo _("Filter")?>: <input type="text" id="filtern" name="filtern" style="height: 18px;width: 170px;" />
                                </div>
                                <div style="float:right">
                                    <input type="button" style="margin-right:0px;" class="lbutton" value="<?=_("Apply")?>" onclick="load_tree(document.fmodify.filtern.value,'<?php echo $current_entity ?>')" /> 
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="nobborder" id="td_nets">
                        </td>
                    </tr>
				</table>
			</td>
			
			<td class="noborder" style="border-right:2px solid #E0E0E0;"></td>
			
			<td valign="top" class="nobborder" style="padding-top:8px">
				<a href="#" onclick="checkall('sensor');return false;"><?php echo gettext("Select / Unselect all"); ?></a>
				<hr noshade='noshade'>
				<?php
				$i = 0;
				foreach($sensor_list as $sensor) {
					$sensor_name = $sensor->get_name();
					$sensor_ip = $sensor->get_ip();
					$input = "<input type=\"checkbox\" class=\"sensor\" name=\"sensor$i\" value=\"" . $sensor_ip . "\"";
					if (false !== strpos(Session::allowedSensors($user->get_login()) , $sensor_ip)) {
						$input.= " checked='checked' ";
					}
					if ($sensors || ($user->get_login() == ACL_DEFAULT_OSSIM_ADMIN)) {
						$input.= " checked='checked' ";
					}
					if ($user->get_login() == 'admin') {
						$input.= "disabled='disabled'";
					}
					$input.= "/>$sensor_name<br/>";
					echo $input;
					$i++;
				}
				?>
				<input type="hidden" name="nsensors" value="<?php echo $i ?>"/>
				<br/><br/><i><?php echo gettext("NOTE: No selection allows ALL") . " " . gettext("sensors"); ?></i>
			</td>
		
			<td class="noborder" style="border-right:2px solid #E0E0E0;"></td>
		
			<td class="nobborder" valign="top">
				<table class="noborder">
					<tr>
						<td class="nobborder">
							<a href="#" onclick="checkall('perms');return false;"><?php echo _("Select / Unselect all");?></a>
						</td>
						<td class="nobborder" style="color:#777777;text-align:center;" nowrap='nowrap'>
							<span style="color:black"><strong><?php echo _("Granularity")?></strong> <?php echo _("Net")?> / <?php echo _("Sensor")?></span>
							<!--<br><img src="../pixmaps/tick.png"> <i>Checked is filtered</i>-->
						</td>
					</tr>
					
					<tr><td colspan='2' class="nobborder"><hr noshade='noshade'></td></tr>
					
					<input type="hidden" name="knowledgedb_perms" value=""/>
					
					<?php
					include ("granularity.php");
					include ("perms_sections.php");
					
					foreach($ACL_MAIN_MENU as $mainmenu => $menus)
					{
						foreach($menus as $key => $menu)
						{
							$color = ($i++ % 2 != 0) ? "bgcolor='#f2f2f2'" : "";
					?>
								<tr <?=$color?>>
									<td class="nobborder">
									<? if ($perms_sections[$key] != "") { ?>
									<a href="<?=$perms_sections[$key]?>?user=<?=$user->get_login()?>" title="Permissions Submenu" class="greybox"><img src="../pixmaps/plus.png" border=0></a>
									<? } else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?>
									<input type="checkbox" class="perms" name="<?php echo $key ?>"
									<?php
											$checked = 0;
											if ($user->get_login() == 'admin') echo " disabled='disabled'";
											if ($perms) $checked = 1;
											if (check_perms($user->get_login() , $mainmenu, $key)) $checked = 1;
											//if ($perms || ($user->get_login() == 'admin')) echo " checked ";
											if ($checked) echo " checked='checked'";
											echo "/>";
									?>
									<?php
											$sensor_tick = ($granularity[$mainmenu][$key]['sensor']) ? "<img src='../pixmaps/tick.png'/>" : "<img src='../pixmaps/tick_gray.png'/>";
											$net_tick = ($granularity[$mainmenu][$key]['net']) ? "<img src='../pixmaps/tick.png'/>" : "<img src='../pixmaps/tick_gray.png'/>";
											echo $menu["name"] . "</td><td class='nobborder' style='text-align:center'>".$net_tick." ".$sensor_tick."</td></tr>\n";
						}
						
						echo "<tr><td colspan=2 class='nobborder'><hr noshade='noshade'></td></tr>";
					}
					?>
		
		
				</table>
			</td>
		</tr>
	</table>

	<br/>
	
	<table align="center" class='transparent'>
		<tr>
			<td class="center nobborder" style="padding-top:10px;">
				<input type="button" onclick="formsubmit()" class="button" value="<?php echo _("Ok"); ?>"/>
				<input type="reset" class="button" value="<?php echo gettext("Reset"); ?>"/>
			</td>
		</tr>
	</table>
	
	</form>
	<? } ?>
	
</div>

</body>
</html>

