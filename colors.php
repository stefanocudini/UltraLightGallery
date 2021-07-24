<?

function colors_getcolors($fotofile)
{
	require_once("colors.inc.php");
	$ex = new GetMostCommonColors();
	$ex->image = $fotofile;
	return array_keys($ex->Get_Color());
}

/*
function colors_thumb_text($fotofile)
{
  if(is_dir($fotofile)) return false;
	?><div class="colorspicks"><?
	for ($i = 0; $i <= $how_many; $i++)
	{
		echo '<div style="background-color:#'.$colors[$i].';">#'.$colors[$i].'</div>';
	}
	?></div><?
  return 'Colori';
} # */


function colors_thumb()
{
	foreach(getfiles() as $file)
	{
	  $cols = colors_getcolors(thumburl($file));
		?><div style="background-color: #<?=$cols[0]?>">#<?=$cols[0]?></div><?
	}
} # */
?>