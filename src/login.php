<?

//ulgStartSession();
//spostato in ulg.php

if( isset($_SESSION['username']) )
{

	if( isset($users[ $_SESSION['username'] ]) )
	//controlla che l'utente della sessione attiva esiste nell'elenco utenti
	{
		$username = $_SESSION['username'];  //imposta utente attivo
		$admin = true;  //sblocca area amministrativa
		//$admin deve essere settato a true PRIMA che vengano inclusi gli altri plugins
		//xke alcune funzioni vengono definite dentro un if($admin){ ... }
	}
	if(isset($_GET['logout']))
		login_out();
}

function login_init()
{
	global $users;
	global $admin;
	global $urls;
	
	if(isset($_GET['logout']))
	  $urls['action'] = str_replace('logout','',$urls['action']);  //senno viene trascinato su tutti gli urls
}

function login_out()
{
	global $admin;
	global $alerts;
	global $users;
	global $urls;
	global $username;
#	global $recacheforce;

	ulgDelSession();
	
	#$admin = false;
	#$recacheforce = true; //forse inutile ma lasciamolo
	//questi 2 righe sono inutili con il location
	
#	$_SESSION['recacheforce'] = true;
	header("Location: ".$urls['current']);  //PER NON GENERARE LA PAGINA CON DATI POST VIENE RINVIATA A UN'ALTRA
	exit(0);
}

function login_in($user)
{
	global $admin;
	global $alerts;
	global $users;
	global $urls;
	global $username;
#	global $recacheforce;

	ulgStartSession(true);//rigenera session id
	
	$_SESSION['username'] = $user;
    
	set_cookie('panelclose','false');//riapre pannello per utenti sloggati
    
	#error_log('ulg_login:'.$_SERVER['REMOTE_ADDR'].' '.$user.' "'.$_SERVER['REQUEST_URI'].'"');

	header('Location: '.$urls['current']);  //PER NON GENERARE LA PAGINA CON DATI POST VIENE RINVIATA A UN'ALTRA
	exit(0);
}

function login_submit()
{
	global $admin;
	global $alerts;
	global $users;
	global $urls;
	global $username;
#	global $recacheforce;
    
    if(isset($_POST['login'])) //Effettuo il Login
    {
        $user = $_POST['username'];
        $pass = $_POST['password'];
        
        if(!empty($user) and !empty($pass))
        {
            $u = $p = false;
            
            if($users[$user]) $u = true;
            else $alerts[]= 'Username Errato';
            
            if($u and $pass==$users[$user]['password']) $p = true;
            elseif($u) $alerts[]= 'Password Errata';
    
            if($u and $p)  //login corretto
				login_in($user);
			else
				error_log('ulg_error_login: '.$_SERVER['REMOTE_ADDR'].' '.$user.' '.$pass.' "'.$_SERVER['REQUEST_URI'].'"');
        }
        else
          $alerts[]= 'Digita Username e Password';
    }
	elseif(isset($_POST['logout'])) //Effettuo il Logout
    {
        login_out();
    }
}


function login_user_menu()
{
  global $admin;
  global $public;  
  global $urls;
  global $username;  
  
#  if(!$public and !$admin) return false; //tanto ce login_content in questo caso

?>
    <form id="loginform" action="<?=$urls['action']?>" method="post">
    <div><input type="hidden" name="submit" value="login" /></div>
<?
    if($admin)
    {
      echo 'Ciao! <b>'.$username.'</b> &nbsp;&nbsp;';
      ?><input type="submit" name="logout" value="Esci" class="pulsante" /><?
      $label = "Esci";
    }
    else
    {
		?>
		<div id="loginfields">
		<a href="#" id="hidelogin">&times;</a>
		
			<label>Username </label>
			<input type="text" id="loginusername" name="username" value="" size="8" title="Nome Utente" />&nbsp;
		
			<label>Password </label>
			<input type="password" id="loginpassword" name="password" value="" size="8" title="Password" />&nbsp;
		
			<input type="submit" id="loginsubmit" name="login" value="Entra" class="pulsante" />&nbsp;
		
		</div>
		<a id="accedi" href="#" title="Amministrazione della Gallery">Accedi</a>
		<?
      $label = 'Accedi';
    }
?>
  </form>
<?
  return $label;
}

function login_head_js()  //questa parte javascript cambia per ogni pagina
{
	global $username;
?>
<script>
ULG.opts.username = '<?=$username?>';
</script>
<?
}  //questa parte javascript cambia per ogni pagina



function login_js()
{
?>
<? if(false): ?>
<script>
<? endif; ?>

$(document).ready(function() {

    $('#loginfields').hide();
	$("#accedi").show();

	$('#hidelogin').click(function(){
		$('#loginfields').hide();
		$("#accedi").show();				
		return false;
	});

    $("#accedi").click(function() {
	    $(this).hide();
		$('#loginfields').show();
		$('#loginusername').focus();
		//non animare xke sviene! :)
		//$('#loginfields').animate({width:'auto'}, "slow", function() {
		//	$('#loginusername').focus();
		//});		
		return false;
    });

    $("#loginform").submit(function() {
		if( $('#loginusername').val()=='' || $('#loginpassword').val()=='' )
		{
			ulgalert('Inserisci Username e Password');
			return false;
		}
    });

});
<? if(false): ?>
</script>
<? endif; ?>
<?
}

function login_css()
{
    global $colors;
    global $dims;
    global $urls;
?>
<? if(false): ?>
<style type="text/css">
<? endif; ?>
#loginform {
	float:right;
}
#loginfields {
	float: right;
	text-align: right;
	width: auto;
	white-space:nowrap;
}
#accedi {
	display:none;
}
#hidelogin {
	float:left;
	vertical-align:top;
	font-size:1.5em;
	line-height:1em;
	margin-right:.5em;
}
#loginbox {
	margin:3em auto;
	text-align:center;
}
#loginbox .pulsante {
	margin:1em 0 0 1em !important;
	margin:1em 0 0 2.5em;
}
<? if(false): ?>
</style>
<? endif; ?>
<?
}

?>
