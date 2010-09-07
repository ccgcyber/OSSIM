<?php
/*****************************************************************************
*
*    License:
*
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
?>
<table class="bordered" height="100%">
	<tr>
		<td class="headerpr" height="20"><a style="color:black" href="../top.php?option=2&soption=2&url=<?=urlencode("vulnmeter/index.php?value=$host&type=hn&hmenu=Vulnerabilities&smenu=Vulnerabilities")?>" target='topmenu'><?=gettext("Latest Vulnerabilities")?></a></td>
	</tr>
	<? if (count($vul_events) < 1) { ?>
	<tr><td><?=gettext("No Vulnerabilities Found for")?> <i><?=$host?></i></td></tr>
	<? } else { ?>
	<tr>
		<td valign="top">
			<table>
				<tr>
					<th><?=gettext("Name")?></th>
					<th><?=gettext("Risk")?></th>
					<? if ($network) { ?><th>IP</th><? } ?>
				</tr>
			<? $i = 0; foreach ($vul_events as $vul_event) { if ($i > 5) continue; $color = (($i+1)%2==0) ? "#E1EFE0" : "#FFFFFF"; ?>
				<tr>
					<td bgcolor="<?=$color?>" style="text-align:left;color: #17457c;font-size:10px"><b><?=$vul_event['name']?></b></td>
					<td bgcolor="<?=$color?>"><?=Incident::get_priority_in_html($vul_event['risk'])?></td>
					<? if ($network) { ?><td bgcolor="<?=$color?>"><a href="host_report.php?host=<?=$vul_event['ip']?>" class="HostReportMenu" id="<?=$vul_event['ip']?>;<?=$vul_event['ip']?>"><?=$vul_event['ip']?></a></td><? } ?>
				</tr>
			<? $i++; } ?>
			</table>
		</td>
	</tr>
	<tr><td style="text-align:right;padding-right:20px"><a style="color:black" href="../top.php?option=2&soption=2&url=<?=urlencode("vulnmeter/index.php?value=$host&type=hn&hmenu=Vulnerabilities&smenu=Vulnerabilities")?>" target='topmenu'><b><?=gettext("More")?> >></b></a></td></tr>
	<? } ?>
	<tr><td></td></tr>
</table>