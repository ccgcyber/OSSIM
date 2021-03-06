<?php
/**
* Class and Function List:
* Function list:
* Classes list:
*/
/*******************************************************************************
** OSSIM Forensics Console
** Copyright (C) 2009 OSSIM/AlienVault
** Copyright (C) 2004 BASE Project Team
** Copyright (C) 2000 Carnegie Mellon University
**
** (see the file 'base_main.php' for license details)
**
** Built upon work by Roman Danyliw <rdd@cert.org>, <roman@danyliw.com>
** Built upon work by the BASE Project Team <kjohnson@secureideas.net>
*/
/**
 *  Check to see if the base_conf.php file exists and is big enough...
 *  if not redirect to the setup/index.php page
 */
if (!file_exists('base_conf.php') || filesize('base_conf.php') < 10) {
    header('Location: setup/index.php');
    die();
}
require ("base_conf.php");
include ("$BASE_path/includes/base_include.inc.php");
include_once ("$BASE_path/base_db_common.php");
include_once ("$BASE_path/base_common.php");
$errorMsg = "";
$displayError = 0;
$noDisplayMenu = 1;
// Redirect to base_main.php if auth system is off
if ($Use_Auth_System == 0) {
    base_header("Location: base_main.php");
}
if (isset($_POST['submit'])) {
    $debug_mode = 0; // wont login with debug_mode
    $BASEUSER = new BaseUser();
    $user = filterSql($_POST['login']);
    $pwd = filterSql($_POST['password']);
    if (($BASEUSER->Authenticate($user, $pwd)) == 0) {
        header("Location: base_main.php");
        exit();
    }
} else {
    $displayError = 1;
    $errorMsg = gettext("User does not exist or your password was incorrect!<br>Please try again");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- <?php
echo gettext("Forensics Console " . $BASE_installID) . $BASE_VERSION; ?> -->
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php
echo (gettext("iso-8859-1")); ?>" />
  <meta http-equiv="pragma" content="no-cache" />
  <title><?php
echo (gettext("Forensics Console " . $BASE_installID) . $BASE_VERSION); ?></title>
  <link rel="stylesheet" type="text/css" href="styles/<?php
echo ($base_style); ?>" />
<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
</head>
<body onload="javascript:document.loginform.login.focus();">
  <div class="mainheadertitle">&nbsp;
<?php
echo gettext("Forensics Console " . $BASE_installID); ?>
  </div><br />
<?php
if ($displayError == 1) {
    echo "<div class='errorMsg' align='center'>$errorMsg</div>";
}
?>
<form action="index.php" method="post" name="loginform">
  <table width="75%" style='border:0;padding:0;margin:auto;'>
    <tr>
      <td align="right" width="50%"><?php
echo gettext("Login:"); ?>&nbsp;</td>
      <td align="left" width="50%"><input type="text" name="login"></td>
    </tr>
    <tr>
      <td align="right"><?php
echo gettext("Password:"); ?>&nbsp;</td>
      <td align="left"><input type="password" name="password" /></td>
    </tr>
    <tr>
      <td colspan="2" align="center">
        <input type="submit" class="button" name="submit" value="Login" />
        <input type="reset" name="reset" />
      </td>
    </tr>
  </table>
</form>
<?php
include ("$BASE_path/base_footer.php");
?>
</body>
</html>
