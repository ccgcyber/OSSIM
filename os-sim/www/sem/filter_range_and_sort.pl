#!/usr/bin/perl
use Time::Local;
use DBI;
use Net::CIDR;

my $dbhost = `grep db_ip /etc/ossim/ossim_setup.conf | cut -f 2 -d "="`; chomp($dbhost);
$dbhost = "localhost" if ($dbhost eq "");
my $dbuser = `grep user /etc/ossim/ossim_setup.conf | cut -f 2 -d "="`; chomp($dbuser);
my $dbpass = `grep pass /etc/ossim/ossim_setup.conf | cut -f 2 -d "="`; chomp($dbpass);

$|=1; 
if(!$ARGV[5]){
print "Accepts two epoch_timestamps as commands, loglines as stdin. Only prints out those within the two timestamps\n";
exit;
}
$debug = 0; # 1 for debuging info
$debug_log = "";

$start = $ARGV[0];
$end = $ARGV[1];
$start_line = $ARGV[2];
$num_lines = $ARGV[3];
$filter = $ARGV[4];
$order_by = ($ARGV[5] eq "date") ? "perl -e 'print <>'" : "perl -e 'print reverse <>'";
$reverse =  ($ARGV[5] eq "date") ? 0 : 1;
$debug = 1 if ($ARGV[6] eq "debug");
$debug_log = $ARGV[7] if ($ARGV[7] ne "");

#$grep_str = `perl format_params_grep.pl "$filter"`;
#print "Calling: php grep_filter.php \"$filter\" get_string\n";
#$grep_str = `php grep_filter.php "$filter" get_string`;
#print $grep_str."\n";exit;
#$redo_filter = ($grep_str =~ /plugin\_sid/) ? 1 : 0;

%filters = ();
set_filters($filter);
#debug_filters(); exit;

#chop($grep_str);
if ($debug_log ne "") {
	open (L,">>$debug_log");
	print L "FILTER_RANGE_AND_SORT.pl: FORMAT_PARAMS_GREP.pl: $grep_str\n";
	close L;
}

$lines_threshold = $start_line + $num_lines;

$hourday = 0;
$complete_lines = 0;
$currentdate = 0;
my %events = ();
my %already = ();
my @files = ();
#
# add first last hours of current day
#
if ($ARGV[5] ne "date") {
	my @tm = localtime($end); $tm[5]+=1900; $tm[4]++;
	$tm[3] = "0".$tm[3] if (length($tm[3])<2);
	$tm[4] = "0".$tm[4] if (length($tm[4])<2);
	open (L,"find /var/ossim/logs/".$tm[5]."/".$tm[4]."/".$tm[3]."/ -name *log 2>/dev/null | sort -r |");
	while($file=<L>) {
		chomp($file);  
		print "Adding log: $file\n" if ($debug);
		 if ($debug_log ne "") {
			open (L,">>$debug_log");
			print L "FILTER_RANGE_AND_SORT.pl: Adding log: $file\n";
			close L;
		}
		#$already{$file}++;
		#push (@files,$file);
		}
	close L;
}
#
#
while($file = <STDIN>){
	chomp($file);
	push (@files, $file) if (!defined $already{$file});
}
#
$searchingdate1 = "";
$searchingdate2 = "";
foreach my $file (@files) {
	#if ($debug) {
		#print "$file\n";
		#next;
	#}
	next if ($file =~ /Warning/ || $file eq "");
	my @fields = split(/\//,$file);
	my $sdirtime = timegm(0, 0, $fields[7], $fields[6], $fields[5]-1, $fields[4]);
	my $edirtime = timegm(59, 59, $fields[7], $fields[6], $fields[5]-1, $fields[4]);
	if ($start<=$sdirtime && $edirtime<=$end) { #if ($edirtime > $start && $sdirtime < $end) {
		#print "$file: $start - $dirtime - $end\n" if ($debug);
		if ($fields[4].$fields[5].$fields[6].$fields[7]==$hourday || $complete_lines<$lines_threshold) { # read files while same hourday or need more events
			$hourday = $fields[4].$fields[5].$fields[6].$fields[7];
			$searchingdate1 = $fields[4].$fields[5].$fields[6];
			if ($searchingdate1 ne $searchingdate2) {
				print "Searching in $searchingdate1\n";
				$searchingdate2 = $searchingdate1;
			}
			$lastdate = $currentdate; # last selected event date
			#
			$jumprow = 0;
			#if ($grep_str eq "") {
			if ($filter eq "") {
				# calc jump row
				$jumprow = 1;
				%timeline = ();
				my $filet = $file; $filet =~ s/log(\.gz)?$/ind/;
				if (-e $filet) {
					open (F,$filet);
					while (<F>) {
						chomp;
						next if /^lines/;
						my @tmp = split/\:/;
						$timeline{$tmp[0]} = (!$reverse) ? $tmp[2] : $tmp[1];
					}
					close F;
					# calc jump row
					foreach $fecha (sort {$a<=>$b} keys (%timeline)) {
						$jumprow = $timeline{$fecha} if ($reverse && $end>=$fecha);
						$jumprow = $timeline{$fecha} if (!$reverse && $fecha<=$start);
					}
				}
			}
			# read line
			$read_lines = $total_lines = 0;
			if (!-e $file && -e $file.".gz") { $file .= ".gz"; }
			$cmd = ($file =~ /\.gz$/) ? "zcat \"$file\" | $order_by |" : "$order_by \"$file\" |";
			#$cmd .= " $grep_str |" if ($grep_str ne "");
			#$cmd .= " php grep_filter.php \"$filter\" |" if ($filter ne "" && $redo_filter);
			
			print "Reading $file $jumprow $complete_lines $lines_threshold $start $end $lastdate '$filter' '$cmd'\n" if ($debug);
			if ($debug_log ne "") {
				open (L,">>$debug_log");
				print L "FILTER_RANGE_AND_SORT.pl: Reading $file $jumprow $complete_lines $lines_threshold $start $end $lastdate '$filter' '$cmd'\n";
				close L;
			}
			open (F,$cmd);
			LINE: while (<F>) {
				next LINE if ($total_lines++<$jumprow);
				#if (/ date='(\d+)' /i) {
				if (/entry id='([^']+)'\s+fdate='([^']+)'\s+date='([^']+)'\s+plugin_id='([^']+)'\s+sensor='([^']+)'\s+src_ip='([^']+)'\s+dst_ip='([^']+)'\s+src_port='([^']+)'\s+dst_port='([^']+)'\s+tzone='[^']+'+\s+data='([^']+)'(\s+sig='[^']*')?(\s+plugin_sid='[^']*')?/i) {
					$id = $1;
					$currentdate = $3;
					$plugin_id = $4;
					$sensor = $5;
					$src_ip = $6;
					$dst_ip = $7;
					$src_port = $8;
					$dst_port = $9;
					$data = $10;
					$sig = $11;
					$plugin_sid = $12;
					if ($sig =~ /plugin\_sid/) {
						$plugin_sid = $sig; $sig = "";
					}
					$plugin_sid =~ s/\s*plugin\_sid\='(.+)'/$1/;
					
					last LINE if ($reverse && $complete_lines>=$lines_threshold && $currentdate<$lastdate); # jump innecesary events
					last LINE if (!$reverse && $complete_lines>=$lines_threshold && $currentdate>$lastdate); # jump innecesary events
					#print "Evento: $currentdate > $start && $currentdate < $end\n";
					if ($currentdate > $start && $currentdate < $end && pass_filters($_,$plugin_id,$plugin_sid,$sensor,$src_ip,$dst_ip,$src_port,$dst_port,$data)) {
						#print "$complete_lines BIEN Plugin $plugin_id - $plugin_sid -> ".pass_filters($_,$plugin_id,$plugin_sid,$sensor,$src_ip,$dst_ip,$src_port,$dst_port)."\n";
						chomp;
						$events{$_.";$file"} = $currentdate;
						$complete_lines++; $read_lines++;
						#print "found $complete_lines;$_;$currentdate;$lines_threshold\n" if ($debug);
						last LINE if ($read_lines>=$lines_threshold); # jump innecesary events
					} #else {
						#print "MAL $data != ".$filters{1}{1}{'data'}."\n";
					#}
				}
			}
			close F;
		}
	}
}
print "$complete_lines $lines_threshold $start $end $lastdate '$filter'\n" if ($debug);
 if ($debug_log ne "") {
	open (L,">>$debug_log");
	print L "FILTER_RANGE_AND_SORT.pl: $complete_lines $lines_threshold $start $end $lastdate '$filter'\n";
	close L;
}



# sort events
$from = 0;
if (!$reverse) {
	foreach $event (sort {$events{$a}<=>$events{$b}} keys (%events)) {
		if ($from>=$start_line && $from<$lines_threshold) {
			print "$event\n";
		}
		$from++;
	}
} else {
	foreach $event (sort {$events{$b}<=>$events{$a}} keys (%events)) {
		if ($from>=$start_line && $from<$lines_threshold) {
			print "$event\n";
		}
		$from++;
	}
}


# FUNCTIONS
sub pass_filters {
	my $line = shift;
	my $plugin_id = shift;
	my $plugin_sid = shift;
	my $sensor = shift;
	my $src_ip = shift;
	my $dst_ip = shift;
	my $src_port = shift;
	my $dst_port = shift;
	my $data = shift;
	
	foreach $key1 (keys %filters) {
		foreach $key2 (keys %{$filters{$key1}}) {
			my $pass_filter = 0;
			foreach $type (keys %{$filters{$key1}{$key2}}) {
				# LOOP BY THE OR SENTENCES
				$pass_filter = 1 if ($type eq "plugin_id_sid" && defined $filters{$key1}{$key2}{$type}{$plugin_id}{$plugin_sid});
				$pass_filter = 1 if ($type eq "plugin_id" && defined $filters{$key1}{$key2}{$type}{$plugin_id});
				$pass_filter = 1 if ($type eq "sensor" && $filters{$key1}{$key2}{$type} eq $sensor);
				$pass_filter = 1 if ($type eq "src_ip" && $filters{$key1}{$key2}{$type} eq $src_ip);
				$pass_filter = 1 if ($type eq "dst_ip" && $filters{$key1}{$key2}{$type} eq $dst_ip);
				$pass_filter = 1 if ($type eq "src_net" && $filters{$key1}{$key2}{$type}{'from'} <= ip2long($src_ip) && $filters{$key1}{$key2}{$type}{'to'} >= ip2long($src_ip));
				$pass_filter = 1 if ($type eq "dst_net" && $filters{$key1}{$key2}{$type}{'from'} <= ip2long($dst_ip) && $filters{$key1}{$key2}{$type}{'to'} >= ip2long($dst_ip));
				$match = $filters{$key1}{$key2}{$type};
				$pass_filter = 1 if ($type eq "data" && $data =~ /$match/i);
			}
			return 0 if (!$pass_filter);
		}
	}
	
	return 1;
}

sub set_filters {
	my $filter = shift;
	
	$filter =~ s/ or /|/ig;
	@args = split(/\s+/, $filter);
	my $and_num = 1;
	foreach $arg (@args){ # LOOP by the AND elements
		if($arg eq "and" || $arg eq "AND") {next;}
		if($arg eq " ") {next;}
		if($arg eq "") {next;}
		
		my @atoms = ();
		if ($arg =~ /\|/) {
			@atoms = split(/\|/,$arg);
		} else {
			@atoms = ($arg);
		}
		
		my $or_num = 1;
		foreach $atom (@atoms) { # LOOP by the OR elements (Many times it will be 1 loop)
			# NET EQUAL
			if($atom =~ /^\s*(.*)!=(.*)$/){
				$aux_filter{$1}{'val'} = $2;
				$aux_filter{$1}{'op'} = "not equal";
				$filters{$and_num}{$or_num} = %aux_filter; $or_num++;
			# EQUAL
			} elsif ($atom =~ /^\s*(.*)=(.*)$/){
				# Taxonomy filter
				if ($atom =~ /taxonomy\=/) {
					set_taxonomy_filters($atom,$and_num,$or_num);
				}
				# Some fields filter
				elsif ($1 eq "id" || $1 eq "fdate" || $1 eq "date" || $1 eq "plugin_id" || $1 eq "sensor" || $1 eq "src_ip" || $1 eq "dst_ip" || $1 eq "ip_src" || $1 eq "ip_dst" || $1 eq "src_port" || $1 eq "dst_port" || $1 eq "tzone"|| $1 eq "data"){
					$aux = $2;
					$par = $1;
					$par =~ s/ip\_(...)/$1_ip/;
					$aux =~ s/'+//g;
					$aux = quotemeta $aux if ($par eq "data");
					$filters{$and_num}{$or_num}{$par} = $aux; $or_num++;
				}
				# IP filter (2 push for src OR dst sentence)
				elsif ($1 eq "ip") {
					$aux = $2;
					$aux =~ s/'+//g;
					$filters{$and_num}{$or_num}{'src_ip'} = $aux; $or_num++;
					$filters{$and_num}{$or_num}{'dst_ip'} = $aux; $or_num++;
				}
				# Net src/dst filter
				elsif ($1 eq "src_net" || $1 eq "dst_net") {
					$aux = $2;
					$par = $1;
					$aux =~ s/'+//g;
					my @cidr_list = ($aux);
					my @cidr_range = Net::CIDR::cidr2range(@cidr_list);
					my @range = split(/\-/,$cidr_range[0]);
					$filters{$and_num}{$or_num}{$par}{'from'} = ip2long($range[0]);
					$filters{$and_num}{$or_num}{$par}{'to'} = ip2long($range[1]); $or_num++;
				}
				# Net filter
				elsif ($1 eq "net") {
					$aux = $2;
					$aux =~ s/'+//g;
					my @cidr_list = ($aux);
					my @cidr_range = Net::CIDR::cidr2range(@cidr_list);
					my @range = split(/\-/,$cidr_range[0]);
					$filters{$and_num}{$or_num}{'src_net'}{'from'} = ip2long($range[0]);
					$filters{$and_num}{$or_num}{'src_net'}{'to'} = ip2long($range[1]); $or_num++;
					$filters{$and_num}{$or_num}{'dst_net'}{'from'} = ip2long($range[0]);
					$filters{$and_num}{$or_num}{'dst_net'}{'to'} = ip2long($range[1]); $or_num++;
				}
				# Any field filter
				else {
					$filters{$and_num}{$or_num}{$1} = quotemeta $2;
				}
			}
		}
		$and_num++;
	}
}

sub GetPluginsBySourceType {
	my $sourcetype = shift;
	
	my @ids = ();
    $temp_sql = "select id from plugin where source_type = \"$sourcetype\"";
    $dbh = conn_db();
	$sql = qq{ $temp_sql };
	$sth_sel=$dbh->prepare( $sql );
	$sth_sel->execute;
	while ( my ($id) = $sth_sel->fetchrow_array ) {
		push(@ids,$id);
	}
	$sth_sel->finish;
	disconn_db($dbh);
    
    return \@ids;
}

sub set_taxonomy_filters {
	my $filter = shift;
	my $and_num = shift;
	my $or_num = shift;
	#$filters{$and_num}{$or_num}{'plugin_id_sid'}{'7017'}{'1002'}++; # For Debug
	my @plugin_ids = ();
	if ($filter =~ /taxonomy='(.*)-(.*)-(.*)'/) {
		if ($1 ne "") {
			my $st = $1;
			$st =~ s/\_/ /g;
			my $pointer = GetPluginsBySourceType($st);
			@plugin_ids = @$pointer;
			$plugin_query = "plugin_id in (".join(",",@plugin_ids).") AND";
		}
		if ($2 ne "" && $2 ne '0') {
			$dbh = conn_db();
			$category_id = $2;
			if ($3 ne "" && $3 ne '0') {
				$subcategory_id = $3;
				$temp_sql = "select distinct plugin_id,sid from plugin_sid WHERE $plugin_query category_id=$category_id AND subcategory_id=$subcategory_id";
		    } else {
				$temp_sql = "select distinct plugin_id,sid from plugin_sid WHERE $plugin_query category_id=$category_id";
		    }
		    $sql = qq{ $temp_sql };
			$sth_sel=$dbh->prepare( $sql );
			$sth_sel->execute;
			while ( my ($plugin_id,$sid) = $sth_sel->fetchrow_array ) {
				$filters{$and_num}{$or_num}{'plugin_id_sid'}{$plugin_id}{$sid}++;
			}
			$sth_sel->finish;
		    disconn_db($dbh);
		} elsif ($#plugin_ids > 0) {
			$onlyid = true;
			foreach $plugin_id (@plugin_ids) {
				$filters{$and_num}{$or_num}{'plugin_id'}{$plugin_id}++;
			}
		}
	}
}

sub debug_filters {
	foreach $key1 (keys %filters) {
		print "Filter $key1:\n";
		foreach $key2 (keys %{$filters{$key1}}) {
			print "\tOR\n" if ($key2 > 1);
			foreach $type (keys %{$filters{$key1}{$key2}}) {
				if ($type eq "plugin_id_sid") {
					foreach $pid (keys %{$filters{$key1}{$key2}{$type}}) {
						foreach $psid (keys %{$filters{$key1}{$key2}{$type}{$pid}}) {
							print "   Plugin id-sid = $pid - $psid\n";
						}
					}
				} elsif ($type =~ /net/) {
					print "   $type = range { ".$filters{$key1}{$key2}{$type}{'from'}." - ".$filters{$key1}{$key2}{$type}{'to'}." }\n"
				} else {
					print "   $type = ".$filters{$key1}{$key2}{$type}."\n";
				}
			}
		}
	}
}

sub ip2long {
	#converts an IP address x.x.x.x into a long IP number as used by ulog
	my $ip_address = shift;
	
	my (@octets,$octet,$ip_number,$number_convert);
	
	chomp ($ip_address);
	@octets = split(/\./, $ip_address);
	$ip_number = 0;
	foreach $octet (@octets) {
		$ip_number <<= 8;
		$ip_number |= $octet;
	}
	return $ip_number;
}

sub conn_db {
    $dbh = DBI->connect( "DBI:mysql:ossim;host=$dbhost;port=3306;socket=/var/lib/mysql/mysql.sock", $dbuser, $dbpass, {
        PrintError => 0,
        RaiseError => 1,
        AutoCommit => 1 } ) or die("Failed to connect : $DBI::errstr\n");
    return $dbh;
}
 
sub disconn_db {
    my ( $dbh ) = @_;
    $dbh->disconnect or die("Failed to disconnect : $DBI::errstr\n");
}