<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/channel.service.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/user.service.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/storage.service.php");

if (!isset($_SESSION)) session_start();

$channel = new ChannelService();
$user = new UserService();
$storage = new StorageService();

if (isset($_SESSION['username'])) {
	if (isset($_GET['at'])) {
		$channel_data = $channel->GetChannelDataByName($_GET['at']);

		if (isset($_POST['action'])) {
			switch ($_POST['action']) {
				case "change-banner":
					if ($_FILES['banner']['name'] !== "") {
						$banner_path = $_SERVER["DOCUMENT_ROOT"] . "/@chan/_storage/channel-banners/" . $channel_data['banner'];

						unlink($banner_path);

						$banner_path_new = $storage->Upload($_FILES['banner'], "banner");
						$banner_change_result = $channel->ChangeBanner($channel_data['name'], $banner_path_new);

						if ($banner_change_result) header("Refresh:0; url=/@chan/index.php?p=channel-panel&at=" . $_GET['at'] . "&banner_success");
					}
					break;

				case "delete-channel":
					if ($_POST['channel_to_delete'] === $channel_data['name']) { //says wrong channel name
						if ($channel->DeleteChannel($_POST['channel_to_delete'])) {
							header("Location: /@chan/index.php");
						} else header("Location: /@chan/index.php?p=channel-panel&at=" . $_GET['at'] . "&wrong_name");
					} else header("Location: /@chan/index.php?p=channel-panel&at=" . $_GET['at'] . "&wrong_name");
					break;
			}
		}
	} else header("Location: /@chan/error.php?e=404");
} else header("Location: /@chan/index.php?p=login&unauthenticated");
?>

<div class="w-100 d-flex flex-column align-items-center bg-light border-start border-end" style="min-height: 87.9vh">
	<header class="container border-bottom mb-2">
		<h3 class="py-2 px-2"><?php echo "<a href='/@chan/index.php?p=channel&at=" . $_GET['at'] . "'>" . $_GET['at'] . "</a>"; ?> / Panel</h3>
	</header>

	<div class="form-container py-3 px-2">
		<h4>Change channel banner</h4>
		<div>
			<?php
			if (isset($_GET['banner_success'])) {
				echo "
						<div class='alert alert-success alert-dismissible fade show w-100' role='alert'>
							<i class='bi bi-check-square-fill'></i>
							Banner changed successfully.
							<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
						</div>
					";
			}
			?>
			<div class="pb-3 border-bottom">
				<?php echo "<img src='/@chan/_storage/channel-banners/" . $channel_data['banner'] . "' class='img-fluid img-thumbnail' alt='Profile picture'>"; ?>

				<form action="" method="post" enctype="multipart/form-data" class="w-100" id="banner-form" novalidate>
					<div class="mb-3">
						<label for="file-input" class="form-label"><small>The file must be a jpg, jpeg, or png smaller than 2MB</small></label>
						<input class="form-control" name="banner" type="file" id="file-input">
						<div class="invalid-feedback" id="invalid-extension-alert">Please select a valid file.</div>
					</div>

					<input type="hidden" name="action" value="change-banner">
					<button type="button" class="btn btn-primary w-100" id="change-banner-btn">Upload</button>
				</form>
			</div>
		</div>

		<h4 class="mt-2">Delete channel</h4>
		<form action="" method="post" class="w-100" id="delete-channel-form" novalidate>
			<?php
			if (isset($_GET['wrong_name'])) {
				echo "
						<div class='alert alert-danger alert-dismissible fade show w-100' role='alert'>
							<i class='bi bi-exclamation-triangle-fill'></i>
  							Wrong channel name.
  							<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
						</div>
					";
			}
			?>

			<label for="channel-name-input" class="form-label">Channel name</label>
			<div class="input-group mb-3">
				<span class="input-group-text" id="basic-addon1">@</span>
				<input type="text" name="channel_to_delete" class="form-control" id="channel-name-input" style="border-radius: 0 0.25rem 0.25rem 0;" placeholder="Enter the channel name" autocomplete="on">
				<div class="invalid-feedback">Please enter the channel's name.</div>
			</div>

			<input type="hidden" name="action" value="delete-channel">
			<button type="button" class="btn btn-danger w-100 disabled" id="delete-channel-btn"><i class="bi bi-exclamation-triangle"></i> DELETE THIS CHANNEL</button>
		</form>
	</div>
</div>