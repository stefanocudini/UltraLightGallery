<?

function fontsize_menu()
{
?>
<a href="#" id="fontout" title="Riduci dimensioni">A<sup>-</sup></a>
<a href="#" id="fontres" title="Ripristina dimensioni">A<sup></sup></a>
<a href="#" id="fontin" title="Aumenta dimensioni">A<sup>+</sup></a>
<?
}

function fontsize_js()
{
?>
<? if(false): ?>
<script>
<? endif; ?>
$(document).ready(function() {

  var resfont = $('body').css('font-size');
	  
	$('#fontres').click(function(){
	  $('body').css('font-size',parseFloat(resfont,10));
	  $(this).blur();
	  return false;
	});
	
	$('#fontin').click(function(){
	  var orifont = $('body').css('font-size');
	  $('body').css('font-size',parseFloat(orifont,10)*1.1);
	  $(this).blur();	  
	  return false;
	});
	
	$('#fontout').click(function(){
	  var orifont = $('body').css('font-size');
	  $('body').css('font-size',parseFloat(orifont,10)*0.9);
  	  $(this).blur();
	  return false;
	});

});
<? if(false): ?>
</script>
<? endif; ?>
<?
}

function fontsize_css()
{
  global $colors;
?>
<? if(false): ?>
<style type="text/css">
<? endif; ?>
#fontin {
  font-size:1.2em;
}
#fontres,
#fontout sup,
#fontout sub {
  font-size:1em;
}
#fontout {
  font-size:0.8em;
}
<? if(false): ?>
</style>
<? endif;
}

?>
