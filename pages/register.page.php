<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/user.service.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/storage.service.php");

$user = new UserService();
$storage = new StorageService();

if (!empty($_POST)) {
	$pfp_path = "";
	if ($_FILES['pfp']['name'] !== "") $pfp_path = $storage->Upload($_FILES['pfp'], "pfp");

	$register_results = $user->Register($_POST['username'], $_POST['password'], $pfp_path);

	if ($register_results) {
		session_start();
		$_SESSION['username'] = $_POST['username'];
		header("Location: /@chan/index.php?p=profile");
	} else header("Location: /@chan/index.php?p=register&invalid");
}
?>

<?php
if (isset($_GET['invalid'])) {
	echo "
		<div class='alert alert-warning alert-dismissible fade show' role='alert'>
			<i class='bi bi-exclamation-triangle-fill'></i>
			Username already in use.
			<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
		</div>
	";
}
?>

<div class="d-flex flex-column justify-content-center align-items-center form-container">
	<h1 class="mb-5"><i class="bi bi-person-plus"></i> Register</h1>
	<form action="" method="post" enctype="multipart/form-data" class="w-100" id="form" novalidate>
		<label for="username-input" class="form-label">
			Username
			<small>( Only letters, numbers, and _ or - )</small>
		</label>
		<div class="input-group mb-3">
			<span class="input-group-text" id="basic-addon1">@</span>
			<input type="text" name="username" class="form-control" id="username-input" style="border-radius: 0 0.25rem 0.25rem 0;" placeholder="Enter a username" autocomplete="on">
			<div class="invalid-feedback">Please enter a valid username.</div>
		</div>

		<label for="password-input" class="form-label">
			Password
			<small>(At least 8 characters long)</small>
		</label>
		<div class="input-group mb-3">
			<input type="password" name="password" class="form-control" id="password-input" placeholder="Enter a password" aria-label="Enter a password" aria-describedby="toggle-password-btn" autocomplete="on">
			<button class="btn btn-outline-secondary" type="button" id="toggle-password-btn" style="box-shadow: none; border-radius: 0 0.25rem 0.25rem 0;" onclick="togglePassword()" title="Show password"><i class="bi bi-eye"></i></button>
			<div class="invalid-feedback">Please enter a valid password.</div>
		</div>

		<label for="confirm-password-input" class="form-label">
			Confirm
			<small>(Enter your password again)</small>
		</label>
		<div class="mb-3">
			<input type="password" class="form-control" id="confirm-password-input" placeholder="Enter a password" aria-label="Enter a password" aria-describedby="toggle-password-btn" autocomplete="on">
			<div class="invalid-feedback">Passwords don't match.</div>
		</div>

		<div class="mb-3">
			<label for="file-input" class="form-label">
				Upload a profile picture
				<small>(Optional. The file must be a jpg, jpeg, or png smaller than 2MB)</small>
			</label>
			<input class="form-control" name="pfp" type="file" id="file-input">
			<div class="invalid-feedback" id="invalid-extension-alert">File is either too big or is of the wrong type.</div>
		</div>

		<div class="d-flex flex-column justify-content-center flex-md-row w-100">
			<button type="button" onclick="validateForm(false)" class="btn btn-primary h-100 w-md-50 w-100 mb-1 me-md-1" id="register-btn">Register</button>
			<a href="/@chan/index.php?p=login" class="btn btn-outline-primary h-100 w-md-50 w-100 m-none ms-md-1" id="login-btn">Log in</a>
			<button class="btn btn-primary w-50 d-none" id="loading-btn" type="button" disabled>
				<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
				Signing you up...
			</button>
		</div>
	</form>
</div>