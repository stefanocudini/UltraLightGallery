#!/bin/bash


#FILTER='-not -regex ".*/\..*" -not -regex ".*_html/.*" -not -regex ".*_trash/.*" -not -regex ".*/_.*jpg" -not -regex ".*/_exif.*"'


find $1 -type f -name "*.jpg" -not -name "_*" -not -regex ".*_trash/.*" -printf "%TY-%Tm-%Td %p\n" | sort -k1 -r | head -n100 > lastupload.list
