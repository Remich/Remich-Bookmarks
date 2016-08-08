<!DOCTYPE HTML>
<html>

	<head>	
		
		<meta http-equiv="content-type" content="text/html;charset=utf-8">
		<link rel="stylesheet" type="text/css" media="all" href="modules/styles/login.style.css">
  		<link rel="stylesheet" type="text/css" media="all" href="extensions/jquery.alerts/jquery.alerts.css">
	    <title>Login</title>
	    
  	</head>
  	<body>
  	
  		<div id="hidden"></div>

		<table>
			<tr>
				<td>
					<form method="post" action="">
			  			<fieldset>
			  				<legend>Login</legend>
				  			<dl>
				  				<dt><label for="username">Username:</label></dt>
								<dd><input type="text" id="username" name="username" maxlength="255" value=""></dd>
								<dt><label for="password">Password:</label></dt>
								<dd><input type="password" id="password" name="password" maxlength="255" value=""></dd>
							</dl>
							<input type="submit" value="Submit" id="login" />
						</fieldset>
					</form>
					<footer>
						<a href="index.php?module=universal&page=register" alt="Register" title="Register">Register</a>
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
					var pass = $('#password').val().trim();
	  					
  					if(user == '' || pass == '') jAlert('Please fill out the required fields', 'Error');
  					else {
  					
  						$('#hidden').load('index.php?module=universal&ajax=1&page=login&username=' + encodeURIComponent(user) + '&password=' + encodeURIComponent(CryptoJS.SHA256(pass)), function( result ) {
	  							
	  						if(result == 1) 
	  							window.location = "index.php?module=bookmarks";
	  						else
	  							jAlert(result, 'Error');
	  							
						});
						
  					}
	
				}); 
  			
  			});
  		
  		</script>
  		
  	</body>
  	
</html>
