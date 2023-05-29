<?php

function chat_head_js()
{
	global $urls;
	global $fotopage;
	global $start;
	global $public;
	global $ajax;
	global $admin;

	if(!$public or $ajax or $fotopage or $start!='ulg') return false;

	?>
<script type="text/javascript">
window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');
$.src='//v2.zopim.com/?1ZtjdzMD8HuZ8I3qsM6HYzBjZ9reP0WA';z.t=+new Date;$.
type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');
</script>
	<?
}


?>