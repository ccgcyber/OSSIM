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
Session::logcheck("MenuPolicy", "PolicySignatures");
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
echo gettext("Delete signature group"); ?> </h1>

<?php
require_once ("classes/Security.inc");
$sig_name = GET('signame');
ossim_valid($sig_name, OSS_PUNC, OSS_ALPHA, 'illegal:' . _("Signature name"));
if (ossim_error()) {
    die(ossim_error());
}
if (GET('confirm')) {
?>
    <p> <?php
    echo gettext("Are you sure"); ?>? </p>
    <p><a href="<?php
    echo $_SERVER["SCRIPT_NAME"] . "?signame=$sig_name&confirm=yes"; ?>"> 
    <?php
    echo gettext("Yes"); ?> </a>&nbsp;&nbsp;&nbsp;<a href="signature.php"> 
    <?php
    echo gettext("No"); ?></a>
    </p>
<?php
    exit();
}
require_once 'ossim_db.inc';
require_once 'classes/Signature_group.inc';
$db = new ossim_db();
$conn = $db->connect();
Signature_group::delete($conn, $sig_name);
$db->close($conn);
?>

    <p> <?php
echo gettext("Signature group deleted"); ?> </p>
    <p><a href="signature.php"> <?php
echo gettext("Back"); ?> </a></p>
    <?php
exit(); ?>

</body>
</html>
