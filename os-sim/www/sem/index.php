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
require_once ("classes/Util.inc");
require_once ('classes/Session.inc');
require_once ('classes/Security.inc');
require_once ('classes/User_config.inc');
include("process.inc");
include("classes/Server.inc");
Session::logcheck("MenuEvents", "ControlPanelSEM");
require_once ('../graphs/charts.php');
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
// Open Source
require_once "ossim_conf.inc";
$conf = $GLOBALS["CONF"];
$version = $conf->get_conf("ossim_server_version", FALSE);
if (!preg_match("/pro|demo/i",$version)) {
	echo "<html><body><a href='http://alienvault.com/about/contact' target='_blank' title='Profesional SIEM'><img src='../pixmaps/sem_pro.png' border=0></a></body></tml>";
	exit;
}
$db_aux = new ossim_db();
$conn_aux = $db_aux->connect();

// Solera API
$_SESSION["_solera"] = ($conf->get_conf("solera_enable", FALSE)) ? true : false;

// Timezone correction
$tz = Util::get_timezone();
$timetz = gmdate("U")+(3600*$tz); // time to generate dates with timezone correction

$param_query = GET("query") ? GET("query") : "";
$current_search = urldecode(GET("current_search"));
$param_start = GET("start") ? GET("start") : gmdate("Y-m-d H:i:s", $timetz - (24 * 60 * 60));
$param_end = GET("end") ? GET("end") : gmdate("Y-m-d H:i:s", $timetz);

// Default GRAPH RANGE [day|last_month]";
$_SESSION['graph_type'] = "day";
$_SESSION['cat'] = gmdate("M j, Y");

$database_servers = Server::get_list($conn_aux,",server_role WHERE server.name=server_role.name AND server_role.sem=1");
list($logger_servers, $ip_to_name, $ip_list, $fcolors, $bcolors, $from_remote, $logger_colors) = get_logger_servers($conn_aux);

ossim_valid($param_query, OSS_TEXT, OSS_NULLABLE, OSS_BRACKET, 'illegal:' . _("query"));
ossim_valid($current_search, OSS_TEXT, OSS_NULLABLE, OSS_BRACKET, OSS_PUNC, '%', 'illegal:' . _("current_search"));
ossim_valid($param_start, OSS_DIGIT, OSS_COLON, OSS_SCORE, OSS_SPACE, OSS_NULLABLE, 'illegal:' . _("start date"));
ossim_valid($param_end, OSS_DIGIT, OSS_COLON, OSS_SCORE, OSS_SPACE, OSS_NULLABLE, 'illegal:' . _("end date"));
ossim_valid($num_servers, OSS_DIGIT, OSS_NULLABLE, 'illegal:' . _("num_servers"));
ossim_valid(GET('del_export'), OSS_DIGIT, OSS_ALPHA, OSS_NULLABLE, '=', 'illegal:' . _("del_export"));
if (ossim_error()) {
    die(ossim_error());
}

$config = parse_ini_file("everything.ini");

if($config["debug"]==1){
	if($config["debug_log"]==""){
		$config["debug_log"] = "/var/log/ossim/sem.log";
	}
	//$handle = fopen($config["debug_log"], "a+");
	//fputs($handle,"============================== INDEX.php ".date("Y-m-d H:i:s")." ==============================\n");
	//fclose($handle);
}

$uniqueid = uniqid(rand() , true);

// Filters
$uconfig = new User_config($conn_aux);
$_SESSION['logger_filters'] = $uconfig->get(Session::get_session_user(), 'logger_filters', 'php', "logger");
if ($_SESSION['logger_filters']['default'] == "") {
	$_SESSION['logger_filters']['default']['start_aaa'] = $param_start;
	$_SESSION['logger_filters']['default']['end_aaa'] = $param_end;
	$_SESSION['logger_filters']['default']['query'] = "";
	$uconfig->set(Session::get_session_user(), 'logger_filters', $_SESSION['logger_filters'], 'php', 'logger');
}

// Exports
$exports = array();
if (is_dir($config["searches_dir"])) {
	$find_str = $config["searches_dir"].Session::get_session_user();
	$cmd = "ls -t '$find_str'*/results.txt";
	$res = explode("\n",`$cmd`);
	foreach ($res as $line) if (preg_match("/$user\_(\d\d\d\d\-\d\d\-\d\d \d\d\:\d\d\:\d\d)\_(\d\d\d\d\-\d\d\-\d\d \d\d\:\d\d\:\d\d)\_(none|date|date\_desc)\_(.*)\/results\.txt/",$line,$found)) {
		$name = $found[1].$found[2].$found[3].$found[4];
		$filename = trim($line);
		if (GET('del_export') != "" && $name == base64_decode(GET('del_export')) && file_exists($filename)) {
			unlink($filename);
		} else {
			$exports[$filename] = array($found[1],$found[2],$found[3],$found[4]);
		}
	}
}
?>
<?php
$help_entries["help_tooltip"] = _("Click on this icon to active <i>contextual help</i> mode. Then move your mouse over items and you\'ll see how to use them.<br/>Click here again to disable that mode.");
$help_entries["search_box"] = _("This is the main searchbox. You can type in stuff and it will be searched inside the \'data\' field. Special keywords can be used to restrict search on specific fields:<br/><ul><li>sensor</li><li>src_ip</li><li>dst_ip</li><li>plugin_id</li><li>src_port</li><li>dst_port</li></ul><br/>Examples:<ul><li>plugin_id=4004 and root</li><li>plugin_id!=4004 and not root</li></ul>");
$help_entries["saved_searches"] = _("You can save queries using the Save button near the search box. Here you can recover them and/or delete them.");
$help_entries["close_all"] = _("This will close the graphs below as well as the cache status. Used for a quick <i>tidy up</i>.");
$help_entries["cache_status"] = _("Depending on the amount of time you query on and your log volume, cache can be grow rapidly. Use this to check the status and clean/delete as needed.");
$help_entries["graphs"] = _("Graphs will be recalculated based on the searchbox data, but take some time. Collapse this part for faster searching. You can add query criteria by clicking on various graph regions. Charst aren\'t drawn if the query results in more than 500000 events.");
$help_entries["result_box"] = _("This is the main result box. Each line is a log entry, and can be reordered based on date. You can click anywhere on the log lines to add the highlighted text to the search criteria.");
//$help_entries["clear"] = _("Use this to clear the search criteria.");
$help_entries["play"] = _("Submit your query for processing.");
$help_entries["date_ack"] = _("Acknowledge your date setting in order to recalculate the query.");
$help_entries["save_button"] = _("Use this button to save your current search for later re-use. Saved searches can be viewed by clicking on the saved searches drop-down in the upper left corner.");
$help_entries["date_frame"] = _("Choose between various pre-defined dates to query on. They will be recalculated each time the page is loaded.");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link rel="stylesheet" type="text/css" href="../style/style.css">
<link rel="stylesheet" type="text/css" href="../forensics/styles/ossim_style.css">
<link rel="stylesheet" type="text/css" href="../style/jquery.contextMenu.css" />
<link rel="stylesheet" type="text/css" href="../style/jquery.tagit.css" />
<link rel="stylesheet" type="text/css" href="../style/greybox.css"/>
<link rel="stylesheet" type="text/css" href="../style/datepicker.css"/>
<link rel="stylesheet" type="text/css" href="../style/jquery.autocomplete.css"/>

<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../js/jquery.contextMenu.js"></script>
<script type="text/javascript" src="../js/greybox_post.js"></script>
<!--[if IE]><script language="javascript" type="text/javascript" src="../js/excanvas.pack.js"></script><![endif]-->
<script type="text/javascript" src="../js/jquery-ui-1.8.core-and-interactions.min.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.autocomplete.min.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/tag-it.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery.flot.pie.js"></script>
<script type="text/javascript" src="../js/datepicker.js"></script>
<script type="text/javascript" src="../js/jquery.simpletip.js"></script>
<script type="text/javascript" src="../js/urlencode.js"></script>

<style type="text/css">
.etooltip { 
	text-align:left;
	position: absolute;
	padding: 5px;
	z-index: 10;

	color: #303030;
	background-color: #f5f5b5;
	border: 1px solid #FFE38F;

	font-family: arial;
	font-size: 12px;
	text-decoration: none !important;	
	width:650px; white-space:normal; 
}
</style>

<? include ("../host_report_menu.php") ?>

<script type="text/javascript">

var first_load = 1;
var byDateStart="";
var byDateEnd="";
var load_stop=false;
var old_query=true;

// ****************************************** ON LOAD ******************************************************
$(document).ready(function(){
	// INITIALIZE CALENDAR
	$("#start_aaa,#end_aaa").change(function(objEvent){
		CalendarOnChange();
	});
	<?php
	$y = strftime("%Y", $timetz - (24 * 60 * 60));
	$m = strftime("%m", $timetz - (24 * 60 * 60));
	$d = strftime("%d", $timetz - (24 * 60 * 60));
	?>
	byDateStart='<?php echo date("Y-m-d", $timetz - (24 * 60 * 60)) ?>';
	byDateEnd='<?php echo date("Y-m-d", $timetz) ?>';
	var datefrom = new Date(<?php echo $y ?>,<?php echo $m-1 ?>,<?php echo $d ?>);
	var dateto = new Date(<?php echo date("Y") ?>,<?php echo date("m")-1 ?>,<?php echo date("d") ?>);
    var dayswithevents = [ ];
	$('#widgetCalendar').DatePicker({
		flat: true,
		format: 'Y-m-d',
		date: [new Date(datefrom), new Date(dateto)],
		calendars: 3,
		mode: 'range',
		starts: 1,
		onChange: function(formated) {
			if (formated[0]!=formated[1]) {
				var f1 = formated[0].split(/-/);
				var f2 = formated[1].split(/-/);
				document.getElementById('start_aaa').value = f1[0]+'-'+f1[1]+'-'+f1[2]+' 00:00:00';
				document.getElementById('end_aaa').value = f2[0]+'-'+f2[1]+'-'+f2[2]+' 23:59:59';
                $("#widget>a").trigger('click');
				setFixed2();
			}
		}
	});
	var state = false;
	$('#widget>a').bind('click', function(){
		$('#widgetCalendar').stop().animate({height: state ? 0 : $('#widgetCalendar div.datepicker').get(0).offsetHeight}, 500);
		$('#imgcalendar').attr('src',state ? '../pixmaps/calendar.png' : '../pixmaps/tables/cross.png');
		state = !state;
		return false;
	});
	$('#widgetCalendar div.datepicker').css('position', 'absolute');
	$('#date2').addClass('negrita');
	bold_dates('date2');
	
	// INITIALIZE AUTOCOMPLETE SEARCH
	<?php 
	list($sensors, $hosts) = Host::get_ips_and_hostname($conn_aux);
	?>
	$("#mytags").tagit({
		autoFormat: true,
		changeFunction: function() { MakeRequest(); },
		availableTags: ["data"<?php foreach ($sensors as $ip=>$name) { ?>, "sensor:<?php echo $name?>"<?php } ?><?php $top = 0; foreach ($hosts as $ip=>$name) if ($top < 20) { ?>, "source:<?php echo $name?>", "destination:<?php echo $name ?>"<?php $top++; } ?>]
	});
	
	// LAUNCH LOGGER FETCH
	RequestLines();
	// From GET query
	<? if ($current_search != "") { ?>
	SetSearch("<?php echo $current_search ?>");
	<? } elseif (trim($_GET['query'])!="") { ?>
	SetSearch("src=<?php echo $_GET['query'] ?> OR dst=<?php echo $_GET['query'] ?>");
	// Default load
	<? } else { ?>
	MakeRequest();
	<? } ?>
	
});

// Change selected date option
function bold_dates(which_one){
	$('#date1td,#date2td,#date3td,#date4td,#date5td').css('background-color','white');
	$('#date1a,#date2a,#date3a,#date4a,#date5a').css('color','black');
	if (which_one) $('#'+which_one+"td").css('background-color','#28BC04');
	if (which_one) $('#'+which_one+"a").css('color','white');
}

// Handle clicks on graphs
function display_info ( var1, var2, var3, var4, var5, var6 ){
	hideGraphs();
	var combined = var6 + "=" + var4;
	SetSearch(combined);
	//hideLayer("by_date");
}

function is_operator (value) {
	return (value == "and" || value == "AND" || value == "or" || value == "OR") ? 1 : 0;
}

// Called by process.php when fetchall.pl is done
// Fill the results div with the logger response
function SetFromIframe(content,str,start,end,sort) {
	HandleResponse(content);
	$("#processcontent").show();
	document.getElementById('processframe').style.height = "30px";
    if(document.getElementById('txtexport').value!='noExport') {
        //$("#href_download").show();
        //$("#img_download").show();
        //$("#href_download").attr("href", "download.php?query=" + str + "&start=" + start + "&end=" + end + "&sort=" + sort);
    }
    if (document.getElementById('txtexport').value == "exportScreen") reload_export();
    document.getElementById('txtexport').value = 'noExport';
    load_contextmenu();
    $(".scriptinfo").simpletip({
            position: 'right',
            onBeforeShow: function() {
                    var ip = this.getParent().attr('ip');
                    this.load('../control_panel/alarm_netlookup.php?ip=' + ip);
            }
    });
    $(".scriptinfoimg").simpletip({
            offset: [0, -104],
            position: 'right',
            baseClass: 'imgtip',
            onBeforeShow: function() {
                    this.update(this.getParent().attr('txt'));
            }
    });
    $(".scriptinfotxt").simpletip({
            position: 'right',
            baseClass: 'ytooltip',
            onBeforeShow: function() {
                    this.update(this.getParent().attr('txt'));
            }
    }); 
    $(".eventinfo").simpletip({
            position: 'bottom',
            offset: [300,5],
            baseClass: 'etooltip',
            onBeforeShow: function() {
                    this.update(this.getParent().attr('txt'));
            }
    }); 
}

function GetSearchString() {
	var str = "";
	var prev_atom = "";
    $('.search_atom').each(function(){
		var cur_atom = this.value;
		cur_atom = cur_atom.replace(" = ","=");
		cur_atom = cur_atom.replace(" != ","!=");
		cur_atom = cur_atom.replace(/ /g,"SPACESCAPE");
		if (!is_operator(cur_atom) && !cur_atom.match(/\=/)) {
			cur_atom = "data="+cur_atom;
		}
		/* first atom */
		if (prev_atom == "") {
			str = cur_atom;
		}
		/* default operator AND if not specified */
		else if (prev_atom != "" && !is_operator(prev_atom) && !is_operator(cur_atom)) {
			str += " AND "+cur_atom;
		}
		/* other case */
		else {
			str += " "+cur_atom;
		}
    	prev_atom = cur_atom;
    });
    str = escape(str);
    return str;
}

function MakeRequest()
{
    if (load_stop) {
    	if ($.browser.msie) document.execCommand('Stop'); 
    	else window.stop();
	}
	$('input.tagit-input').removeClass('ui-autocomplete-loading');
    if(document.getElementById('txtexport').value=='noExport') {
        //$("#href_download").hide();
        //$("#img_download").hide();
    }
	// Used for main query
	//document.getElementById('loading').style.display = "block";
        //
    //document.getElementById('ResponseDiv').innerHTML = '<img align="middle" style="vertical-align: middle;" src="../pixmaps/sem/loading.gif"> <?php echo _('Loading events...'); ?>';

    var str = GetSearchString();
    //alert(str);

	<? if (GET('query') != "")  { ?>
	var str = "src=<?php echo GET('query')?> OR dst=<?php echo GET('query') ?>";
	<? } ?>
	var offset = parseInt(document.getElementById('offset').value);
	var start = escape(document.getElementById('start').value);
	var end = escape(document.getElementById('end').value);
	var sort = escape(document.getElementById('sort').value);
	var top = escape(document.getElementById('top').value);

    var txtexport = document.getElementById('txtexport').value;
    var tzone =  document.getElementById('tzone').value;
    
    document.getElementById('ResponseDiv').innerHTML = "";
	document.getElementById('processframe').src = "process.php?query=" + str + "&offset=" + offset + "&top=" + top + "&start=" + start + "&end=" + end + "&sort=" + sort + "&tzone=" + tzone + "&uniqueid=<?php echo $uniqueid ?><?=(($config["debug"]==1) ? "&debug_log=".urlencode($config["debug_log"]) : "")?>&txtexport="+txtexport+"&old_query="+old_query;
	
	return false;
}

function resize_iframe() {
	document.getElementById('processframe').style.height = "400px";
}

function RequestLines()
{
	// Used for main query
	document.getElementById('loading').style.display = "block";
	var start = escape(document.getElementById('start').value);
	var end = escape(document.getElementById('end').value);
	var url = "wcl.php?ips=<?php echo $ip_list ?>&start=" + start + "&end=" + end + "&uniqueid=<?php echo $uniqueid?><?=(($config["debug"]==1) ? "&debug_log=".urlencode($config["debug_log"]) : "")?>";
	load_stop = false;
	//alert(url);
	$.ajax({
		type: "GET",
		url: url,
		data: "",
		success: function(msg) {
			document.getElementById('loading').style.display = "none";
			document.getElementById('numlines').innerHTML = msg;
			load_stop = true;
		}
	});
}

function KillProcess()
{
	load_stop = false;
	$.ajax({
		type: "GET",
		url: "killprocess.php?uniqueid=<?php echo $uniqueid ?>&ips=<?php echo $ip_list ?>",
		data: "",
		success: function(msg) {
			//alert("Processes stoped!");
            document.getElementById('processframe').src = "process.php?txtexport=stop";
            load_stop = true;
		}
	});
}

function HandleQuery(response){
// Print query listing
	document.getElementById('saved_searches').innerHTML = response;
}

function MakeRequest2(query, action)
{
// Used for query saving
	load_stop = false;
	$.ajax({
		type: "GET",
		url: "manage_querys.php?query=" + urlencode(query) + "&action=" + action,
		data: "",
		success: function(msg) {
			HandleQuery(msg);
			load_stop = true;
		}
	});
}

function DeleteQuery(query){
// delete saved query from list
	MakeRequest2(query,"delete");
}

function AddQuery(){
// Add saved query to list
	var query = escape(document.getElementById('searchbox').value);
	MakeRequest2(query,"add");
}

var graphs_toggled = false;
function getGraphs() {
	document.getElementById('test').style.display = "inline";
	//document.getElementById('test').innerHTML = '<img align="middle" style="vertical-align: middle;" src="../pixmaps/sem/loading.gif"> <?php echo _('Loading Stats, please a wait a few seconds...') ?>';
	/*$.ajax({
		type: "GET",
		url: "pies.php?uniqueid="+Math.floor(Math.random()*101),
		data: "",
		success: function(msg) {
			document.getElementById('test').innerHTML = msg;
			document.getElementById('graphs_link').href = "javascript:hideGraphs();";
			graphs_toggled = true;
			load_pies();
		}
	});*/
	document.getElementById('graphs_link').href = "javascript:hideGraphs();";
	graphs_toggled = true;
	//document.getElementById('test').innerHTML = '<div id="testLoading"><img align="middle" style="vertical-align: middle;" src="../pixmaps/sem/loading.gif"> <?php echo _('Loading Stats, please a wait a few seconds...') ?></div>'+"<iframe src='pies.php?ips=<?php echo $ip_list ?>&uniqueid=<?php echo $uniqueid ?>' style='width:100%;height:460px;border: 1px solid rgb(170, 170, 170);overflow:hidden' frameborder='0'></iframe>";
	document.getElementById('test').innerHTML = "<iframe src='pies.php?ips=<?php echo $ip_list ?>&uniqueid=<?php echo $uniqueid ?>' style='width:100%;height:460px;border: 1px solid rgb(170, 170, 170);overflow:hidden' frameborder='0'></iframe>";
}
function hideGraphs() {
	document.getElementById('graphs_link').href = "javascript:getGraphs();";
	document.getElementById('test').style.display = "none";
	graphs_toggled = false;
}

function toggleLayer( whichLayer )
{
  var elem, vis;
  if( document.getElementById ) // this is the way the standards work
    elem = document.getElementById( whichLayer );
  else if( document.all ) // this is the way old msie versions work
      elem = document.all[whichLayer];
  else if( document.layers ) // this is the way nn4 works
    elem = document.layers[whichLayer];
  vis = elem.style;
  // if the style.display value is blank we try to figure it out here
  if(vis.display==''&&elem.offsetWidth!=undefined&&elem.offsetHeight!=undefined)
    vis.display = (elem.offsetWidth!=0&&elem.offsetHeight!=0)?'block':'none';
  vis.display = (vis.display==''||vis.display=='block')?'none':'block';
}

function hideLayer( whichLayer )
{
  var elem, vis;
  if( document.getElementById ) // this is the way the standards work
    elem = document.getElementById( whichLayer );
  else if( document.all ) // this is the way old msie versions work
      elem = document.all[whichLayer];
  else if( document.layers ) // this is the way nn4 works
    elem = document.layers[whichLayer];
  vis = elem.style;
  // if the style.display value is blank we try to figure it out here
  vis.display = 'none';
}


function closeLayer( whichLayer )
{
  var elem, vis;
  if( document.getElementById ) // this is the way the standards work
    elem = document.getElementById( whichLayer );
  else if( document.all ) // this is the way old msie versions work
      elem = document.all[whichLayer];
  else if( document.layers ) // this is the way nn4 works
    elem = document.layers[whichLayer];
  vis = elem.style;
  // if the style.display value is blank we try to figure it out here
  vis.display = 'none';
}

function SubmitClick(cmd) {
	old_query = (cmd=='ignore') ? true : false;
	$('#offset').val('0');
	if (typeof($('.tagit-input')).val() != 'undefined' && $('.tagit-input').val() != ''){
		AddAtom($('.tagit-input').val());$('.tagit-input').val('');
	}
	doQuery('noExport');
}

function IsNew (value){
	value = value.replace(/\<b\>/g,"");
	value = value.replace(/\<\/b\>/g,"");
	if (!value.match(/\=/) && !is_operator(value)) value = "data="+value;
	var is_new = true;
	$('#mytags').children(".tagit-choice").each(function(i){
		n = $(this).children("input").val();
		if (value == n) {
			is_new = false;
		}
	})
	return is_new;
}

function AddAtom(value) {
	if (IsNew(value)) {
	if (!value.match(/\=/) && !is_operator(value)) value = "<b>data</b>="+value;
	if (value.match(/\=/) && !value.match(/\<b\>/)) {
		value = "<b>"+value;
		if (value.match("!=")) {
			value = value.replace("!=","</b>!=");
		} else {
			value = value.replace("=","</b>=");
		}
	}
	var el = "";
	el  = "<li class=\"tagit-choice\">\n";
	el += value + "\n";
	el += "<a class=\"close\">x</a>\n";
	value = value.replace(/\<b\>/g,"");
	value = value.replace(/\<\/b\>/g,"");
	el += "<input type=\"hidden\" style=\"display:none;\" class=\"search_atom\" value=\""+value+"\" name=\"item[tags][]\">\n";
	el += "</li>\n";
	$(el).insertBefore ($('#mytags').children(".tagit-new"));
	}
}

function SetSearch(content)
{
	var atoms = new Array;
	if (content.match(/ /)) {
		atoms = content.split(" "); 
	} else {
		atoms[0] = content;
	}
	for (i = 0; i < atoms.length; i++) {
		var value = atoms[i];
		value = value.replace(/^\s+/g,'').replace(/\s+$/g,'').replace(/\n/g,'');
		value = value.replace(/SPACESCAPE/g,' ');
		if (value != "" && value != "data=" && value != "and" && value != "AND") {
			AddAtom(value);
		}
	}
	window.scrollTo(0,0);
	//$('#tip_msg').show();
	//setTimeout("$('#tip_msg').fadeOut('slow');",2000);
	document.getElementById('offset').value = '0';
	MakeRequest();
}

function ReplaceSearch(content)
{
// Replace search bar, perform search
  document.getElementById('searchbox').value = content;
  MakeRequest();
}

function ClearSearch()
{
// Clear search bar, perform search
  document.getElementById('searchbox').value = "";
  document.getElementById('offset').value = "0";
  document.getElementById('sort').value = "none";
  MakeRequest();
}

function IncreaseOffset(amount)
{
// Pagination
  var offset = parseInt(document.getElementById('offset').value);
  document.getElementById('offset').value = offset + amount;
  MakeRequest();
}

function DateAsc()
{
// Sorting
  document.getElementById('sort').value = "date";
  MakeRequest();
}

function DateDesc()
{
// Sorting
  document.getElementById('sort').value = "date_desc";
  MakeRequest();
}


function DecreaseOffset(amount)
{
// Pagination
	var offset = parseInt(document.getElementById('offset').value);
	document.getElementById('offset').value = offset - amount;
	MakeRequest();
}

function setFixed(start, end, gtype, datef)
{
// Gets fixed time ranges from day, month, etc... buttons
	document.getElementById('start').value = start;
	document.getElementById('start_aaa').value = start;
	document.getElementById('end').value = end;
	document.getElementById('end_aaa').value = end;
	document.getElementById('tzone').value = "<?=$tz?>";
      
	if (gtype != '' && datef != '') {
		UpdateByDate("forensic.php?graph_type="+gtype+"&cat="+datef);
	}
	
	$('#widgetCalendar').DatePickerClear();
	var date_arr = new Array; date_arr[0] = start; date_arr[1] = end;
	$('#widgetCalendar').DatePickerSetDate(date_arr, 0);
	RequestLines();
	MakeRequest();
}

function setFixed2()
{
// Gets fixed time ranges from calendar popups
// If not entered manually hour information will be missing so..
	var start_pad = "";
	var end_pad = "";
	if(document.getElementById('start_aaa').value.length == 10){
		var start_pad = " 00:00:00";
	}
	if(document.getElementById('end_aaa').value.length == 10){
		var end_pad = " 23:59:59";
	}

	document.getElementById('start').value = document.getElementById('start_aaa').value + start_pad;
	document.getElementById('end').value = document.getElementById('end_aaa').value + end_pad;
	document.getElementById('tzone').value = "<?=$tz?>";
	RequestLines();
	MakeRequest();
}


function HandleResponse(response)
{
// Main response handler for event lines
	document.getElementById('ResponseDiv').innerHTML = response;
	if(first_load == 1){
		first_load = 0;
	} else {
		if (graphs_toggled) getGraphs();
	}
}

function HandleCacheResponse(response)
{
// Handle Gauge and cache information
  var responses = response.split(":");
  if(responses[0] == "pct"){
    gauge.needle.setValue(responses[1]);
  } else {
  document.getElementById('gauge_text').innerHTML = response;
  }
}

function showTip(text, color, width){
	if(document.body.style.cursor == 'help'){
		ddrivetip(text,color,width);
	}
}

function hideTip(){
	if(document.body.style.cursor == 'help'){
		hideddrivetip();
	}
}

function toggleCursor(){
	if(document.body.style.cursor == 'help'){
		document.body.style.cursor = document.getElementById('cursor').value;} else {
			document.body.style.cursor = "help";
		}
}

function HandleStatsByDate(response)
{
	//document.getElementById('by_date').innerHTML=response.replace(/so.write\([^\)]+\)/,'so.write("by_date")');
	var cont=document.getElementById('by_date').innerHTML;
	document.getElementById('by_date').innerHTML="";
	document.getElementById('by_date').innerHTML=cont;
  	if(first_load != 1)
	{
		hideLayer("test");
	}
}

function UpdateByDate(urlres)
{
	load_stop = false;
	$.ajax({
		type: "GET",
		url: urlres,
		data: "",
		async: false,
		success: function(msg) {
			HandleStatsByDate(msg);
			load_stop = true;
		}
	});
}


function graph_by_date( col ,row ,value, category, series, t_year, t_month, t_day)
{
    var urlres = "forensic.php";
    var month;
    var year;
    var day;
    var hour;
	//alert(col+','+row+','+value+','+category+','+series+','+t_year+','+t_month);
    document.getElementById('searchbox').value = "";
    document.getElementById('offset').value = "0";
    document.getElementById('sort').value = "none";
    document.getElementById('tzone').value = "0";
  switch(row)
  {
    case 1:
      urlres = urlres+ "?graph_type=year&cat=" + category;

      year=category.replace(/^ *| *$/g,"");
      byDateStart=year+"-01-01";
      byDateEnd=year+"-12-31";
      document.getElementById('start').value = byDateStart+" 00:00:00";
      document.getElementById('start_aaa').value = byDateStart+" 00:00:00";
      document.getElementById('end').value = byDateEnd+ " 23:59:59";
      document.getElementById('end_aaa').value = byDateEnd+" 23:59:59";
      RequestLines(); MakeRequest();
      bold_dates();
    break;
    case 2:
      urlres = urlres + "?graph_type=month&cat=" + category;

      month=monthToNumber(category.replace(/,.*$/,""));
      year=category.replace(/^.*, /,"");
      byDateStart=year+"-"+month+"-01";
      lastmonthday = new Date((new Date(year, month, 1))-1).getDate();
      byDateEnd=year+"-"+month+"-"+lastmonthday;
      document.getElementById('start').value = byDateStart+" 00:00:00";
      document.getElementById('start_aaa').value = byDateStart+" 00:00:00";
      document.getElementById('end').value = byDateEnd+ " 23:59:59";
      document.getElementById('end_aaa').value = byDateEnd+" 23:59:59";
      RequestLines(); MakeRequest();
      bold_dates();
    break;
    case 3:
      urlres = urlres + "?graph_type=day&cat=" + category;

      month=monthToNumber(category.replace(/ .*$/,""));
      year=category.replace(/^.*, /,"");
      day=category.replace(/^[^ ]+ /,"");
      day=day.replace(/,.*$/,"");
      if(day.length==1)
      	day="0"+day;
      byDateStart=year+"-"+month+"-"+day;
      byDateEnd=year+"-"+month+"-"+day;
      document.getElementById('start').value = byDateStart+" 00:00:00";
      document.getElementById('start_aaa').value = byDateStart+" 00:00:00";
      document.getElementById('end').value = byDateEnd+ " 23:59:59";
      document.getElementById('end_aaa').value = byDateEnd+" 23:59:59";
      RequestLines(); MakeRequest();
      bold_dates();
      //alert("day: "+ day +" month: "+month+ " year: "+year+" cat: "+category);
    break;
    default:
      //Dont create another graph... refresh the search and stop here
      hour=category.replace(/[^\d]+/,"");
      hour=hour.replace(/[^\d]+/,"");
      from_day = (t_day!="" && typeof(t_day)!="undefined") ? t_year+"-"+t_month+"-"+t_day : byDateStart;
      to_day = (t_day!="" && typeof(t_day)!="undefined") ? t_year+"-"+t_month+"-"+t_day : byDateEnd;      
      document.getElementById('start_aaa').value = document.getElementById('start').value = from_day+" "+hour+":00:00";
      document.getElementById('end_aaa').value = document.getElementById('end').value = to_day+ " "+hour+":59:59";
      RequestLines(); 
      MakeRequest();
      bold_dates();
      document.getElementById('start_aaa').value = document.getElementById('start').value = from_day+" 00:00:00";
      document.getElementById('end_aaa').value = document.getElementById('end').value = to_day+ " 23:59:59";
      return;
    break;
  }
  UpdateByDate(urlres);
}
function monthToNumber(m)
{
	m=m.toLowerCase();
	switch(m)
	{
		case "jan":
			return "01";
			break;
		case "feb":
			return "02";
			break;
		case "mar":
			return "03";
			break;
		case "apr":
			return "04";
			break;
		case "may":
			return "05";
			break;
		case "jun":
			return "06";
			break;
		case "jul":
			return "07";
			break;
		case "aug":
			return "08";
			break;
		case "sep":
			return "09";
			break;
		case "oct":
			return "10";
			break;
		case "nov":
			return "11";
			break;
		case "dec":
			return "12";
			break;
		default:
			return 0;
			break;
	}
}

function SubmitForm() { /*document.forms[0].submit();*/ MakeRequest(); }

function EnterSubmitForm(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  if (evt.keyCode == 13) SubmitForm();
} 

var taskID = "";
var intv = 1;
function bg_task() {
	intv = intv * (-1);
	load_stop = false;
	$.ajax({
        type: "GET",
        url: "ajax_exports.php?action=ps",
        data: "",
        success: function(msg) {
        	load_stop = true;
        	if (msg == "") {
        		document.getElementById('exports_bg').innerHTML = "";
        		clearInterval(taskID);
        		reload_export();
        	} else {
        		document.getElementById('exports_bg').innerHTML = "<img src='../forensics/images/alarm-clock-blue.png'> <?php echo _("Saving events in background") ?> "+msg;
        	}
        }
	});
}

function doQuery(tipoExport) {
	if (tipoExport == 'exportScreen') {
		load_stop = false;
		document.getElementById('exports_box').innerHTML = "<img src='../pixmaps/loading.gif' width='16'>";
	} else if (tipoExport == 'exportEntireQuery') {
		document.getElementById('exports_bg').innerHTML = "<img src='../forensics/images/alarm-clock-blue.png'> <?php echo _("Saving events in background") ?>";
		if (taskID != "") clearInterval(taskID);
		taskID = setInterval("bg_task()",5000);
	}
  //hideLayer("by_date");
  document.getElementById('txtexport').value=tipoExport;
  document.getElementById('tzone').value = "<?=$tz?>";
  SubmitForm();
}

function CalendarOnChange() {
	bold_dates('');
	setFixed2();
}
Array.prototype.in_array = function(p_val) {
    for(var i = 0, l = this.length; i < l; i++) {
        if(this[i] == p_val) {
            return true;
        }
    }
    return false;
}

function change_calendar() {
	var n = 0;
	var date_arr = new Array; date_arr[0] = document.getElementById('start_aaa').value; date_arr[1] = document.getElementById('end_aaa').value;
	
	$('#widgetCalendar').DatePickerSetDate(date_arr, 0);
}

function save_filter(filter_name) {
	//var filter_name = document.getElementById('filter').value;
	var start = document.getElementById('start').value;
	var end = document.getElementById('end').value;
	var query = GetSearchString();
	
	document.getElementById('filter_msg').innerHTML = '<img align="middle" style="vertical-align: middle;" src="../pixmaps/sem/loading.gif">';
	$.ajax({
		type: "GET",
		url: "ajax_filters.php?mode=new&filter_name="+filter_name+"&start="+start+"&end="+end+"&query="+query,
		data: "",
		success: function(msg) {
			document.getElementById('filter_msg').innerHTML = "";
		}
	});
}
function new_filter() {
	var filter_name = document.getElementById('filter_name').value;
	var start = document.getElementById('start').value;
	var end = document.getElementById('end').value;
	var query = GetSearchString();
	
	if (filter_name == "") alert("<?=_("You must type a name for the new filter.")?>");
	else {
		document.getElementById('filter_msg').innerHTML = '<img align="middle" style="vertical-align: middle;" src="../pixmaps/sem/loading.gif">';
		$.ajax({
			type: "GET",
			url: "ajax_filters.php?mode=new&filter_name="+filter_name+"&start="+start+"&end="+end+"&query="+query,
			data: "",
			success: function(msg) {
				document.getElementById('filter_box').innerHTML = msg;
				document.getElementById('filter_msg').innerHTML = "";
			}
		});
	}
}
function change_filter(filter_name) {
	document.getElementById('filter_msg').innerHTML = '<img align="middle" style="vertical-align: middle;" src="../pixmaps/sem/loading.gif">';
	$.ajax({
		type: "GET",
		url: "ajax_filters.php?mode=load&filter_name="+filter_name,
		data: "",
		success: function(msg) {
			var filter_data = msg.split(/\#\#/);
			if (filter_data[0] != "" && filter_data[1] != "") {
				document.getElementById('start_aaa').value = filter_data[1];
				document.getElementById('end_aaa').value = filter_data[2];
				$('#mytags').children(".tagit-choice").remove();
				if (filter_data[3] != "") SetSearch(filter_data[3].replace(/and/i, ""));
                document.getElementById('filter_box').innerHTML = msg;
				setFixed2();
			}
			else alert("Error: "+msg);
			document.getElementById('filter_msg').innerHTML = "";
		}
	});
}
function delete_filter(filter_name) {
	//var filter_name = document.getElementById('filter').value;

	if (filter_name == "" || filter_name == "default") alert("<?=_("You can not delete this filter.")?>");
	else {
            if(confirm('<?php echo _("Are you secure?")?>')){
                    document.getElementById('filter_msg').innerHTML = '<img align="middle" style="vertical-align: middle;" src="../pixmaps/sem/loading.gif">';
                    $.ajax({
                            type: "GET",
                            url: "ajax_filters.php?mode=delete&filter_name="+filter_name,
                            data: "",
                            success: function(msg) {
                                    document.getElementById('filter_box').innerHTML = msg;
                                    document.getElementById('filter_msg').innerHTML = "";
                            }
                    });
                }else{
                    return false;
                }
	}
}
function delete_export(code) {
	document.getElementById('exports_box').innerHTML = "<img src='../pixmaps/loading.gif' width='16'>";
	$.ajax({
        type: "GET",
        url: "ajax_exports.php?del_export="+code,
        data: "",
        success: function(msg) {
        	document.getElementById('exports_box').innerHTML = msg;
        }
	});
}
function reload_export() {
	$.ajax({
        type: "GET",
        url: "ajax_exports.php",
        data: "",
        success: function(msg) {
        	document.getElementById('exports_box').innerHTML = msg;
        }
	});
}
function save_current_search() {
	document.serverform.current_search.value = GetSearchString();
}
</script>
</head>
<body>
<ul id="myMenu" class="contextMenu">
<li class="report"><a href="#edit"><?=_("Host Report")?></a></li>
</ul>
<?php
include ("../hmenu.php"); ?>

<table class="transparent" border=0 cellpadding=0 cellspacing=0 align="right">
<?
if (count($database_servers)>0 && Session::menu_perms("MenuConfiguration", "PolicyServers")) { 
	// session server
	?>
	<form name="serverform">
	<tr>
		<td class="left nobborder" style="padding-right:10px">
		<a style='cursor:pointer; font-weight:bold;color:#222222' class='ndc' onclick="$('#rservers').toggle()"><img src="../pixmaps/arrow_green.gif" align="absmiddle" border="0"/><?php echo _("Remote Servers")?></a>
			<div style="position:relative; z-index:1">
			<div id="rservers" style="position:absolute;right:0;top:0;display:none;border:1px solid gray;background-color:#EEEEEE">
				<table class="transparent" border=0 cellpadding=1 cellspacing=2 width="100%">
				<?php
				$i = 0;
				foreach ($database_servers as $db) {
					if ($i >= count($bcolors)) $i = 0;
					$name = $db->get_name();
					$_SESSION['logger_colors'][$name]['bcolor'] = $bcolors[$i];
					$_SESSION['logger_colors'][$name]['fcolor'] = $fcolors[$i];
					?>
					<tr bgcolor='#EEEEEE'>
						<td class="nobborder"><input type="checkbox" id="check_<?php echo $name ?>" onclick="save_current_search();document.serverform.submit()" name="<?php echo $name ?>" value="<?php echo $name ?>" <?php if ($logger_servers[$name]) { echo "checked"; } if ($logger_error[$name]) { echo " disabled"; } ?>></input></td>
						<td class="nobborder"></td>
						<td class="nobborder"><table class="transparent"><tr><td class="nobborder" style="padding-left:5px;padding-right:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;border:0px;background-color:<?php echo '#'.$bcolors[$i]?>;color:<?php echo '#'.$fcolors[$i]?>"><?php echo $name ?></td></tr></table></td>
					</tr>
				<?php $i++; } ?>
				</table>
			</div>
			</div>
		</td>
	</tr>
	<input type="hidden" name="num_servers" value="<?php echo $i ?>">
	<input type="hidden" name="current_search" value="">
	</form>
<?php
}
?>
</table>

<a href="javascript:toggleLayer('by_date');"><img src="<?php echo $config["toggle_graph"]; ?>" border="0" align="absmiddle" title="<?=_("Toggle Graph by date")?>"> <font style="color:black;font-family:arial"><?=_("Graphs by UTC dates")?></font></a>
<center style="margin:0px">
<div id="by_date">
    <div id="testLoading2"><img align="middle" style="vertical-align: middle;" src="../pixmaps/sem/loading.gif"> <?php echo _('Loading Graphs, please a wait a few seconds...') ?></div>
    <a href="javascript:UpdateByDate('forensic.php?graph_type=all&cat=&uniqueID=<?php echo $uniqueid ?>');" style="color:black"><?=_("Click to show the main chart")?></a>
    <IFRAME src="forensic_source.php?ips=<?php echo $ip_list ?>" frameborder="0" style="margin-top:0px;width:100%;height:180px;overflow:hidden"></IFRAME>
</div>
</center>
<!--
<div id="help" style="position:absolute; right:30px; top:5px";>
<img src="<?php echo $config["help_graph"] ?>" border="0" onMouseover="ddrivetip('<?php echo $help_entries["help_tooltip"]; ?>','lighblue', 300)" onMouseout="hideddrivetip()" onClick="toggleCursor()">
</div>-->
<!-- Misc internal vars -->
<form id="search" action="javascript:MakeRequest();">
<input type="hidden" id="searchbox" style="font-size: 1.5em;margin: 0.5em;"></input>
<input type="hidden" name="cursor" id="cursor" value="">
<script>
document.getElementById('cursor').value = document.body.style.cursor;
</script>
<input type="hidden" id="offset" value="0">
<?php // Possible sort values: none, date, date_desc
 ?>
<input type="hidden" id="sort" value="none">
<input type="hidden" id="start" value="<?php echo ($param_start != "" && date_parse($param_start)) ? $param_start : gmdate("Y-m-d H:i:s", $timetz - (24 * 60 * 60)) ?>">
<?php // Temporary fix until the server logs right
 ?>
<input type="hidden" id="end" value="<?php echo ($param_end != "" && date_parse($param_end)) ? $param_end : gmdate("Y-m-d H:i:s", $timetz) ?>">
<!--
<div id="compress">
<center><a href="javascript:closeLayer('entiregauge');closeLayer('test');closeLayer('compress');" onMouseOver="showTip('<?php echo $help_entries["close_all"] ?>','lightblue','300')" onMouseOut="hideTip()"><font color="black"><?php echo _("Click here in order to compress everything") ?></a></center>
</div>
-->
<div id="saved_searches" style="display:none; position:absolute; background-color:#FFFFFF">
<?php
require_once ("manage_querys.php");
?>
</div>
<table class="transparent" cellspacing="0" width="100%" border="0" id="maintable">
<tr>
<td nowrap class="nobborder">
	<table cellspacing="0" width="100%" id="searchtable" style="border: 1px solid rgb(170, 170, 170);border-radius: 0px; -moz-border-radius: 0px; -webkit-border-radius: 0px;background:url(../pixmaps/fondo_hdr2.png) repeat-x">
		<tr><td class="nobborder" align="center" style="text-align:center;padding-top:20px">
			<table class="transparent" align="center">
				<tr>
					<td class="nobborder" style="font-size:18px;font-weight:bold;color:#222222"><?=_("Search")?>:</td>
					<td class="nobborder">
						<div style="clear:both">
						<ul id="mytags" style="list-style: none"></ul>
						</div>
					</td>
					<td class="nobborder">
					
					<? if (file_exists($config["searcher"])) { ?>
					<input type="button" class="button" onclick="SubmitClick('')" value="<?php echo _("Indexed Query") ?>" style="font-weight:bold;height:30px">&nbsp;<input type="button" class="button" onclick="SubmitClick('ignore')" value="<?php echo _("Raw Query") ?>" style="font-weight:bold;height:30px">
					<? } else { ?>
					<input type="button" class="button" onclick="SubmitClick('ignore')" value="<?php echo _("Submit Query") ?>" style="font-weight:bold;height:30px">
					<? } ?>
					
					</td>
					<td class="nobborder" style="padding:10px">
                        <input type="hidden" name="txtexport" id="txtexport" value="noExport" />
                        <a href="javascript:;" onclick="$('#exports').toggle()" style="color:black"><img src="../pixmaps/arrow_green.gif" align="absmiddle" border="0"> <b><?php echo _("Exports")?></b></a>
                        <div style="position:relative">
                        <div id="exports" style="position:absolute;left:0;top:0;display:none;z-index:1000">
                        <table cellpadding=0 cellspacing=0 align="center" style="padding:2px">
	                        <tr>
	                        	<th style="padding:0px">
	                        		<table style="background-color:transparent;border:0px" width="100%">
	                        			<tr>
	                        				<td width="30"></td>
	                        				<td align="center"><?php echo _("Saved exports") ?></td>
	                        				<td align="right" width="30"><a style="margin: 0 0 0 5px" href="javascript:;" onclick="$('#exports').toggle()"><img src="../pixmaps/cross-circle-frame.png" alt="<?php echo _("Close"); ?>" title="<?php echo _("Close"); ?>" border="0" /></a></td>
	                        			</tr>
	                        		</table>
	                        	</th>
	                        </tr>
	                        <tr>
	                        	<td class="nobborder" style="padding:10px" align="center">
	                        	<a onclick="doQuery('exportScreen')" href="#" alt="<?php echo _("Export screen")?>"><img src="../pixmaps/exportScreen.png" border="0" title="<?php echo _("Export screen")?>" alt="<?php echo _("Export screen")?>" /> <?php echo _("Screen export") ?></a>
	                        	&nbsp;<a onclick="doQuery('exportEntireQuery')" href="#" alt="<?php echo _("Export entire query")?>"><img src="../pixmaps/exportQuery.png" border="0" title="<?php echo _("Export entire query")?>" alt="<?php echo _("Export entire query")?>" /> <?php echo _("Entire export") ?></a>
	                        	</td>
	                        </tr>
	                        <tr class="nobborder">
		                        <td class="nobborder" id="exports_box">
			                        <?php if (count($exports) < 1) { ?>
			                        <i><?php echo _("No export files found") ?>.</i>
			                        <?php } else { ?>
			                        <table style="border:0px">
										<tr>
											<th><?php echo _("From") ?></th>
											<th><?php echo _("To") ?></th>
											<th><?php echo _("Query") ?></th>
											<th><?php echo _("Size") ?></th>
											<td align="right"><a href="" onclick="if(confirm('<?php echo _("Are you sure?") ?>')) delete_export('all');return false;"><img src="../vulnmeter/images/delete.gif" alt="<?php echo _("Delete all"); ?>" title="<?php echo _("Delete all"); ?>" border="0"></img></a></td>
										</tr>
									<? $i=0;
									foreach ($exports as $filename=>$name) {
				                        $size = (filesize($filename)/1024 > 2000) ? floor(filesize($filename)/1024/1024)."MB" : floor(filesize($filename)/1024)."KB";
										$i++;    ?>
				                        <tr class="<?php if($i%2==0){ echo 'impar'; }else{ echo 'par'; } ?>" style="padding-top:4px">
				                        <td><b><?php echo $name[0] ?></b></td>
				                        <td><b><?php echo $name[1] ?></b></td>
				                        <td><b><?php echo ($name[3] != "") ? "yes" : "no" ?></b></td>
				                        <td><?php echo $size ?></td>
				                        <td>
				                        <a href="download.php?query=<?php echo $name[3] ?>&start=<?php echo $name[0] ?>&end=<?php echo $name[1] ?>&sort=<?php echo $name[2] ?>"><img src="../pixmaps/download.png" alt="<?php echo _("Download"); ?>" title="<?php echo _("Download"); ?>" border="0" /></a>
				                        <a href="" onclick="if(confirm('<?php echo _("Are you sure?") ?>')) delete_export('<?php echo base64_encode($name[0].$name[1].$name[2].$name[3]) ?>');return false;"><img src="../vulnmeter/images/delete.gif" alt="<?php echo _("Delete"); ?>" title="<?php echo _("Delete"); ?>" border="0" /></a>
				                        </td>
				                        </tr>
				                        <? } ?>
			                        </table>
			                        <?php } ?>
		                        </td>
	                        </tr>
	                        <tr><td class="nobborder" id="exports_bg"></td></tr>
                        </table>
                        </div>
                        </div>
					</td>
					<!-- 
					<td class="nobborder">
                    	<a href="#" id="href_download" style="display:none;"><img align="absmiddle" title="<?=_("Download")?>" alt="<?=_("Download")?>" style="display:none;" id="img_download" src="../pixmaps/download.png" border="0" width="15"></a>
                    </td>
                     -->
                    <td class="nobborder">
                    	<a href="javascript:;" onclick="$('#searches').toggle()" style="color:black"><img src="../pixmaps/arrow_green.gif" align="absmiddle" border="0"> <b><?php echo _("Predefined Searches")?></b></a>
                        <div style="position:relative">
                        <div id="searches" style="position:absolute;right:0;top:0;display:none">
                        <table cellpadding=0 cellspacing=0 align="center" style="padding:2px">
	                        <tr>
	                        	<th style="padding:3px"><?php echo _("Select a predefined to search") ?> <a style="margin: 0 0 0 5px" href="javascript:;" onclick="$('#searches').toggle()"><img src="../pixmaps/cross-circle-frame.png" alt="<?php echo _("Close"); ?>" title="<?php echo _("Close"); ?>" border="0" /></a></th>
	                        </tr>
	                        <tr class="nobborder">
		                        <td id="filter_box">
		                        	<input type="hidden" name="filter" id="filter" value="default" />
									<table class="transparent" width="100%">
			                        <? $i=0;
			                        foreach ($_SESSION['logger_filters'] as $name=>$attr) {
			                        $i++;    ?>
			                        <tr>
									<td class="nobborder" style="background-color:<?php if($i%2==0){ echo 'transparent'; }else{ echo '#F2F2F2'; } ?>">
				                        <a onclick="change_filter('<?php echo $name ?>')" href="#" id="filter_<?php echo $name ?>">
				                        <?php echo $name ?>
				                        </a>
									</td>
									<td class="nobborder" width="30" nowrap>
				                        <div style="width: 40px;opacity:0.4;filter:alpha(opacity=40)">
				                        <img src="../pixmaps/disk-gray.png" alt="<?php echo _("Update"); ?>" title="<?php echo _("Update"); ?>" border="0" />
				                        <img src="../vulnmeter/images/delete.gif" alt="<?php echo _("Delete"); ?>" title="<?php echo _("Delete"); ?>" border="0" />
				                        </div>
									</td>
									</tr>
			                        <? } ?>
									</table>
		                        </td>
	                        </tr>
	                        <tr>
	                       		<td id="filter_msg" class="nobborder"></td>
	                        </tr>
	                        <tr>
	                        	<td class="nobborder" style="text-align: left;padding-left: 7px;padding-top:2px">
		                        <input type="text" name="filter_name" id="filter_name" value="" style="width:140px"  />
		                        <input type="button" value="<?php echo _("add")?>" onclick="new_filter()" class="button" style="height:18px;width:30px" />
	                       		</td>
	                        </tr>
                        </table>
                        </div>
                        </div>
                    </td>
				</tr>
				<tr>
					<td class="nobborder" style="font-size:9x">&nbsp;</td>
					<td class="nobborder">
					<div id="tip_msg" style="font-family:arial;font-size:9px;color:gray;display:none">
					<?php echo _("Add new criteria search or click Submit button to get events.") ?>
					</div>
					</td>
				</tr>
			</table>
		</tr>
		
		<tr><td style="padding-left:10px;padding-right:10px" colspan="5" class="nobborder"><table class="transparent" width="100%" cellpadding=0 cellspacing=0 border=0><tr><td class="nobborder" style="background:url('../pixmaps/points.gif') repeat-x"><img src="../pixmaps/points.gif"></td></tr></table></td></tr>
		
		<tr>
			<td class="nobborder" style="padding:10px" valign="top">
			<table class="transparent" width="100%">
				<tr>
				<td class="nobborder">
					<table class="transparent">
                    <tr>
                         <?php
                            $txtzone = "<a href=\"javascript:;\" class=\"scriptinfoimg\" style=\"color:black\" txt=\"<img src='../pixmaps/timezones/".rawurlencode(Util::timezone($tz)).".png' border=0>\">".Util::timezone($tz)."</a>";
                         ?>
                        <td class="nobborder" nowrap style="font-size:11px;font-family:arial"><?=_("Time frame selection")." $txtzone"?>:</td>
                        <td class="nobborder">
                            <div id="widget">
                                <a href="javascript:;"><img src="../pixmaps/calendar.png" id='imgcalendar' border="0"></a>
                                <div id="widgetCalendar"></div>
                            </div>
                        </td>
                        <td class="nobborder" nowrap>
                        <?php
                        if ($param_start != "" && $param_end != "" && date_parse($param_start) && date_parse($param_end)) {
                        ?>
                            <input type="text" size="18" id="start_aaa" name="start_aaa" value="<?php echo $param_start; ?>">
                            <input type="text" size="18" id="end_aaa" name="end_aaa" value="<?php echo $param_end; ?>" >

                        <?php
                        } else {
                        ?>
                            <input type="text" size="18" id="start_aaa" name="start_aaa" value="<?php echo gmdate("Y-m-d H:i:s", $timetz - (24 * 60 * 60)) ?>">
                            <input type="text" size="18" id="end_aaa" name="end_aaa" value="<?php echo gmdate("Y-m-d H:i:s", $timetz); ?>">
                        <?php
                        }
                        ?>
                        <input type="hidden" name="tzone" id="tzone" value="<?=$tz?>">
                        <input type="button" value="<?=_("OK")?>" onclick="change_calendar();setFixed2();" class="button" style="font-size:10px;height:20px;width:28px" />
                        </td>
					</tr>
                    <tr>
                        <td class="nobborder" colspan="3" nowrap><img src="../pixmaps/arrow_green.gif" alt="" align="absmiddle"></img> <?php echo gettext("Fetch"); ?>&nbsp;
                            <select name="top" id="top" onchange="document.getElementById('offset').value='0';doQuery('noExport')">
                                <option value="10">10</option>
                                <option value="50" selected>50</option>
                                <option value="100">100</option>
                            </select>&nbsp;<?php echo ($from_remote) ? _("events <b>per server</b>") : _("events per page"); ?>
                        </td>
                    </tr>
					</table>
				</td>
				<td nowrap class="nobborder" valign="top">
					<table class="transparent">
					<tr>
					<td class="nobborder" nowrap id="date2td" style="padding-left:5px;padding-right:5px" <? if ($_GET['time_range'] == "day") echo "bgcolor='#28BC04'" ?>><a <?php
if ($_GET['time_range'] == "day") echo "style='color:white;font-weight:bold'"; else echo "style='color:black;font-weight:bold'" ?> href="javascript:setFixed('<?php echo gmdate("Y-m-d H:i:s", $timetz - (24 * 60 * 60)) ?>','<?php echo gmdate("Y-m-d H:i:s", $timetz); ?>','day','<?php echo urlencode(date("M d, Y")) ?>');" onClick="javascript:bold_dates('date2');" id="date2a"><?=_("Last 24 Hours")?></a>
					</td>
					<td class="nobborder"><font style="color:green;font-weight:bold">|</font></td>
					<td class="nobborder" id="date3td" nowrap style="padding-left:5px;padding-right:5px" <? if ($_GET['time_range'] == "week") echo "bgcolor='#28BC04'" ?>><a <?php
if ($_GET['time_range'] == "week") echo "style='color:white;font-weight:bold'"; else echo "style='color:black;font-weight:bold'" ?> href="javascript:setFixed('<?php echo gmdate("Y-m-d H:i:s", $timetz - ((24 * 60 * 60) * 7)) ?>','<?php echo gmdate("Y-m-d H:i:s", $timetz); ?>','last_week','<?php echo urlencode(date("M, Y")) ?>');" onClick="javascript:bold_dates('date3');" id="date3a"><?=_("Last Week")?></a>
					</td>
					<td class="nobborder"><font style="color:green;font-weight:bold">|</font></td>
					<td class="nobborder" id="date4td" nowrap style="padding-left:5px;padding-right:5px" <? if ($_GET['time_range'] == "month") echo "bgcolor='#28BC04'" ?>><a <?php
if ($_GET['time_range'] == "month") echo "style='color:white;font-weight:bold'"; else echo "style='color:black;font-weight:bold'" ?> href="javascript:setFixed('<?php echo gmdate("Y-m-d H:i:s", $timetz - ((24 * 60 * 60) * 31)) ?>','<?php echo gmdate("Y-m-d H:i:s", $timetz); ?>','last_month','<?php echo urlencode(date("M, Y")) ?>');" onClick="javascript:bold_dates('date4');" id="date4a"><?=_("Last Month")?></a>
					</td>
					<td class="nobborder"><font style="color:green;font-weight:bold">|</font></td>
					<td class="nobborder" id="date5td" nowrap style="padding-left:5px;padding-right:5px" <? if ($_GET['time_range'] == "all") echo "bgcolor='#28BC04'" ?>><a <?php
if ($_GET['time_range'] == "all") echo "style='color:white;font-weight:bold'"; else echo "style='color:black;font-weight:bold'" ?> href="javascript:setFixed('<?php echo gmdate("Y-m-d H:i:s", $timetz - ((24 * 60 * 60) * 365)) ?>','<?php echo gmdate("Y-m-d H:i:s", $timetz); ?>','last_year','<?php echo urlencode(date("Y")) ?>');" onClick="javascript:bold_dates('date5');" id="date5a"><?=_("Last Year")?></a>
					</td>
					</tr>
					</table>
				</td>
				<td class="nobborder" nowrap align="middle" valign="center" style="padding-left:20px">
					<div id="numlines" style="vertical-align:middle; padding-right:10px">&nbsp;</div>
				</td>
				<td class="nobborder" nowrap width="20" style="padding-left:15px" valign="top">
					<div id="loading" style="display:none; vertical-align:middle; padding-right:10px; padding-top:10px;"><img src="<?php echo $config["loading_graph"]; ?>" align="middle" style="vertical-align:middle;"></div>
				</td>
				</tr>
			</table>
			</td>
		</tr>
	</table>
	</td>
	</tr>
	</table>
</form>
<table class="transparent" width="100%">
<tr>
<td class="nobborder" width="50" valign="top"><a href="javascript:getGraphs();" id="graphs_link"><img src="<?php echo $config["toggle_graph"]; ?>" border="0" title="<?=_("Toggle Graph")?>"> <small><font color="black"><?php echo _("Stats") ?></font></small></a></td>
<td class="nobborder"><div id="loadingProcess">
<iframe id="processframe" src="" width="100%" style="height:30px" frameborder="0" scrolling="no"></iframe>
</div></td>
</tr>
</table>
<div id="test" onMouseOver="showTip('<?php echo $help_entries["graphs"] ?>','lightblue','300')" onMouseOut="hideTip()" style="z-index:50;display:none">
</div>
<?
//echo "storage_graphs2.php?label=" . _("Sources") . "&what=src_ip&uniqueid=$uniqueid";
?>
<div id="ResponseDiv" style="height:22px;margin-top:5px" onMouseOver="showTip('<?php echo $help_entries["result_box"] ?>','lightblue','300')" onMouseOut="hideTip()">
</div>
<script>
<?php
if ($param_start != "" && $param_end != "" && date_parse($param_start) && date_parse($param_end)) {
    //print "setFixed('$param_start', '$param_end', '', '');\n";
} else {
    //print "RequestLines();MakeRequest();\n";
}
?>
</script>

<div id="dhtmltooltip" style="position: absolute;width: 150px;border: 2px solid black;padding: 2px;background-color: lightyellow;visibility: hidden;z-index: 100;"></div>

<script type="text/javascript">

/***********************************************
* Cool DHTML tooltip script- © Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
***********************************************/

var offsetxpoint=-60 //Customize x offset of tooltip
var offsetypoint=20 //Customize y offset of tooltip
var ie=document.all
var ns6=document.getElementById && !document.all
var enabletip=false
if (ie||ns6)
	var tipobj=document.all? document.all["dhtmltooltip"] : document.getElementById? document.getElementById("dhtmltooltip") : ""

	function ietruebody(){
		return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
	}

	function ddrivetip(thetext, thecolor, thewidth){
		if (ns6||ie){
			if (typeof thewidth!="undefined") tipobj.style.width=thewidth+"px"
				if (typeof thecolor!="undefined" && thecolor!="") tipobj.style.backgroundColor=thecolor
					tipobj.innerHTML=thetext
					enabletip=true
					return false
		}
	}

	function positiontip(e){
		if (enabletip){
			var curX=(ns6)?e.pageX : event.clientX+ietruebody().scrollLeft;
			var curY=(ns6)?e.pageY : event.clientY+ietruebody().scrollTop;
			//Find out how close the mouse is to the corner of the window
			var rightedge=ie&&!window.opera? ietruebody().clientWidth-event.clientX-offsetxpoint : window.innerWidth-e.clientX-offsetxpoint-20
			var bottomedge=ie&&!window.opera? ietruebody().clientHeight-event.clientY-offsetypoint : window.innerHeight-e.clientY-offsetypoint-20

			var leftedge=(offsetxpoint<0)? offsetxpoint*(-1) : -1000

			//if the horizontal distance isn't enough to accomodate the width of the context menu
			if (rightedge<tipobj.offsetWidth)
				//move the horizontal position of the menu to the left by it's width
				tipobj.style.left=ie? ietruebody().scrollLeft+event.clientX-tipobj.offsetWidth+"px" : window.pageXOffset+e.clientX-tipobj.offsetWidth+"px"
				else if (curX<leftedge)
					tipobj.style.left="5px"
					else
						//position the horizontal position of the menu where the mouse is positioned
						tipobj.style.left=curX+offsetxpoint+"px"

						//same concept with the vertical position
						if (bottomedge<tipobj.offsetHeight)
							tipobj.style.top=ie? ietruebody().scrollTop+event.clientY-tipobj.offsetHeight-offsetypoint+"px" : window.pageYOffset+e.clientY-tipobj.offsetHeight-offsetypoint+"px"
							else
								tipobj.style.top=curY+offsetypoint+"px"
								tipobj.style.visibility="visible"
		}
	}

	function hideddrivetip(){
		if (ns6||ie){
			enabletip=false
			tipobj.style.visibility="hidden"
			tipobj.style.left="-1000px"
			tipobj.style.backgroundColor=''
			tipobj.style.width=''
		}
	}

	document.onmousemove=positiontip
</script>
<form action="validate.php" method="post" id="validate_form">
<input type="hidden" name="log">
<input type="hidden" name="start">
<input type="hidden" name="end">
<input type="hidden" name="logfile">
<input type="hidden" name="signature">
<input type="hidden" name="server">
</form>
<form action="../conf/solera.php" method="post" id="solera_form">
<input type="hidden" name="from">
<input type="hidden" name="to">
<input type="hidden" name="src_ip">
<input type="hidden" name="dst_ip">
<input type="hidden" name="src_port">
<input type="hidden" name="dst_port">
<input type="hidden" name="proto">
</form>
<script>
    function validate_signature (log,start,end,logfile,signature,server) {
        $('#validate_form input[name=log]').val(log);
        $('#validate_form input[name=start]').val(start);
        $('#validate_form input[name=end]').val(end);
        $('#validate_form input[name=logfile]').val(logfile);
        $('#validate_form input[name=signature]').val(signature);
        $('#validate_form input[name=server]').val(server);
        GB_show_post('<?=_("Validate signature")?>','#validate_form',300,600);
    }
    function solera_deepsee (from,to,src_ip,src_port,dst_ip,dst_port,proto) {
        $('#solera_form input[name=from]').val(from);
        $('#solera_form input[name=to]').val(to);
        $('#solera_form input[name=src_ip]').val(src_ip);
        $('#solera_form input[name=src_port]').val(src_port);
        $('#solera_form input[name=dst_ip]').val(dst_ip);
        $('#solera_form input[name=dst_port]').val(dst_port);
        $('#solera_form input[name=proto]').val(proto);
        GB_show_post('Solera DeepSee &trade;','#solera_form',300,600);
    }
</script>
</body>
</html>
