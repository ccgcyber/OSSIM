#!/usr/bin/perl
$|=1;
use Time::Local;

if(!$ARGV[6]){
print "Expecting: start_date end_date query start_line num_lines order_by operation cache_file\n";
print "Don't forget to escape the strings\n";
exit;
}

$debug="";

$start = $ARGV[0];
$end = $ARGV[1];
$query = $ARGV[2];
$start_line = $ARGV[3];
$num_lines = $ARGV[4];
$order_by = $ARGV[5];
$operation = $ARGV[6];
$cache_file = $ARGV[7];
$idsesion = $ARGV[8];

$user = $ARGV[9];
$ips = $ARGV[10];

my @ips_arr = split(/\,/,$ips);
foreach $ip (@ips_arr) {
	print "Connecting $ip\n";
	$cmd = "cd /usr/share/ossim/www/sem;perl fetchall.pl '$start' '$end' '$query' $start_line $num_lines $order_by $operation $cache_file $idsesion $user";
	system($cmd);
	#print "ssh $ip \"$cmd\"\n";
}