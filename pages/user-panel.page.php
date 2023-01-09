<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/user.service.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/storage.service.php");

if (!isset($_SESSION)) session_start();

$user = new UserService();
$storage = new StorageService();

if (isset($_SESSION['username'])) {
	$user_data = $user->GetUserDataByUsername($_SESSION['username']);

	if (isset($_POST['action'])) {
		switch ($_POST['action']) {
			case "change-pfp":
				if ($_FILES['pfp']['name'] !== "") {
					$pfp_path = $_SERVER["DOCUMENT_ROOT"] . "/@chan/_storage/profile-pictures/" . $user_data['pfp'];

					if ($user_data['default_pfp'] === 0) unlink($pfp_path);

					$pfp_path_new = $storage->Upload($_FILES['pfp'], "pfp");
					$pfp_change_result = $user->ChangePfp($user_data['username'], $pfp_path_new);

					if ($pfp_change_result) header("Refresh:0; url=/@chan/index.php?p=user-panel&pfp_success");
				}
				break;

			case "change-password":
				$password_change_result = $user->ChangePassword($user_data['username'], $_POST['password'], $_POST['new_password']);

				if ($password_change_result) header("Refresh:0; url=/@chan/index.php?p=user-panel&password_success");
				break;

			case "change-username":
				$username_change_result = $user->ChangeUsername($user_data['username'], $_POST['new_username']);

				if ($username_change_result) {
					$_SESSION['username'] = $_POST['new_username'];
					header("Refresh:0; url=/@chan/index.php?p=user-panel&username_success");
				}
				break;

			case "delete-user":
				if ($user->DeleteUser($user_data['id'], $_POST['user_to_delete'])) {
					header("Location: /@chan/logout.php");
				}
				break;
		}
	}
} else header("Location: /@chan/index.php?p=login&unauthenticated");
?>

<div class="w-100 d-flex flex-column align-items-center bg-light border-start border-end" style="min-height: 87.9vh">
	<header class="container border-bottom mb-2">
		<h3 class="py-2 px-2"><?php echo $_SESSION['username']; ?> / Panel</h3>
	</header>

	<div class="form-container py-3 px-2">
		<h4>Change your profile picture</h4>
		<div>
			<?php
			if (isset($_GET['pfp_success'])) {
				echo "
						<div class='alert alert-success alert-dismissible fade show w-100' role='alert'>
							<i class='bi bi-check-square-fill'></i>
							Profile picture changed successfully.
							<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
						</div>
					";
			}
			?>
			<div class="d-flex pb-3 border-bottom">
				<?php echo "<img src='/@chan/_storage/profile-pictures/" . $user_data['pfp'] . "' class='img-crop img-thumbnail' alt='Profile picture'>"; ?>

				<form action="" method="post" enctype="multipart/form-data" class="w-100 ms-3" id="pfp-form" novalidate>
					<div class="mb-3">
						<label for="file-input" class="form-label"><small>The file must be a jpg, jpeg, or png smaller than 2MB</small></label>
						<input class="form-control" name="pfp" type="file" id="file-input">
						<div class="invalid-feedback" id="invalid-extension-alert">Please select a valid file.</div>
					</div>

					<input type="hidden" name="action" value="change-pfp">
					<button type="button" class="btn btn-primary w-100" id="change-pfp-btn">Upload</button>
				</form>
			</div>
		</div>

		<h4 class="mt-2">Change your password</h4>
		<form action="" method="post" class="w-100 pb-3 border-bottom" id="password-form" novalidate>
			<?php
			if (isset($_GET['wrong_password'])) {
				echo "
						<div class='alert alert-danger alert-dismissible fade show w-100' role='alert'>
							<i class='bi bi-exclamation-triangle-fill'></i>
  							Wrong password!
  							<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
						</div>
					";
			}

			if (isset($_GET['same_password'])) {
				echo "
						<div class='alert alert-danger alert-dismissible fade show w-100' role='alert'>
							<i class='bi bi-exclamation-triangle-fill'></i>
  							You can't have the same password again.
  							<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
						</div>
					";
			}

			if (isset($_GET['password_success'])) {
				echo "
						<div class='alert alert-success alert-dismissible fade show w-100' role='alert'>
							<i class='bi bi-check-square-fill'></i>
  							Password changed successfully.
  							<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
						</div>
					";
			}
			?>
			<label for="password-input" class="form-label">Current password</label>
			<div class="input-group mb-3">
				<input type="password" name="password" class="form-control" id="password-input" placeholder="Enter a password" aria-label="Enter a password" aria-describedby="toggle-password-btn" autocomplete="on">
				<button class="btn btn-outline-secondary" type="button" id="toggle-password-btn" style="box-shadow: none; border-radius: 0 0.25rem 0.25rem 0;" onclick="togglePassword()" title="Show password"><i class="bi bi-eye"></i></button>
				<div class="invalid-feedback">Please enter your current password.</div>
			</div>

			<label for="confirm-password-input" class="form-label">
				New password
				<small>( Can't be the same as your current password and must be at least 8 characters long )</small>
			</label>
			<div class="mb-3">
				<input type="password" name="new_password" class="form-control" id="confirm-password-input" placeholder="Enter a password" aria-label="Enter a password" aria-describedby="toggle-password-btn" autocomplete="on">
				<div class="invalid-feedback">Please enter a valid password.</div>
			</div>

			<input type="hidden" name="action" value="change-password">
			<button type="button" class="btn btn-primary w-100" id="change-password-btn">Change password</button>
		</form>

		<h4 class="mt-2">Change your username</h4>
		<form action="" method="post" class="w-100 border-bottom pb-3" id="username-form" novalidate>
			<?php
			if (isset($_GET['username_exists'])) {
				echo "
						<div class='alert alert-danger alert-dismissible fade show w-100' role='alert'>
							<i class='bi bi-exclamation-triangle-fill'></i>
  							That username is already in user.
  							<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
						</div>
					";
			}

			if (isset($_GET['username_success'])) {
				echo "
						<div class='alert alert-success alert-dismissible fade show w-100' role='alert'>
							<i class='bi bi-check-square-fill'></i>
  							Username changed successfully.
  							<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
						</div>
					";
			}
			?>

			<label for="username-input" class="form-label">
				New username
				<small>( Only letters, numbers, and _ or - )</small>
			</label>
			<div class="input-group mb-3">
				<span class="input-group-text" id="basic-addon1">@</span>
				<input type="text" name="new_username" class="form-control" id="username-input" style="border-radius: 0 0.25rem 0.25rem 0;" placeholder="Enter a username" autocomplete="on">
				<div class="invalid-feedback">Please enter a valid username.</div>
			</div>

			<input type="hidden" name="action" value="change-username">
			<button type="button" class="btn btn-primary w-100" id="change-username-btn">Change username</button>
		</form>

		<h4 class="mt-2">Delete your account</h4>
		<form action="" method="post" class="w-100" id="delete-user-form" novalidate>
			<?php
			if (isset($_GET['wrong_username'])) {
				echo "
						<div class='alert alert-danger alert-dismissible fade show w-100' role='alert'>
							<i class='bi bi-exclamation-triangle-fill'></i>
  							Wrong username.
  							<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
						</div>
					";
			}
			?>

			<label for="delete-user-username-input" class="form-label">Username</label>
			<div class="input-group mb-3">
				<span class="input-group-text" id="basic-addon1">@</span>
				<input type="text" name="user_to_delete" class="form-control" id="delete-user-username-input" style="border-radius: 0 0.25rem 0.25rem 0;" placeholder="Enter a username" autocomplete="on">
				<div class="invalid-feedback">Please enter your username.</div>
			</div>

			<input type="hidden" name="action" value="delete-user">
			<button type="button" class="btn btn-danger w-100 disabled" id="delete-user-btn"><i class="bi bi-exclamation-triangle"></i> DELETE YOUR ACCOUNT</button>
		</form>
	</div>
</div>