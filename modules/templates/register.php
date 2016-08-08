<!DOCTYPE HTML>
<html>

	<head>	
		
		<meta http-equiv="content-type" content="text/html;charset=utf-8">
		<link rel="stylesheet" type="text/css" media="all" href="modules/styles/login.style.css">
  		<link rel="stylesheet" type="text/css" media="all" href="extensions/jquery.alerts/jquery.alerts.css">
	    <title>Register</title>
	    
  	</head>
  	<body>
  	
  		<div id="hidden"></div>

		<table>
			<tr>
				<td>
					<form method="post" action="">
			  			<fieldset>
			  				<legend>Register</legend>
				  			<dl>
				  				<dt><label for="username">Username:</label></dt>
								<dd><input type="text" id="username" name="username" maxlength="255" value=""></dd>
				  				<dt><label for="email">Email:</label></dt>
								<dd><input type="text" id="email" name="email" maxlength="255" value=""></dd>
								<dt><label for="password">Password:</label></dt>
								<dd><input type="password" id="password" name="password" maxlength="255" value=""></dd>
								<dt><label for="password_2">Retype Password:</label></dt>
								<dd><input type="password" id="password_2" name="password_2" maxlength="255" value=""></dd>
							</dl>
							<input type="submit" value="Register" id="register" />
						</fieldset>
					</form>
					<footer>
						<a href="index.php?module=bookmarks&page=login" alt="Login" title="Login">Login</a>
					</footer>
				</td>
			</tr>
		</table>
  		
  		<script src="extensions/jquery-2.1.4.min.js" type="text/javascript"></script>
		<script src="extensions/jquery.alerts/jquery.alerts.js" type="text/javascript"></script>
  		<script src="extensions/sha256.js" type="text/javascript"></script>
  		<script type="text/javascript">
  		
  			$(document).ready( function() {

  				$('#username').focus();
  				
  				$('body').on('submit', 'form', function(e) {
	
					e.preventDefault();
					var user = $('#username').val().trim();
					var mail = $('#email').val().trim();
					var password = $('#password').val().trim();
					var password2 = $('#password_2').val().trim();
	  					
  					if(user === '' 
  						|| mail === ''
  						|| password === ''
  						|| password2 === '') {

  						jAlert('Please fill out the required fields', 'Error');
  						// TODO detect which is the first empty field and then focus that particular field
  						$('#username').focus();

  						return;
  					}

  					if( password !== password2 ) {
  						jAlert('The Passwords don\'t match', 'Error');
  						$('#password').focus();

  						return;
  					}

  					
					$('#hidden').load('index.php?module=universal&ajax=1&page=register_2&username=' + encodeURIComponent(user) + '&mail=' + encodeURIComponent(mail) + '&password=' + encodeURIComponent(CryptoJS.SHA256(password)), function( result ) {
							
  						if(result === '1') {

  							jAlert('Registration Complete. Please Login.', 'Success');
  							setTimeout(function() {
	  							window.location.href = "index.php?module=bookmarks";
  							}, 1000);
  							
  						} else
  							jAlert(result, 'Error');
  							
					});
	
				}); 
  			
  			});
  		
  		</script>
  		
  	</body>
  	
</html>
