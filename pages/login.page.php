<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/user.service.php");
$user = new UserService();

if (!empty($_POST)) {
	$login_result = $user->Login($_POST['username'], $_POST['password']);

	if ($login_result) {
		session_start();
		$_SESSION['username'] = $_POST['username'];
		header("Location: /@chan/index.php?p=profile");
	}
}
?>

<div class="d-flex flex-column justify-content-center align-items-center form-container">
	<?php
	if (isset($_GET['unauthenticated'])) {
		echo "
			<div class='alert alert-danger alert-dismissible fade show w-100' role='alert'>
				<i class='bi bi-exclamation-triangle-fill'></i>
  				You are not logged in!
  				<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
			</div>
			";
	}

	if (isset($_GET['nouser'])) {
		echo "
			<div class='alert alert-danger alert-dismissible fade show w-100' role='alert'>
				<i class='bi bi-exclamation-triangle-fill'></i>
  				User does not exist.
  				<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
			</div>
			";
	}

	if (isset($_GET['password'])) {
		echo "
			<div class='alert alert-danger alert-dismissible fade show w-100' role='alert'>
				<i class='bi bi-exclamation-triangle-fill'></i>
  				Wrong password.
  				<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
			</div>
			";
	}
	?>

	<h1 class="mb-5"><i class="bi bi-box-arrow-in-right"></i> Log in</h1>
	<form action="" method="post" class="w-100" id="form">
		<label for="username-input" class="form-label">Username</label>
		<div class="input-group mb-3">
			<span class="input-group-text" id="basic-addon1">@</span>
			<input type="text" name="username" class="form-control" id="username-input" style="border-radius: 0 0.25rem 0.25rem 0;" placeholder="Enter your username" autocomplete="on">
			<div class="invalid-feedback">Please enter a valid username.</div>
		</div>

		<label for="password-input" class="form-label">Password</label>
		<div class="input-group mb-3">
			<input type="password" name="password" class="form-control" id="password-input" placeholder="Enter your password" aria-label="Enter  your password" aria-describedby="toggle-password-btn" autocomplete="on">
			<button class="btn btn-outline-secondary" type="button" id="toggle-password-btn" style="border-radius: 0 0.25rem 0.25rem 0;" onclick="togglePassword()" title="Show password"><i class="bi bi-eye"></i></button>
			<div class="invalid-feedback">Please enter a valid password.</div>
		</div>

		<div class="d-flex flex-column flex-md-row w-100">
			<button type="button" onclick="validateForm(true)" class="btn btn-primary h-100 w-md-50 w-100 mb-1 me-md-1" id="register-btn">Log in</button>
			<a href="/@chan/index.php?p=register" class="btn btn-outline-primary h-100 w-md-50 w-100 m-none ms-md-1" id="login-btn">Register</a>
			<button class="btn btn-primary w-50 d-none" id="loading-btn" type="button" disabled>
				<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
				Logging you in...
			</button>
		</div>
	</form>
</div>