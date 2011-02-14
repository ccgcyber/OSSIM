<?php
require_once ('classes/Session.inc');
require_once ('classes/Security.inc');
require_once 'classes/Util.inc';
require_once 'sensor_filter.php';
Session::logcheck("MenuControlPanel", "ControlPanelExecutive");

function SIEM_trends($h=24) {
	global $timetz;
	$data = array();
	require_once 'ossim_db.inc';
	$db = new ossim_db();
	$dbconn = $db->snort_connect();
	$sensor_where = make_sensor_filter($dbconn);
	$sqlgraph = "SELECT COUNT(acid_event.sid) as num_events, hour(timestamp) as intervalo, day(timestamp) as suf FROM acid_event WHERE timestamp BETWEEN '".date("Y-m-d H:i:s",$timetz-(3660*$h))."' AND '".date("Y-m-d H:i:s",$timetz)."' $sensor_where GROUP BY suf,intervalo";
	if (!$rg = & $dbconn->Execute($sqlgraph)) {
	    print $dbconn->ErrorMsg();
	} else {
	    while (!$rg->EOF) {
	        $data[$rg->fields["intervalo"]."h"] = $rg->fields["num_events"];
	        $rg->MoveNext();
	    }
	}
	$db->close($dbconn);
	return $data;
}

function SIEM_trends_week($param="") {
	global $timetz;
	$data = array();
	$plugins = $plugins_sql = "";
	require_once 'ossim_db.inc';
	$db = new ossim_db();
	$dbconn = $db->connect();
	$sensor_where = make_sensor_filter($dbconn);
	if ($param!="") {
		require_once("classes/Plugin.inc");
		$oss_p_id_name = Plugin::get_id_and_name($dbconn, "WHERE name LIKE '$param'");
		$plugins = implode(",",array_flip ($oss_p_id_name));
		$plugins_sql = "AND acid_event.plugin_id in ($plugins)";
	}
	$sqlgraph = "SELECT COUNT(acid_event.sid) as num_events, day(timestamp) as intervalo, monthname(timestamp) as suf FROM snort.acid_event LEFT JOIN ossim.plugin ON acid_event.plugin_id=plugin.id WHERE timestamp BETWEEN '".date("Y-m-d 00:00:00",$timetz-604800)."' AND '".date("Y-m-d  23:59:59",$timetz)."' $plugins_sql $sensor_where GROUP BY suf,intervalo ORDER BY suf,intervalo";
	if (!$rg = & $dbconn->Execute($sqlgraph)) {
	    print $dbconn->ErrorMsg();
	} else {
	    while (!$rg->EOF) {
	        $hours = $rg->fields["intervalo"]." ".substr($rg->fields["suf"],0,3);
	        $data[$hours] = $rg->fields["num_events"];
	        $rg->MoveNext();
	    }
	}
	$db->close($dbconn);
	return ($param!="") ? array($data,$oss_plugin_id) : $data;
}

function Logger_trends() {
	require_once("forensics_stats.inc");
	global $timetz;
	$data = $hours = array();
	$csv = get_day_csv(date("Y",$timetz),date("m",$timetz),date("d",$timetz));
	foreach ($csv as $key => $value) $data[$key."h"] = $value;
	return $data;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title>Event Trends</title>
		<script language="javascript" src="../js/raphael/raphael.js"></script>
        <script language="javascript" src="../js/jquery-1.3.2.min.js"></script>
        <style type="text/css"> body { overflow:hidden; } </style>
</head>
<?php
$max = 16;
$hours = $trend = $trend2 = array();
//
if (GET("type")=="siemday") { 
    $js = "analytics";
    $data = SIEM_trends(16);
    foreach ($data as $h => $v) {
    	$hours[] = $h;
    	$trend[] = ($v!="") ? $v : 0;
    }
    $max = count($hours);
    $siem_url = "../forensics/base_qry_main.php?clear_allcriteria=1&time_range=day&time[0][0]=+&time[0][1]=>%3D&time[0][2]=".date("m",$timetz)."&time[0][3]=".date("d",$timetz)."&time[0][4]=".date("Y",$timetz)."&time[0][5]=HH&time[0][6]=00&time[0][7]=00&time[0][8]=+&time[0][9]=AND&time[1][0]=+&time[1][1]=<%3D&time[1][2]=".date("m",$timetz)."&time[1][3]=".date("d",$timetz)."&time[1][4]=".date("Y",$timetz)."&time[1][5]=HH&time[1][6]=59&time[1][7]=59&time[1][8]=+&time[1][9]=+&submit=Query+DB&num_result_rows=-1&time_cnt=2&sort_order=time_d&hmenu=Forensics&smenu=Forensics";
} elseif (GET("type")=="siemweek") { 
    $js = "analytics";
    $data = SIEM_trends_week();
    foreach ($data as $h => $v) {
    	$hours[] = $h;
    	$trend[] = ($v!="") ? $v : 0;
    }
    $max = count($hours);
    $siem_url = "../forensics/base_qry_main.php?clear_allcriteria=1&time_range=day&time[0][0]=+&time[0][1]=>%3D&time[0][2]=MM&time[0][3]=DD&time[0][4]=".date("Y",$timetz)."&time[0][5]=00&time[0][6]=00&time[0][7]=00&time[0][8]=+&time[0][9]=AND&time[1][0]=+&time[1][1]=<%3D&time[1][2]=MM&time[1][3]=DD&time[1][4]=".date("Y",$timetz)."&time[1][5]=23&time[1][6]=59&time[1][7]=59&time[1][8]=+&time[1][9]=+&submit=Query+DB&num_result_rows=-1&time_cnt=2&sort_order=time_d&hmenu=Forensics&smenu=Forensics";
} elseif (GET("type")=="hids") { 
    $js = "analytics";
    list($data,$plugins) = SIEM_trends_week("ossec%");
    foreach ($data as $h => $v) {
    	$hours[] = $h;
    	$trend[] = ($v!="") ? $v : 0;
    }
    $max = count($hours);
    $siem_url = "../forensics/base_qry_main.php?clear_allcriteria=1&time_range=day&time[0][0]=+&time[0][1]=>%3D&time[0][2]=MM&time[0][3]=DD&time[0][4]=".date("Y",$timetz)."&time[0][5]=00&time[0][6]=00&time[0][7]=00&time[0][8]=+&time[0][9]=AND&time[1][0]=+&time[1][1]=<%3D&time[1][2]=MM&time[1][3]=DD&time[1][4]=".date("Y",$timetz)."&time[1][5]=23&time[1][6]=59&time[1][7]=59&time[1][8]=+&time[1][9]=+&submit=Query+DB&num_result_rows=-1&time_cnt=2&sort_order=time_d&hmenu=Forensics&smenu=Forensics&plugin=".$plugins;
} else {
    $js = "analytics_duo";
    $data = SIEM_trends();
    $data2 = Logger_trends();
    for ($i=$max; $i>=0; $i--) {
    	$h = date("G",$timetz-(3600*$i))."h";
    	$hours[] = $h;
    	$trend[] = ($data[$h]!="") ? $data[$h] : 0;
    	$trend2[] = ($data2[$h]!="") ? $data2[$h] : 0;
    }
    $siem_url = "../forensics/base_qry_main.php?clear_allcriteria=1&time_range=day&time[0][0]=+&time[0][1]=>%3D&time[0][2]=".date("m",$timetz)."&time[0][3]=".date("d",$timetz)."&time[0][4]=".date("Y",$timetz)."&time[0][5]=HH&time[0][6]=00&time[0][7]=00&time[0][8]=+&time[0][9]=AND&time[1][0]=+&time[1][1]=<%3D&time[1][2]=".date("m",$timetz)."&time[1][3]=".date("d",$timetz)."&time[1][4]=".date("Y",$timetz)."&time[1][5]=HH&time[1][6]=59&time[1][7]=59&time[1][8]=+&time[1][9]=+&submit=Query+DB&num_result_rows=-1&time_cnt=2&sort_order=time_d&hmenu=Forensics&smenu=Forensics";
}
?>
<body scroll="no" style="overflow:hidden">		
	<table id="data" style="display:none">
        <tfoot>
            <tr>
            	<?	for ($i=0;$i<$max;$i++) {
            			$day = ($hours[$i]!="") ? $hours[$i] : "-";
            			echo "<th>$day</th>\n";
            		}
            	?>
            </tr>
        </tfoot>
        <tbody>
            <tr>
            	<?	for ($i=0;$i<$max;$i++) {
            			$value = ($trend[$i]!="") ? $trend[$i] : 0;
            			echo "<td>$value</td>\n"; 
            		}
            	?>
            </tr>
        </tbody>
    </table>
    <table id="data2" style="display:none">
        <tbody>
            <tr>
            	<?	for ($i=0;$i<$max;$i++) {
            			$value = ($trend2[$i]!="") ? $trend2[$i] : 0;
            			echo "<td>$value</td>\n"; 
            		}
            	?>
            </tr>
        </tbody>
    </table>
	
    <script language="javascript">
        logger_url = '../sem/index.php?start=<?=urlencode(date("Y-m-d",$timetz)." HH:00:00")?>&end=<?=urlencode(date("Y-m-d",$timetz)." HH:59:59")?>';
        siem_url = '<?=$siem_url?>';
    </script>
	<script src="../js/raphael/<?=$js?>.js"></script>
	<script src="../js/raphael/popup.js"></script>
    		
	<div id="holder" style='height:100%;width:100%;margin: auto;'></div>

</body>
</html>
