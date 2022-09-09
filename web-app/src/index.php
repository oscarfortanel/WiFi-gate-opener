<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="fonts/roboto.css">
	<title>Gate</title>
</head>
<body>
	<?php
		session_start();

		$formHTML ='
		<section class="main">
			<div class="container">
				<h1 class="title">Login</h1>
				<form action="" method="post">
					<label for="userName">Username</label>
					<input class="text-input" type="text" name="userName" id="uName">
					<label for="password">Password</label>
					<input class="text-input" type="password" name="password" id="pw">
					<input class="button-input" type="submit" value="Login">
				</form>
			</div>
		</section>
		';

		

		require_once dirname(__FILE__).'/includes/sessionOps.php';

		$userSession = new SessionOps();

		if(isset($_POST['logOut'])){
			echo 'Post log';
			$userSession->logOut();
			header("Refresh:0");
		}

		function displayFormLogOut($uName){
			echo '
			<section class="main">
				<div class="user">
					<p>Welcome, ' . $uName .'!</p>
					<form action="" method="POST">
						<input type="hidden" name="logOut" value="logout">
						<input type="submit" value="logout">
					</form>
					<form id="action-form" action="" method="POST">
						<input type="hidden" name="action" value="action">
					</form>
				</div>
				<div class="container-button" id="action-button">
			
				</div>
			</section>
			';
		}

		if(isset($_COOKIE['sT'])){
			if(isset($_SESSION['uName'])){
				//echo 'Welcome, ' . $_SESSION['uName'];
				displayFormLogOut($_SESSION['uName']);

				if (isset($_POST['action'])){
					$userSession->clickAction();
				}
			}else{
				if($userSession->verifySession()){
					//echo 'Welcome, ' . $_SESSION['uName'];
					displayFormLogOut($_SESSION['uName']);
				}else{
					session_destroy();
					setcookie('sT', null, -1, '/');
					echo $formHTML;
				}
			}
		}

		if(!isset($_COOKIE['sT'])){
			if(isset($_POST['userName']) && isset($_POST['password'])){
				if($_POST['userName'] != "" && $_POST['password'] != ""){
					if($userSession->logIn($_POST['userName'], $_POST['password'])){
						//echo 'Welcome, ' . $_SESSION['uName'];
						displayFormLogOut($_SESSION['uName']);
						$_POST = array();
					}else{
						echo 'Could not sign you in.';
					}
				}
			}else{
				echo $formHTML;
			}
		}
	?>
	<script>
    	if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    	}

		button = document.getElementById("action-button");
		button.addEventListener("click", () => {
			document.getElementById("action-form").submit();
		})
	</script>
</body>
</html>