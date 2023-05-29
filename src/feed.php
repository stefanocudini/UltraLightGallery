<?

function feed_init()
{
	global $dirs;
	global $urls;
	global $admin;
	global $public;
	global $imgfiles;
	global $imgdirs;
	global $images;
	global $index;
	global $start;
	
	
	if(!$index) return false;  
	
	$feedfile = $dirs['cache']."./rss.xml";
	
	$urls['rsspage'] = $urls['current']."?rss";
	
	$images['rssicon']['dir'] = $dirs['cache_base']."_rssicon.png";
	$images['rssicon']['url'] = $urls['cache_base']."_rssicon.png";
	
	if(isset($_GET['rss']) and ($public or $admin))
	  $start = 'feed';
}

function feed_start()
{
	global $cachetime;
	header("Content-type: text/xml");

	$cachetime = array(5,0,0);
	#feed_feedwrite();  //racchiudere in cache
	cache_exec(array(3,0,0),'feed_feedwrite');
}

function feed_head()
{
    global $urls;
    global $dirs;
	global $index;
	
	if(!$index) return false;
?>
<link rel="alternate" href="<?=$urls['rsspage']?>" type="application/rss+xml" title="Feed RSS: <?=basename($dirs['current'])?>" />
<?
}

/*function feed_foot()
{
  global $urls;
	
  if(islocal() or $urls['base']!="http://".$_SERVER['HTTP_HOST']."/photos/stefano/") return false;

//http://validator.w3.org/feed/images/valid-rss-bbulger.png
//http://validator.w3.org/feed/images/valid-rss-antipixel.png
?>
&nbsp;<a href="http://validator.w3.org/feed/check.cgi?url=http%3A//www.easyblog.it/photos/stefano/%3Frss">[Valid RSS]</a>
}#*/


function feed_menu()
{
    global $urls;
    global $images;
	global $index;
	global $imgfiles;
	global $imgdirs;
	global $images;	
	
	if(!$index) return false;
	
    if((count($imgfiles)>0 or count($imgdirs)>0))
	{
	?><a href="<?=$urls['rsspage']?>" title="Ricevi gli aggiornamenti di questo album"><img src="<?=$images['rssicon']['url']?>" alt="feed rss" /><span> Feed</span></a><?
	}
}#*/

function feed_feedwrite()
{
    global $nolist;
    global $colors;
    global $dims;
    global $dirs;
	global $urls;
	global $recache;
	global $opts;
	global $fotopage;
	global $public;
	global $admin;
	global $plugins;
	
	global $imgfiles;
	global $imgfiles_decript;
	
	$debugfeed = false;  //mostra item xml di debug in fondo

	if(!$public and !$admin) return false;

	// This is a minimum example of using the class
	require_once("feed.write.php");
	
	//Creating an instance of FeedWriter class. 
	$TestFeed = new FeedWriter(RSS2);
	
	$debugItem = $TestFeed->createNewItem();
	//nodo che contiene solo info di debug dentro un nodo xml
	  
	//Setting the channel elements
	//Use wrapper functions for common channel elements
	$TestFeed->setTitle(basename($dirs['current']));
	$TestFeed->setLink($urls['rsspage']);
	$TestFeed->setDescription(basename($dirs['current']));
	$TestFeed->setGenerator(VERSION);
	$TestFeed->setDate(filemtime('./'));
	
	//Image title and link must match with the 'title' and 'link' channel elements for valid RSS 2.0
	#$TestFeed->setImage(basename($dirs['current']),$urls['rsspage'],$urls['action'].$imgfiles[0]);

	$maxsfiles = 10;  //solo le ultime n foto
	
	$rfiles = rgetfiles();
    $rfiles = sortfiles($dirs['current'],$rfiles);
	#$files = array_slice($rfiles,0,$maxsfiles);  //prende solo le ultime $maxsfiles foto
		
	$dirs_current = $dirs['current'];
	$urls_action = $urls['current'];
	
	$debugdesc .= '<pre>'.print_r($rfiles,true).'</pre>';

  for($i=0;$i<$maxsfiles;$i++):
  
        $fil = $rfiles[$i];
		
		//Create an empty FeedItem
		$newItem = $TestFeed->createNewItem();

		$dims['tnsize'] = $maxfotosize;
		$dims['tnmargin'] = 0;
		$opts['thumbquad'] = 0;
		$opts['thumbround'] = 0;
		$opts['thumbcut'] = 0;
		$opts['thumbinterlace'] = 1;
		
		$dims['tnsize'] = back_tnsize(max($dims['tnsizes']));
		
		$dirs['current'] = $dirs_current.dirname($fil).'/';
		$urls['current'] = $urls_action.dirname($fil).'/';
		$dirs['cache'] = $dirs['current'].$opts['thumbdirname'].'/';
		$urls['cache'] = $urls['current'].$opts['thumbdirname'].'/';
		//variabili di ambiente ULG
		
		//fare solo se cambia dir rispetto alla precedente
		$imgfiles = $imgfiles_decript = array();
		$imgfiles = getfiles();
		$basenamefil = decript_filename(basename($fil));
		
		$debugdesc .= "basenamefil[$basenamefil]<br>";
		
		$title = '';
		$titles = plugins_exec('thumb_title',$basenamefil, false, true);
		foreach($titles as $tit)
			$title .= $tit['output'];
		$title = strip_tags($title);
		if(trim($title)=='') $title = $basenamefil;
		
		$debugdesc .= "title[$title]<br>";

		$fotothumb = thumburl($basenamefil);

		$pubdate = filemtime($fil);
		$fotopage = $urls['current'].sprintf($masks['fotopageurl'],$basenamefil);

		unset($thumb_title);
		$thumb_titles = plugins_exec('thumb_title',$basenamefil, false, true);
		foreach($thumb_titles as $t)
			$thumb_title .= $t['output'].'<br />';

		unset($thumb_link);
		$thumb_links  = plugins_exec('thumb_link',$basenamefil, false, true);
		foreach($thumb_links as $t)
			$thumb_link .= $t['output'].'<br />';
				
		unset($thumb_text);
		$thumb_texts = plugins_exec('thumb_text',$basenamefil, false, true);
		#foreach($thumb_texts as $t)
		$first = current($thumb_texts);
		$thumb_text = $first['output'].'<br />';
		
		#$desc = '<html><body bgcolor="#000" text="#ccc">'.trim($thumb_title).trim($thumb_link).trim($thumb_text).'<br /><br /></body></html>';
		$desc = trim($thumb_title).trim($thumb_link).trim($thumb_text);
	
		$newItem->setTitle($title);
		$newItem->setLink($fotopage);
		$newItem->setDate($pubdate);
		$newItem->setDescription($desc);
		$newItem->setGuid($fotothumb,'true');
		#$newItem->setEncloser(sprintf($masks['fotopageurl'],$fil), $length, "image/jpeg");
		
		//Now add the feed item
		$TestFeed->addItem($newItem);

  endfor;
  
  if($debugfeed){
	  $debugItem->setDescription($debugdesc);
	  $TestFeed->addItem($debugItem);
  }
  
  //OK. Everything is done. Now genarate the feed.
  $TestFeed->genarateFeed();
  
}

function feed_cache()
{
  global $images;
    
  require('feed.cache.php');

  put_contents($images['rssicon']['dir'],$icon1);
}

?>
