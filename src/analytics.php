<?

function analytics_init()
{
	global $opts;
	global $analyticsid;
	
	$opts['analyticsid'] = isset($analyticsid) ? $analyticsid : 'UA-22278729-1';
}

function analytics_tail()  //infondo alla pagina
{
  global $admin;
  global $opts;
  
  if(islocal() or $admin) return false;
  
?>
<script>

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '<?=$opts['analyticsid']?>' ]);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<?
}
?>
