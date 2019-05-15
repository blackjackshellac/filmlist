<!doctype html>
<html lang="en">
  <head>
	<?php
		include 'head.php';

	if (isset($_COOKIE['auth'])) {
		$auth=$_COOKIE['auth'];
		$uid=$auth['id'];
		$hash=$auth['hash'];
		header("Location: main.php");
		exit(1);
	}

		require 'config.php';

		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			/*
			echo "<pre>";
			print_r($_POST);
			echo "</pre>";
			 */

			$username = $_POST['username'];
			$password = $_POST['password'];

			$sql="select id,password from auth where username=?";

			if ($stmt = $db->prepare($sql)) {

				/* bind parameters for markers */
				$stmt->bind_param("s", $username);

				/* execute query */
				$stmt->execute();

				$result=$stmt->get_result();
				$row = $result->fetch_assoc();
				/* close statement */
				$stmt->close();
				if (count($row) == 2) {
					$uid = $row['id'];
					$password_hash = $row['password'];
					if (empty($password_hash)) {
						// set password
						$options = [ $cost => 10 ];
						$password_hash = password_hash($password, PASSWORD_DEFAULT, $options);

						$sql = "UPDATE auth SET password=? WHERE id=?";
						if ($stmt = $db->prepare($sql)) {
							$stmt->bind_param("si", $password_hash, $uid);
							$stmt->execute();
							$result = $stmt->get_result();
							$stmt->close();
						}
					}

					// validate password hash
					if (password_verify($password, $password_hash)) {
						setcookie("auth[id]", $uid, time()+3600, "/");
						setcookie("auth[hash]", $password_hash, time()+3600, "/");
					} else {
						$error = "Failed to verify password for user ".$username;
					}
				} else {
					// user not found
					$error="username ".$username." not found";
				}
			}
		}
	?>


    <!-- Custom styles for this template -->
    <link href="/filmlist/css/login.css" rel="stylesheet">
  </head>
  <body class="text-center">
	<form class="form-signin" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
	  <!-- <img class="mb-4" src="/docs/4.3/assets/brand/bootstrap-solid.svg" alt="" width="72" height="72"> -->
	  <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
	  <label for="inputEmail" class="sr-only">Email address</label>
	  <input name="username" type="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
	  <label for="inputPassword" class="sr-only">Password</label>
	  <input name="password" type="password" id="inputPassword" class="form-control" placeholder="Password" required>
	  <div class="checkbox mb-3">
		<label>
		  <input name="remember" type="checkbox" value="true"> Remember me
		</label>
	  </div>
	  <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
		<?php
			if (!empty($error)) {
				echo '<small id="passwordHelp" class="text-danger">';
				echo $error;
				echo "</small>";
			}
		?>
	  <p class="mt-5 mb-3 text-muted">&copy; One Guy Coding 2019</p>
	</form>
</body>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

<?php
		echo "<pre>";
		print_r($_COOKIE['auth']);
		echo "</pre>";
	if (isset($_COOKIE['auth'])) {
		$auth=$_COOKIE['auth'];
		$uid=$auth['id'];
		$hash=$auth['hash'];
		header("Location: main.php");
		exit(1);
	}
?>

</html>

