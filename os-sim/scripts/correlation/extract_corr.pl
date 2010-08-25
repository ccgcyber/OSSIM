#!/usr/bin/perl

if(!$ARGV[0]){
print "Usage: $0 filename\n";
exit();
}

open(IN,"<$ARGV[0]") or die "Can't open $ARGV[0]";

if($ARGV[0] =~ /\/([^\/]*)$/){
print "---- $1\n";
}

while(<IN>){
#alert tcp $EXTERNAL_NET any -> $HOME_NET 22 (msg:"EXPLOIT SSH server banner overflow"; flow:established,from_server; content:"SSH-"; nocase; isdataat:200,relative; pcre:"/^SSH-\s[^\n]{200}/ism"; reference:bugtraq,5287; classtype:misc-attack; sid:1838; rev:6;)
if(/alert.*\s+((\d+)|any)\s+\(msg:"([^"]*)".*sid:(\d+)/){

if ($1 eq "any") {$port=0 } else {$port = $1};

$msg = $2;
$sid = $3;
print "-- $sid $msg\n";
print "1001,$sid,5002,$port\n";
if(/windows/i){
print "1001,$sid,5001,1\n";
}
if(/linux/i){
print "1001,$sid,5001,2\n";
}
if(/cisco/i){
print "1001,$sid,5001,3\n";
}
if(/bsd/i){
print "1001,$sid,5001,4\n";
}
if(/freebsd/i){
print "1001,$sid,5001,5\n";
}
if(/netbsd/i){
print "1001,$sid,5001,6\n";
}
if(/openbsd/i){
print "1001,$sid,5001,7\n";
}
if(/hp-ux/i){
print "1001,$sid,5001,8\n";
}
if(/sunos|solaris/i){
print "1001,$sid,5001,9\n";
}
if(/macos/i){
print "1001,$sid,5001,10\n";
}
if(/plan9/i){
print "1001,$sid,5001,11\n";
}
if(/sco /i){
print "1001,$sid,5001,12\n";
}
if(/aix /i){
print "1001,$sid,5001,13\n";
}
}

}

close IN;
