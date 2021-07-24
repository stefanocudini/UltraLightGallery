<?
function ulg_css()
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
html {
  font-size: 100%;
}
body {
  color: <?=$colors['text']?>;
  background-color: <?=$colors['background']?>;
  font-family: Arial, Helvetica, sans-serif;
  font-size: 1em;
  padding: 0;
  margin: 0;
}
a:link, a:visited, .pulsante {
  color: <?=$colors['text']?>;
  text-decoration: none;
}
a:hover, .pulsante:hover {
  color: <?=$colors['hover']?>;
  text-decoration: none;
}
label {
 font-size: 0.8em;
 line-height: 1em;
}
input, textarea, select, button {
  border: 0.125em solid <?=$colors['border']?>;
  color: <?=$colors['text']?>;
  background-color: <?=$colors['bginput']?>;
  padding: 0.0625em;
  margin: 0.125em;
  font-family: Arial, Helvetica, sans-serif;
  font-size: 1em;
  line-height: 1em;
  border-radius: 0.4em;    
}
select {
  margin-top:-0.0625em;
}
input.pulsante {
  border: 0.125em solid <?=$colors['border']?>;
  padding: 0.0625em;
  margin:0;
}
form,h1,h2,h3,h4,p {
  margin: 0;
  padding: 0;
}
img { border: none; } /* una per tutte!! >:) */
em { font-style:normal; }

.formwrap {
  padding: 0.25em;
  margin:0;
  border-radius: 0.625em;    
}
.pulsante {
  padding: 0.125em;
  margin: 0;
  border: 0.125em solid <?=$colors['border']?>;
  background-color: <?=$colors['bginput']?>;
  font-size: 1em;
  line-height: 1em;
  text-decoration: none;
  text-transform:capitalize;
  font-weight:bold;
  white-space: nowrap;
  cursor: pointer;
  border-radius: 0.4em;
}
.pulsante img {
  vertical-align: bottom;
  opacity: 0.8;  
}
#content {
	padding: 1em;
	margin:0;
}
body#lista img {
	border:none;
	display:block;
	clear:both;
	margin-bottom:1em;
}
#title { /*h1*/
	text-transform:capitalize;
	font-size: 1.125em;
	line-height: 1.125em;
	font-weight:bold;
	float:left;
	padding: 0.25em 0;
}
#title big { font-size:1em; }
#text {
	padding: 0.25em 0;
	float:left;
	clear: both;
}
#copyright {
float:left;
clear:both;
font-size:small;
font-style:italic;
margin:0;
}

#menu {
	padding: 0;
	position: absolute;
	left:0;
	top:-6px;
	margin-right: 150px;
	float: left;
	border-bottom: 1px solid <?=$colors['border']?>;
	background: <?=$colors['background']?>;
}
#user_menu {
	padding: .5em;
	position: absolute;
	right:0;
	top:0;
	float: right;
	text-align:right;
	border-bottom: 1px solid <?=$colors['border']?>;
  background: <?=$colors['background']?>;  
	width:auto;
}
#menu a {
   font-weight:bold;
   margin: 0 .25em;
   padding-right: .25em;
   line-height: 2em;
   white-space: nowrap;
}
#menu a img {
  opacity: 0.6;
  vertical-align:middle;
}
#menu a:hover img {
  opacity: 1;
}
#user_menu .pipe,
#user_menu a {
	line-height:1.25em;
  display:block;
	float: right;
	font-weight:bold;
}

#alert {
  position: relative;
  padding: 0.3em 3.75em 0.3em 0.3em;
  margin: 0 auto;    
  border: 0.125em solid <?=$colors['hover']?>;
  background-color: <?=$colors['bghover']?>;
  text-align:center;
  border-radius: 0.4em; 
  width:80%;   
}
#alertclose {
  position:absolute;
  display:block;
  right: .125em;
  top: .125em;
  height:.7em;
  font-size:1.6em;
  line-height:.5em;
  border:0px solid red;
}
#alert_wrap {
	display:none;
	position: fixed;
	z-index:1000;
	top:2.825em !important;
	top:2.825em;
	width:100%;
	padding: 0 auto;
	margin: 0;
	height: 2em;
	text-align:center;
	opacity: 0.9;
	background-color: <?=$colors['background']?>;	
}
#alert .pulsante {
  border: 0.125em solid <?=$colors['hover']?>;
  background-color: <?=$colors['bghover']?>;
}
#homelink {
	position: absolute;
	top:5.0625em;
	height:1.5em;
	float: left;
	padding: 0.125em;
	border: 0.0625em solid <?=$colors['border']?>;
	border-left: none;
	border-radius: 0.4em;	
}
/*#alert_wrap,#content,#pager { border:1px solid red;}*/

#pager {  /* tabella che contiene: #content e #panel */
  width: 100%;
  clear:both;
  padding: 0;
  margin-top:7em;
}
#pager_left,
#pager_right {
  vertical-align: top;
  padding:0;
}
#pager_left {
  width:100%;
}
#pager_right {
  float:right;
  padding-right:-1em;
}
#pager_panel {
  margin:0;
  float:right;
  display:block;
  height:100%;
  clear:both;
}
#foot {
  clear: both;
  font-size: 0.625em;
  letter-spacing: 0.125em;
  text-align: center;
  padding: 2em;
  margin: 0 auto;
  position: relative;
  z-index:10;
}
#foot a {
  color: <?=$colors['hover']?>;
}
#foot a:hover {
  text-decoration: underline;
}
.page {
	clear:both; /*per non subire il float di #pagertab */
	vertical-align: top;
/*	overflow: hidden;*/
}
.pagefoot {
	border-bottom: 0.0625em solid <?=$colors['border']?>;
	clear:both;
	text-align:right;
	font-size:0.9em;
	color: <?=$colors['text']?>;
	padding: 1em 0.24em 0.24em 0.24em;
}
.thumbs {
	clear:both;
}

#pager_panel td {
  height: 100%;
  vertical-align: top;
  margin:0;
}
#panel_hide {
  width: 0.7em;
  height: 4em;
  display: block;
  line-height: 4em;

  margin-right:-0.625em;	
  padding-right:0.125em;
  cursor: pointer;
    
    border-top: 0.0625em solid <?=$colors['border']?>;
	line-height:1em;
	margin:0;
	height:1em;
	font-size:large;

    border-right: 0.0625em solid <?=$colors['border']?>;
	margin-right:-0.0625em;
}
#panel_hide.closed {
    border-top: 0.0625em solid <?=$colors['border']?>;
    border-left: 0.0625em solid <?=$colors['border']?>;
    border-bottom: 0.0625em solid <?=$colors['border']?>;
	line-height:2.8em;
	height:3em;
}
#panel_hide.opened {
    border-right: 0.0625em solid <?=$colors['border']?>;
}
#panel_hide big {
  font-size: 1.3em;
}

#panel {
  vertical-align: top;
  width: <?=pixem($dims['panelwidth']+30)?>em;
  border-left: 0.0625em solid <?=$colors['border']?>;
  padding: 0.5em;

}

.paneltitle {
	width: <?=pixem($dims['panelwidth'])?>em;
	height: 1.125em;
	padding: 0.25em;
	margin: 0;
	text-align:right;
	border-top: 1px solid <?=$colors['border']?>; 
	background: <?=$colors['background']?>;
	margin-bottom: 0.25em;
	font-size: 1em;
	font-weight:bold;
	white-space:nowrap;	
	cursor: pointer;
}
.paneltitle .ui-state-hover,
.paneltitle .ui-state-active {
	width: <?=pixem($dims['panelwidth'])?>em;
	background:#fff;
}

.paneltitle.selected {
	text-align: left;
	width: <?=pixem($dims['panelwidth'])?>em;
}
.paneltitle.panel-loading a {
	background: url('<?=$images['jqueryloadingcircle1']['url']?>') right top no-repeat;
	padding-right:1.25em;
}
.panelcontent {
  width: <?=pixem($dims['panelwidth']+20)?>em;
  padding: 0; /* serve soltanto a centrare il contenuto dei DD rispetto ai DT   */
  margin: 0;
  margin-bottom:1em; /* per distanziare il contenuto di DD dal DT successivo*/
  clear: both;
}

.trasp { /* utile per animare l'opacita */
  opacity: 0;
}
.trasp50 { /* utile per animare l'opacita */
  opacity: 0.5;
}
.icon {
  display: block;
  float: left;
  height: 1.25em;
  width: 1.25em;
  padding:0.125em 0 0 0.125em;
  margin: 0 0.125em 0.125em 0;
  border: 0.0625em solid <?=$colors['border']?>;
  background-color: <?=$colors['background']?>;
  opacity: 0.5;
  border-radius: 0.4em;    
}
.icon:hover {
  opacity: 1;
  border: 0.0625em solid <?=$colors['hover']?>;
}
.icon span {
  display: none;
}

.thumb { z-index: 20; }
.thumb_menu { z-index: 21; }
.pagefoot { z-index:10 }
#menu,#user_menu {z-index:30;}

.thumb_wrap {
  float: left;
  margin: 1em;
  padding: 0 1.2em 3.2em 0;
  font-size: 1em;
}
.thumb_wrap.list {
  border:none;
  width:18em;
  height:4em;
  margin:0.5em 1em;
  padding:0;
  clear:left;
  float:right;
}

.loading {
  background-color: <?=$colors['bgbox']?>;
}
.thumb_wrap.loading {
  border-radius: 1em;    
}
.thumb {
  position: absolute; /* serve solo per posizione i div che contiene(con position absolute) rispetto ad esso stesso...... ma che vordi'? */
  border: 0.125em solid <?=$colors['border']?>;
  background: <?=$colors['bgbox']?>;
  padding: 0.5em;
  margin: 0;
  border-radius: .8em;    
}

.thumb.list {
  background:none;
  border:none;
  height:4em;
  padding:0;
  width:18em;
}

.thumb_title,
.thumb_text {
  display: block; /* serve solo a thumb_title */
  font-size: 1em;
/*  height: 1.125em;*/
  height:auto;
  overflow: hidden; /*deve essere sempre hidden anche se active*/
  clear: both;
  padding: 0;
  margin: 0;
}
.thumb_title {
  height: 1.125em;
  line-height: 1.125em;
  white-space: nowrap;
  margin-top:-0.3em; /*utile se non ce thumb_title*/
}
.thumb_text {
  border: 0.0625em solid <?=$colors['bgbox']?>;
  margin-bottom:-0.3em; /*utile se non ce thumb_title*/
  border-radius: .125em;
}
.thumb.list .thumb_text {
  margin-right:3em;
}
.thumb_link {  /* non specificare mai dimensioni senno i thumb_link dei plugins si allargano */
}
.thumb_link a {
  display: block; /* non togliere mai senno l'ancora non riesce a contenere l'img */
}
.thumb .thumb_menu {
	display: none;
	position: absolute;
	left:-2.8em !important;
	left:-2.6em;  
	top:-0.125em;
	padding-left: 1em;
	width:3em;
}

/* ANIMAZIONE delle thumb al passaggio del mouse */
.thumb.active { z-index:21; }

.thumb.active .thumb_menu{ display:block; }

.thumb.active .thumb_title,
.thumb.active .thumb_text {
  height: auto;
  overflow: hidden;
  min-height: 1.125em;
  border-color: <?=$colors['border']?>;
}
.thumb.active .thumb_text {
  background-color: <?=$colors['background']?>;
}
.thumb.select {
  border-color:<?=$colors['hover']?>;
}
/* ANIMAZIONE delle thumb al passaggio del mouse */

/* ADVERTISING BOX */
#content_banner {
  width: 120px;
  vertical-align:top;
}
#foot_banner {
  text-align:center;
  vertical-align:top;  
}


/* fotopage */

#fotopage_wrap {
	display:none;
	z-index:32;
	clear:both;
	position:fixed;
	left:0;
	right: 0;
	bottom: 0;
	top: 0;
	overflow:hidden;
	background:<?=$colors['bgfotopage']?>;
	color: <?=$colors['bgtext']?>;	
}
#fotopage {
	padding-left: .5em;
}

#fotopage #content_banner {
	width: 120px;
	vertical-align:top;
	padding-right:1em;
	position:absolute;
	right:0;
	top:0;
}

.foto_wrap {
	clear:both;
	text-align:center;
	position:relative;
	margin-bottom:1em;	
}
.foto_close {
	position:absolute;
	right:15px;
	top:5px;
	padding: 0;
	padding-left:.25em;
	font-size:3em;
	z-index:35;
}
.foto {
	position:relative;
	z-index: 1000;
	background-color: <?=$colors['bgbox']?>;
	border: 1px solid <?=$colors['bgfotopage']?>;
	text-align:center;
	margin:0 auto;
	margin-bottom:1em;
	border-radius: 1em;  	
}
.imgfoto {
	background-color: <?=$colors['bgfotopage']?>;
	margin: 1em;
}
#prev2,
#next2 {
	position:absolute;
	top:0;
	bottom:0;
	width:20%;
	float:left;
	z-index:100;
}
#prev2,#prev2 span {left:0;text-align:left}
#next2,#next2 span {right:0;text-align:right}
#prev2 span,
#next2 span {
	visibility:hidden;
	position:absolute;
	top:50%;
	margin-top:-.5em;
	border-radius: .1em;
	background: <?=$colors['bgbox']?>;
  color: <?=$colors['bgfotopage']?>;  
	font-weight:bold;
	font-size: 5em;
  vertical-align: middle;
  padding-bottom: 2px;
}
#prev2:hover span,
#next2:hover span {
	visibility:visible;
	color: <?=$colors['text']?>;
}
#prev2 span:hover,
#next2 span:hover {
  color: <?=$colors['hover']?>;  
}

.foto_prev,
.foto_next {
  width: <?=pixem(min($dims['tnsizes']))?>em;
  height:auto;
  border-radius: 0.5em;    
}
.foto_prev {
	float:left;
	margin:0;
	padding:0.5em;
	position: absolute;
	top:0;
	left:0;
	background-color: <?=$colors['bgbox']?>;	
}
.foto_next {
	float:right;
	margin:0;
	padding:0.5em;
	position: absolute;
	top:0;
	right:0;
	background-color: <?=$colors['bgbox']?>;	
}
.back {
  line-height:0.5em;
}
#backnext {
  display:none;
	font-weight:bold;
	clear:both;
	text-align:center;
	padding:0;
}
#backnext big {
	font-size: 1em;
}
#prev,
#top {
	margin-right:1em;
}
/* fine fotopage */

.imgthumb {
  border: 2px solid <?=$colors['border']?>;
  margin: -1px;
  border-radius: 10px;
}



<? if(false): ?>
</style>
<? endif; ?>
<?
}  //fine function
?>
