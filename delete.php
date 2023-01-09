<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/channel.service.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/user.service.php");

$channel = new ChannelService();
$user = new UserService();

if (isset($_GET['tid'])) {
	if ($channel->DeleteThread($_GET['tid'])) header("Location: /@chan/index.php?p=channel&at=" . $_GET['at']);
} else if (isset($_GET['cid'])) {
	if ($user->DeleteComment($_GET['cid'])) {
		if (isset($_GET['t']) && isset($_GET['at'])) header("Location: /@chan/index.php?p=thread&tid=" . $_GET['t'] . "&at=" . $_GET['at']);
		else header("Location: /@chan/index.php?p=profile");
	}
} else {
	header("Location: /@chan/index.php");
}
