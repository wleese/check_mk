<?php
# +------------------------------------------------------------------+
# |             ____ _               _        __  __ _  __           |
# |            / ___| |__   ___  ___| | __   |  \/  | |/ /           |
# |           | |   | '_ \ / _ \/ __| |/ /   | |\/| | ' /            |
# |           | |___| | | |  __/ (__|   <    | |  | | . \            |
# |            \____|_| |_|\___|\___|_|\_\___|_|  |_|_|\_\           |
# |                                                                  |
# | Copyright Mathias Kettner 2013             mk@mathias-kettner.de |
# +------------------------------------------------------------------+
#
# This file is part of Check_MK.
# The official homepage is at http://mathias-kettner.de/check_mk.
#
# check_mk is free software;  you can redistribute it and/or modify it
# under the  terms of the  GNU General Public License  as published by
# the Free Software Foundation in version 2.  check_mk is  distributed
# in the hope that it will be useful, but WITHOUT ANY WARRANTY;  with-
# out even the implied warranty of  MERCHANTABILITY  or  FITNESS FOR A
# PARTICULAR PURPOSE. See the  GNU General Public License for more de-
# ails.  You should have  received  a copy of the  GNU  General Public
# License along with GNU Make; see the file  COPYING.  If  not,  write
# to the Free Software Foundation, Inc., 51 Franklin St,  Fifth Floor,
# Boston, MA 02110-1301 USA.

$opt[1] = "--vertical-label 'MEMORY(MB)' -X0 --upper-limit " . ($MAX[1] * 120 / 100) . " -l0  --title \"Memory usage $hostname\" ";

$maxgb = sprintf("%.1f", $MAX[1] / 1024.0);

# For the rest of the data we rather work with names instead
# of numbers
$RRD = array();
foreach ($NAME as $i => $n) {
    $RRD[$n] = "$RRDFILE[$i]:$DS[$i]:MAX";
    $WARN[$n] = $WARN[$i];
    $CRIT[$n] = $CRIT[$i];
    $MIN[$n]  = $MIN[$i];
    $MAX[$n]  = $MAX[$i];
}

$def[1] = "";

if (isset($RRD['pagetables'])) {
    $def[1] .= "DEF:pagetables=$RRD[pagetables] "
            .  "DEF:ram=$RRD[ramused] ";
}
else {
    $def[1] .= "DEF:ram=$RRD[ramused] ";
}
   
$def[1] .= "DEF:virt=$RRDFILE[3]:$DS[3]:MAX "
        . "DEF:swap=$RRDFILE[2]:$DS[2]:MAX " 

        . "HRULE:$MAX[3]#000080:\"RAM+SWAP installed\" "
        . "HRULE:$MAX[1]#2040d0:\"$maxgb GB RAM installed\" "
        . "HRULE:$WARN[3]#FFFF00:\"Warning\" "
        . "HRULE:$CRIT[3]#FF0000:\"Critical\" "
       
        . "'COMMENT:\\n' "
        . "AREA:ram#80ff40:\"RAM used        \" " 
        . "GPRINT:ram:LAST:\"%6.0lf MB last\" " 
        . "GPRINT:ram:AVERAGE:\"%6.0lf MB avg\" " 
        . "GPRINT:ram:MAX:\"%6.0lf MB max\\n\" "
       
        . "AREA:swap#008030:\"SWAP used       \":STACK " 
        . "GPRINT:swap:LAST:\"%6.0lf MB last\" " 
        . "GPRINT:swap:AVERAGE:\"%6.0lf MB avg\" " 
        . "GPRINT:swap:MAX:\"%6.0lf MB max\\n\" "
        ;
       

if (isset($RRD['pagetables'])) {
   $def[1] .= ""
           . "AREA:pagetables#ff8800:\"Page tables     \":STACK " 
           . "GPRINT:pagetables:LAST:\"%6.0lf MB last\" " 
           . "GPRINT:pagetables:AVERAGE:\"%6.0lf MB avg\" "
           . "GPRINT:pagetables:MAX:\"%6.0lf MB max\\n\" "
           . "LINE:virt#000000:\"RAM+SWAP+PT used\" " 
           . "GPRINT:virt:LAST:\"%6.0lf MB last\" " 
           . "GPRINT:virt:AVERAGE:\"%6.0lf MB avg\" " 
           . "GPRINT:virt:MAX:\"%6.0lf MB max\\n\" " 
           ;
}

else {
   $def[1] .= "LINE:virt#000000:\"RAM+SWAP used   \" " 
           . "GPRINT:virt:LAST:\"%6.0lf MB last\" " 
           . "GPRINT:virt:AVERAGE:\"%6.0lf MB avg\" " 
           . "GPRINT:virt:MAX:\"%6.0lf MB max\\n\" " 
           ;
}

if (isset($RRD['mapped'])) {
   $def[1] .= "DEF:mapped=$RRD[mapped] "
           . "LINE2:mapped#8822ff:\"Memory mapped   \" "
           . "GPRINT:mapped:LAST:\"%6.0lf MB last\" " 
           . "GPRINT:mapped:AVERAGE:\"%6.0lf MB avg\" " 
           . "GPRINT:mapped:MAX:\"%6.0lf MB max\\n\" " ;
}

if (isset($RRD['committed_as'])) {
   $def[1] .= "DEF:committed=$RRD[committed_as] "
           . "LINE2:committed#cc00dd:\"Committed       \" "
           . "GPRINT:committed:LAST:\"%6.0lf MB last\" " 
           . "GPRINT:committed:AVERAGE:\"%6.0lf MB avg\" "
           . "GPRINT:committed:MAX:\"%6.0lf MB max\\n\" " ;
}

?>
