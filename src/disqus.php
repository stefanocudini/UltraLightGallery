<?

function disqus_init()
{
	return false;
}


function disqus_panel_bottom()
{
?>
<div id="disqus_thread"></div>
<script>
	var disqus_shortname = 'easyblog-it';
    (function() {
        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
        dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
    })();
</script> 
<?
  return 'Commenti';
} # */


function disqus_css()
{
  global $colors;
  global $images;
  global $dims;
?>
<? if(false): ?>
<style type="text/css">
<? endif; ?>
<? if(false): ?>
</style>
<? endif; ?>
<?
}

function disqus_thumb_menu($fotofile)
{
  return false;
} # */


?>
