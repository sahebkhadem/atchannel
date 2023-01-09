<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/channel.service.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/user.service.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/storage.service.php");

$channel = new ChannelService();
$user = new UserService();
$storage = new StorageService();

if (!empty($_POST)) {
	$creator_id = $user->GetUserDataByUsername($_SESSION['username'])['id'];

	$banner = "";
	if ($_FILES['banner']['name'] !== "") $banner = $storage->Upload($_FILES['banner'], "banner");

	$channel_creation_result = $channel->CreateChannel($_POST['channel-name'], $_POST['channel-description'], $banner, $creator_id);

	if ($channel_creation_result) {
		header("Location: /@chan/index.php?p=channel&at=" . $_POST['channel-name']);
	} else header("Location: /@chan/index.php?p=create-channel&exists");
}
?>

<?php
if (isset($_GET['exists'])) {
	echo "
		<div class='alert alert-warning alert-dismissible fade show' role='alert'>
			<i class='bi bi-exclamation-triangle-fill'></i>
			Channel name already in use.
			<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
		</div>
	";
}
?>

<h1 class="mb-5"><i class="bi bi-plus-square"></i> New Channel</h1>
<div class="d-flex flex-column justify-content-center align-items-center form-container">
	<form action="" method="post" enctype="multipart/form-data" class="w-100" id="form" novalidate>
		<label for="channel-name-input" class="form-label">
			Channel name
			<small>( Only letters, numbers, and _ or - )</small>
		</label>
		<div class="input-group mb-3">
			<span class="input-group-text" id="basic-addon1">@</span>
			<input type="text" name="channel-name" class="form-control" style="border-radius: 0 0.25rem 0.25rem 0;" id="channel-name-input" placeholder="Enter a channel name" autocomplete="on">
			<div class="invalid-feedback">Please enter a valid channel name.</div>
		</div>

		<div class="mb-3">
			<label for="channel-description-input" class="form-label">
				Channel description
				<small>( Describe what the channel is about. )</small>
			</label>
			<textarea name="channel-description" class="form-control" id="channel-description-input" rows="10" maxlength="1024" style="resize: none;"></textarea>
			<small><span id="characters-countdown">1024</span> characters left</small>
			<div class="invalid-feedback">Please describe the channel.</div>
		</div>

		<div class="mb-3">
			<label for="file-input" class="form-label">
				Upload a banner
				<small>( Optional. The file must be a jpg, jpeg, or png smaller than 2MB )</small>
			</label>
			<input class="form-control" name="banner" type="file" id="channel-banner-input">
			<div class="invalid-feedback" id="invalid-extension-alert">File is either too big or is of the wrong type.</div>
		</div>

		<button type="button" class="btn btn-primary h-100 w-100 mb-1 me-md-1" id="create-channel-btn">Create!</button>
	</form>
</div>