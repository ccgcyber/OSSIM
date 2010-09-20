<?php 

header("Content-type: application/xml"); 
require_once ('classes/Session.inc');
Session::logcheck("MenuEvents", "EventsForensics");

require_once 'ossim_db.inc';

$db = new ossim_db();
$conn = $db->connect();
$xml = "";

$sql = "SELECT * FROM `datawarehouse`.`report_data` WHERE id_report_data_type = 33 AND USER = ? ";

$user = $_SESSION['_user'];
settype($user, "string");
$params = array(
	$user
);

			
if (!$rs = $conn->Execute($sql, $params)) {
	print 'Error: ' . $conn->ErrorMsg() . '<br/>';
	exit;
}

$xml .= "<data>";

while( !$rs->EOF ) {
	
	$date = explode (" ",  $rs->fields['dataV2']);
	$d = explode("-", $date[0]);
	$t = explode(":", $date[1]);
	
	$timestamp = mktime($t[0], $t[1], $t[2], $d[1], $d[2], $d[0]);
	$format_date = date("M d Y G:i:s", $timestamp)." GMT";
	
		
	$flag = preg_replace("/http\:\/\/(.*?)\//","/",$rs->fields['dataV4']);
			
	$xml .="<event start='".$format_date."' title='".str_replace("'", "\"", htmlentities($rs->fields['dataV1']))."' ";
	$xml .="link='./base_qry_alert.php?submit=#".$rs->fields['dataI1']."-(".$rs->fields['dataI2']."-".$rs->fields['dataI3'].")&amp;sort_order=time_d'";
	$flag = ( $flag =="" ) ? "/ossim/pixmaps/1x1.png" : $flag;
	$xml .= " icon='$flag'>";
	
	
	$xml .= htmlentities("<div class='bubble_desc'>".$rs->fields['dataV1']."<br/><br/><div class='txt_desc'>".$rs->fields['dataV3'].":<img src='".$rs->fields['dataV4']."'/> -> ".$rs->fields['dataV5'].":<img src='".$rs->fields['dataV6']."'/></div><div class='df'>".$format_date."</div></div>");
	$xml .="</event>"; 
	$rs->MoveNext();
}
			
$xml .= "</data>";
echo $xml;
$db->close($conn);



?>




	
	
	
	
	
		
       
   
   


