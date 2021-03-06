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
//include charts.php to access the InsertChart function

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<body bgcolor="#FFFFFF">

<?php


include "charts.php";

$width  = ( isset($_GET['width'])  && is_numeric($_GET['width'])) ? $_GET['width'] : "450";
$height = ( isset($_GET['height']) && is_numeric($_GET['height']))? $_GET['height'] : "300";

if (preg_match("/^[a-zA-Z0-9]+[a-zA-Z0-9_\-\.]+\.php$/", $_GET['source_graph'])) 
	echo InsertChart("charts.swf?timeout=120", "charts_library", $_GET['source_graph'] . "?" . $_SERVER['QUERY_STRING']."&bypassexpirationupdate=1", $width, $height, "#FFF");
else
	echo "<br/>Invalid source file!! (It should be a php generating xml for chart.swf).<br>";
?>

</body>
</html>
