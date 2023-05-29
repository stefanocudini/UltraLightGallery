<?

function lloogg_tail()  //infondo alla pagina
{
  global $admin;
  if(islocal() or $admin) return false;
?>
<script type="text/javascript">
lloogg_clientid = "29300229a00e75cd";
</script>
<script type="text/javascript" src="http://lloogg.com/l.js?c=29300229a00e75cd">
</script>
<?
}
?>
