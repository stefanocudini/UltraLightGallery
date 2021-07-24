<?

function themes_init()
{
	global $colors;
	
	
	/*
	$colors['text'] =       '#646464';  //testo
	$colors['background'] = '#ffffff';  //sfondo della pagina
	$colors['bgthumb'] =    '#d7d7d7';  //sfondo delle thumbnails
	$colors['bgbox'] =      '#f3f3f3';  //sfondo oggetti
	$colors['border'] =     '#e1e1e1';  //bordi oggetti
	$colors['hover'] =      '#ff8000';  //link e oggetti attivi
	$colors['bghover'] =    '#ffd079';  //sfondo oggetti attivi
	$colors['bginput'] =    '#f0f0f0';  //sfondo pulsanti,textbox,textarea
	*/
}


function themes_menu()
{
?>
<span id="themes">
   <a id="theme_black" title="black"><span>Nero</span>&nbsp;</a>
   <a id="theme_white" title="white"><span>Bianco</span>&nbsp;</a>
</span>
<?
} # */


function themes_css()
{
  global $colors;
?>
<? if(false):?>
<style type="text/css">
<? endif; ?>
#themes a {
  padding:0 0.4em;
}
#themes a span {
  display:none;
}

#theme_black {
  background-color:#000;
  border: 0.125em solid #333;
}

#theme_white {
  background-color: #fff;
  border: 0.125em solid #ff8000;
}

<? if(false):?>
</style>
<? endif; ?>
<?
} # */


function themes_head_js()
{
  global $urls;
  #require jquery.cookie.js
?>
  <script src="<?=$urls['plugins']?>jquery.styleswichter.js" ></script>
<?
}


function themes_head()
{
  global $urls;
?>
  <link rel="stylesheet" title="styleswichter" type="text/css" href="" />
<?
}


function themes_js()
{ 
?>
<? if(false): ?>
<script>
<? endif; ?>

var themes = [];

themes["black"]= "black.css";
themes["white"]= "white.css";

$(document).ready(function() {

  var options = {
	linkTitle: 'styleswichter',
	cookieName: 'selected-style'
  };
  $.fn.StyleSwichter(options);
  
  $('#themes a').click(function() {
    SetStyle(themes[$(this).attr('title')]);
	//$(this).blur();
	return false;
  });
  
});

function SetStyle(aCssPath) {
  var options = {
	cookieDays: 30,
	cssPath: aCssPath,
	linkTitle: 'styleswichter',
	cookieName: 'selected-style'
  };      
  // Set the appropiate style
  $.fn.StyleSwichter(options);
}

<? if(false): ?>
</script>
<? endif; ?>
<?
} # */

?>