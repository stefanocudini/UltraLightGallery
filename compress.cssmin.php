<?php

class cssmin {
/**
 * cssmin.php - A simple CSS minifier.
 * @author 		Joe Scylla <joe.scylla@gmail.com>
 * @copyright 	2008 Joe Scylla <joe.scylla@gmail.com>
 */
 function minify($css, $options = "")
	{
	$options = ($options == "") ? array() : (is_array($options) ? $options : explode(",", $options));
	if (in_array("preserve-urls", $options))
		{
		// Encode url() to base64
		$css = preg_replace_callback("/url\s*\((.*)\)/siU", array(self, "_encodeUrl"), $css);
		}
	// Remove comments
	$css = preg_replace("/\/\*[\d\D]*?\*\/|\t+/", " ", $css);
	// Replace CR, LF and TAB to spaces
	
	$css = str_replace(array("\n", "\r", "\t"), " ", $css);
	
	// Replace multiple to single space
	$css = preg_replace("/\s\s+/", " ", $css);
	// Remove unneeded spaces
	$css = preg_replace("/\s*({|}|\[|\]|=|~|\+|>|\||;|:|,)\s*/", "$1", $css);
	$css = trim($css);
	if (in_array("preserve-urls", $options))
		{
		// Decode url()
		$css = preg_replace_callback("/url\s*\((.*)\)/siU", array(self, "_decodeUrl"), $css);
		}
	return $css;
	}
/**
 * Encodes a url() expression.
 *
 * @param	array	$match
 * @return	string
 */
 function _encodeUrl($match)
	{
	return "url(" . base64_encode(trim($match[1])) . ")";
	}
/**
 * Decodes a url() expression.
 *
 * @param	array	$match
 * @return	string
 */
function _decodeUrl($match)
	{
	return "url(" . base64_decode($match[1]) . ")";
	}
}


?>