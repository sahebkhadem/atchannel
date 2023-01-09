<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/channel.service.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/user.service.php");

$channel = new ChannelService();
$user = new UserService();

if (isset($_GET['at'])) {
	if (isset($_SESSION['username'])) {
		$userid = $user->GetUserDataByUsername($_SESSION['username'])['id'];

		if (!empty($_POST)) {
			$creator_id = $user->GetUserDataByUsername($_SESSION['username'])['id'];

			$thread_creation_result = $channel->CreateThread($_GET['cid'], $_POST['thread-title'], $_POST['thread-content'], $creator_id);

			if (!empty($thread_creation_result)) {
				header("Location: /@chan/index.php?p=thread&tid=" . $thread_creation_result . "&at=" . $_GET['cid']);
			}
		}
	} else header("Location: /@chan/index.php?p=login&unauthenticated");
} else header("Location: /@chan/error.php?e=404");
?>

<h1><i class="bi bi-node-plus"></i> New Thread</h1>
<h4 class="mb-5">@<?php echo $_GET['at']; ?></h4>
<div class="d-flex flex-column justify-content-center align-items-center form-container">
	<form action="" method="post" enctype="multipart/form-data" class="w-100" id="form" novalidate>
		<label for="thread-title-input" class="form-label">Title</label>
		<div class="mb-3">
			<input type="text" name="thread-title" class="form-control" style="border-radius: 0.25rem;" id="thread-title-input" placeholder="Enter a title" autocomplete="on">
			<div class="invalid-feedback">Please enter a title.</div>
		</div>

		<div class="mb-3">
			<label for="channel-description-input" class="form-label">
				Content
				<small>( Optional. More information about the thread. )</small>
			</label>
			<textarea name="thread-content" class="form-control" id="channel-description-input" rows="10" maxlength="1024" style="resize: none;"></textarea>
			<small><span id="characters-countdown">1024</span> characters left</small>
		</div>

		<button type="button" class="btn btn-primary h-100 w-100 mb-1 me-md-1" id="create-thread-btn">Create!</button>
	</form>
</div>