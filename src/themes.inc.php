<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
 <head>
   
  <title>StyleSwichter jQuery plugin - Example</title>
  
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  
  <!-- Require jQuery -->
  <script src="./jscripts/jquery.js" ></script>
  
  <!-- Require jQuery Cookie plugin -->
  <script src="./jscripts/jquery.cookie.js" ></script>
  
  <!-- Require jQuery StyleSwichter plugin -->
  <script src="./jscripts/jquery.styleswichter.js" ></script>
  
  <!-- StyleSheet: note theh title attribute -->
  <link rel="stylesheet" title="styleswichter" 
   type="text/css" media="screen" href="main.css" />

  <!-- A little auxiliar code -->
  <script>
    
    // DOM ready!
    $(function(){
      
      // Prepare options. The most important thing is that
      // you need call the StyleSwichter() plugin function
      // when the DOM is ready. The plugin find the cookie
      // and establish the appropiate style. 
      
      var options={
        linkTitle: 'styleswichter',
        cookieName: 'selected-style'
      };
      $.fn.StyleSwichter(options);
      
    });
    
    // This is an auxiliar function. You can use directly the
    // $.fn.StyleSwichter() function, but, in this example we
    // use this auxiliar function to aid.
    
    // Note that we use all the available options here. The
    // most important here are the "css path", the CSS to be
    // swichting.
    
    // The plugin save the selection in a cookie, and use this
    // after (see above) then the DOM of this page is ready.
    
    function SetStyle(aCssPath){
      var options={
        cookieDays: 30,
        cssPath: aCssPath,
        linkTitle: 'styleswichter',
        cookieName: 'selected-style'
      };      
      // Set the appropiate style
      $.fn.StyleSwichter(options);
    }
    
  </script>
  
 </head>
 <body>
  <h1>StyleSwichter jQuery plugin - Example</h1>
  <p>Please, use the bellow links to change the "style" of this page.</p>
  
  <ul>
   <li><a href="#" onclick="SetStyle('./styles/blue.css'); this.blur(); return false;">Blue</a></li>     
   <li><a href="#" onclick="SetStyle('./styles/black.css'); this.blur(); return false;">Black</a></li>
   <li><a href="#" onclick="SetStyle('./styles/white.css'); this.blur(); return false;">White</a></li>
  </ul> 
  
  <p>For developer information, take a look at the source code of this page.</p>
  <h2>More information</h2>
  <p>Take a look in the <a href="http://plugins.jquery.com/project/styleswichter" title="Plugin page in jQuery.com">plugin webpage in jQuery.com</a>. And <a href="http://www.bitacora.davidesperalta.com/" title="Visit my weblog">visit my weblog</a>.</p>
  <p>To download the plugin and this example <a href="./styleswichter.zip" title="Download jQuery StyleSwichter plugin">you can use this link</a>.</p>
  <p>
   <small>&copy; 2008 StyleSwichter jQuery plugin - <a href="http://www.bitacora.davidesperalta.com/" title="Visit my weblog">David Esperalta</a></small>  
  </p>
 </body>
</html>