<?

function responsive_init()
{
  global $plugins;
  unset(
    $plugins['calendario'],
    $plugins['sitemap'],
    $plugins['fontsize'],
    $plugins['slideshow']
  );
}

function responsive_css()
{
  global $dirs;
  global $colors;
  global $dims;
  global $images;
  global $alerts;
  global $dirs;
  global $nomenu;
  global $noimgs;
?>
<? if(false): ?>
<style type="text/css">
<? endif; ?>


@media screen and (max-width: 480px) {

  #pager {
      width: 100%;
      clear: both;
      padding: 0;
      margin-top: 2em;
  }
  #homelink {
      position: absolute;
      top: .25em;
      height: 1.5em;
      float: left;
      border: none;
  }
  #title {
    padding: 0;
  }
  #content {
    padding: 4px;
  }
  #map {
    border: none;
    border-radius: 0;
  }
  #map_wrap {
      float: left;
      position: static;
      margin: 0
  }
  .leaflet-control {
      margin: 2px !important;
  }
  .thumb_title {
    position: relative;
    bottom: -1.25em;
    left: .25em;
  }
  .thumb_text {
    position: relative;
    bottom: 1.4em;
    left: 0;
    color:#fff;
    border: none;  
  }
  .thumb.active .thumb_text {
    color: #fff;
    background: none;
  }
  .thumb_wrap {
    margin: 0;
    padding: 0 .125em .125em 0;
  }

  .thumb,
  .thumb.select,
  .thumb.active {
    background: none;
    border: none;
    border-radius: 0;
    margin: 0;
    padding: 0;
    position: absolute;
  }
  .imgthumb {
      border: none;
      margin: 0;
      border-radius: 0;
  }

  /* foto page */
  #sidebar,
  .exif {
    display: none;
  }
  #fotopage_wrap {
    background: #000
  }
  #fotopage #title {
    color: #fff
  }
  #fotopage #text {
    position: fixed;
    bottom: 0;
  }

  #fotopage {
      padding-left: 0;
  }
  .foto {
      position: relative;
      z-index: 1000;
      background: none;
      border: none;
      text-align: center;
      margin: 0 auto;
      margin-bottom: 1em;
      border-radius: 0;
      max-width: 100%;
  }

  .foto .imgfoto {
    max-width: 100%;
    height: auto;
    background-color: #bbbbbb;
    margin: 0;
  }
  
  #prev2 span, #next2 span {
    visibility: hidden;
    position: absolute;
    font-size: 3em;
    border-radius: 0;
    background: rgba(255,255,255,0.6);
  }

  #fotopage .foto_close {
    z-index: 20000;
    right: 0;
    top: 0;    
    color:#fff;
    font-size: 32px;
    line-height: 20px;
  }
}

<? if(false): ?>
</style>
<? endif; ?>
<?
}

function responsive_js()
{
  global $urls;
  global $colors;
  global $dirs;
  global $masks;

?>
<? if(false): ?>
<script>
<? endif; ?>
$(function() {

    $("#fotopage").swipe({
      swipe:function(event, direction, distance, duration, fingerCount, fingerData) {

        switch(direction)
        {
          case 'left':
            $(this).find('#prev2').trigger('click');
            break;
          case 'right':
            $(this).find('#next2').trigger('click');
            break;
        }
      }
    });

});

<? if(false): ?>
</script>
<? endif;
}