<?

function InternetCombineUrl($absolute, $relative) {
    $p = parse_url($relative);
    if($p["scheme"])return $relative;

    extract(parse_url($absolute));

    if($relative{0} == '/') {
        $cparts = array_filter(explode("/", $relative));
    }
    else {
        $aparts = array_filter(explode("/", $path));
        $rparts = array_filter(explode("/", $relative));
        $cparts = array_merge($aparts, $rparts);
        foreach($cparts as $i => $part) {
            if($part == '.') {
                $cparts[$i] = null;
            }
            if($part == '..') {
                $cparts[$i - 1] = null;
                $cparts[$i] = null;
            }
        }
        $cparts = array_filter($cparts);
    }
    $path = implode("/", $cparts);
    $url = "";
    if($scheme) {
        $url = "$scheme://";
    }
    if($user) {
        $url .= "$user";
        if($pass) {
            $url .= ":$pass";
        }
        $url .= "@";
    }
    if($host) {
        $url .= "$host/";
    }
    $url .= $path;
	
	if($query) {
        $url .= "?$query";
	}

	
    return $url;
}

?>