<?

function exif_init()
{
  global $masks;
  global $dirs;

  $masks['exiffile']= $dirs['cache']."_exif_%s".".txt";  //nome del file che contiene dait exif per il file immagine %s
}

function exif_content_bottom()
{
  global $fotopage;
  
  $label = 'Dati di Scatto';
	

  if(!$fotopage) return false;
    
	?><div class="exif"><h4><?=$label?></h4><?
	
	  exif_getexifcache($_GET['foto'].'.jpg');

	?></div><?

    return $label;
}

function exif_getdati($filename)
{
  require_once(dirname(__FILE__).'/exif.inc.php');

  $res = read_exif_data_raw(getcript_filename($filename), 0);  
#  $ex['Orientazione'] = $res['IFD0']['Orientation'];
  $ex['ISO'] = $res['SubIFD']['ISOSpeedRatings'];
  $ex['Tempo'] = $res['SubIFD']['ExposureTime'];
  $ex['Apertura'] = $res['SubIFD']['FNumber'];
  $ex['Esposizione'] = $res['SubIFD']['ExposureBiasValue']!='0 EV' ? ($res['SubIFD']['ExposureBiasValue']<0?'-':'+').'1/'.abs(10*round($res['SubIFD']['ExposureBiasValue'],1)) : '';  

  $ex['Focale'] = $res['SubIFD']['FocalLength'];
  
  if(preg_match("#([0-9]{4}):([0-9]{2}):([0-9]{2}) ([0-9]{2}:[0-9]{2}):[0-9]{2}#",$res['SubIFD']['DateTimeOriginal'],$r))
    $ex['Ora'] = $r[3].'/'.$r[2].'/'.$r[1].' '.$r[4];
  
  $ex['Fotocamera'] = str_replace('DIGITAL','',trim($res['IFD0']['Model']));
#  $ex['Flash'] = $res['SubIFD']['MakerNote']['Settings 1']['Flash'];	
#  $ex['Obiettivo'] = $res['SubIFD']['MakerNote']['Settings 1']['ShortFocalLength'].'-'.$res['SubIFD']['MakerNote']['Settings 1']['LongFocalLength'];
#  $ex['Qualit'] = $res['SubIFD']['MakerNote']['Settings 1']['Quality'];	
  return $ex;
}

function exif_putexifcache($fotofile)  //genera file di cache con i dati exif
{
  global $masks;
  global $recache;
    
  $filedata = sprintf($masks['exiffile'], $fotofile);  //file di cache che contiene i dati exif
    
    if(is_file($filedata))
      @unlink($filedata);
    
    $edat = exif_getdati($fotofile);
    
    foreach($edat as $k=>$e)
      if(empty($e)) unset($edat[$k]);
    
    if(count($edat)>0)
    {
      $data = '';
      foreach($edat as $k=>$e)
          $data .= $e!=''?"$k: $e<br />":'';
      put_contents($filedata,$data);
    }
    else
      return false;

    return true;
}

function exif_getexifcache($fotofile)  //genera file di cache con i dati exif
{
    global $masks;
    global $recache;
    global $dirs;

  $filedata = sprintf($masks['exiffile'], $fotofile);

    if($recache or !is_file($filedata))
      exif_putexifcache($fotofile);  

    if(is_file($filedata))
      get_contents($filedata,true);  //stampa invece che ritornare
    else
      echo '&nbsp';
}

function exif_css()
{
    global $dims;
    global $colors;
    global $images;
?>
.exif {
    text-align: left;
    font-size: 0.75em;
    font-size: 0.9em;
    font-size: small;
}
#fotopage .exif {
  z-index: 10;
  position:absolute;
  bottom:.25em;
  left:0;
}
<?
}

?>
