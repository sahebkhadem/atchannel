<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/channel.service.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/user.service.php");

$channel = new ChannelService();
$user = new UserService();

$isCreator = false;
$creator_username = "";

if (isset($_GET['at'])) {
	$thread_data = $channel->GetThreadData($_GET['tid']);

	$limit = 50;
	$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
	$comments_results = $channel->GetThreadComments($_GET['tid'], $current_page, $limit);
	if ($comments_results !== null) {
		$comments = $comments_results['comments'];
		$num_rows = $comments_results['num_rows'];
		$num_pages = ceil($num_rows / $limit);
		$previous = $current_page - 1;
		$next = $current_page + 1;
		$previous_disabled = $current_page == 1 ? 'disabled' : '';
		$next_disabled = $current_page == $num_pages ? 'disabled' : '';

		if (isset($_GET['page']) && $num_pages < 2) {
			if ($_GET['page'] > 1) header("Location: /@chan/error.php?e=404");
		}
	} else $num_pages = 0;

	$creator_username = $user->GetUserDataByID($thread_data['creator'])['username'];
	$channel_name = $channel->GetChannelDataById($_GET['at'])['name'];

	echo "<script>document.title = '" . $thread_data['title'] . "'</script>";

	if (isset($_SESSION['username'])) {
		$userid = $user->GetUserDataByUsername($_SESSION['username'])['id'];

		if (!empty($_POST)) {
			$comment_result = $user->Comment($_POST['comment'], $_GET['at'], $_GET['tid'], $userid);

			if ($comment_result) header("Refresh:0");
		}

		if ($thread_data['creator'] === $userid) {
			$isCreator = true;
		} else $isCreator = false;
	}
} else header("Location: /@chan/error.php?e=404");
?>

<div class="container flex-fill d-flex flex-column align-items-center bg-light border-start border-end p-0">
	<header class="container border-bottom mb-2 p-3">
		<small class="text-muted">Started by <?php echo "<a href='/@chan/index.php?p=profile&u='>" . $creator_username . "</a> on " . $thread_data['created'] . " at <a href='/@chan/index.php?p=channel&at=" . $channel_name . "'>" . $channel_name . "</a> - Comments: " . $thread_data['comments']; ?></small>
		<?php if ($isCreator) echo "<a href='/@chan/delete.php?tid=" . $thread_data['id'] . "&at=" . $thread_data['channel'] . "' class='btn btn-outline-danger ms-3'><i class='bi bi-trash'></i> Delete</a>"; ?>
		<h3 class="py-2 my-2 border-top border-bottom"><?php echo $thread_data['title']; ?></h3>
		<p><?php echo $thread_data['content']; ?></p>
	</header>

	<section class="d-flex flex-column justify-content-center align-items-center w-100 px-md-5 px-none">
		<?php
		if ($num_pages > 1) {
			echo "
				<nav aria-label='Page navigation'>
					<ul class='pagination mt-2'>
						<li class='page-item $previous_disabled'><a class='page-link' href='/@chan/index.php?p=thread&tid=" . $thread_data['id'] . "&at=" . $thread_data['channel'] . "&page=1'>First</a></li>
						<li class='page-item $previous_disabled'><a class='page-link' href='/@chan/index.php?p=thread&tid=" . $thread_data['id'] . "&at=" . $thread_data['channel'] . "&page=$previous'>&laquo; Previous</a></li>
						<li class='page-item'>
							<div class='dropdown'>
								<button class='page-link dropdown-toggle' type='button' id='dropdownMenuButton' data-bs-toggle='dropdown' aria-expanded='false'>
									Page $current_page of $num_pages
								</button>
								<ul class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
			";

			for ($i = 1; $i <= $num_pages; $i++) {
				$is_active = $i == $current_page ? "active" : "";
				echo "<li class='$is_active'><a class='dropdown-item' href='/@chan/index.php?p=thread&tid=" . $thread_data['id'] . "&at=" . $thread_data['channel'] . "&page=$i'>$i</a></li>";
			}

			echo "
								</ul>
							</div>
						</li>
						<li class='page-item $next_disabled'><a class='page-link' href='/@chan/index.php?p=thread&tid=" . $thread_data['id'] . "&at=" . $thread_data['channel'] . "&page=$next'>Next &raquo;</a></li>
						<li class='page-item $next_disabled'><a class='page-link' href='/@chan/index.php?p=thread&tid=" . $thread_data['id'] . "&at=" . $thread_data['channel'] . "&page=$num_pages'>Last</a></li>
					</ul>
				</nav>
			";
		}

		if (empty($comments)) {
			echo "
			<section class='container flex-grow-1 d-flex flex-column jujstify-content-center align-items-center p-5 bg-light'>
				<h3 class='text-muted'>Nothing to see here...</h3>
			</section>
		";
		} else {
			foreach ($comments as $comment) {
				echo "<div class='d-flex w-100 border''>";

				if ($comment['username'] !== "[deleted]") {
					echo "
						<a href='/@chan/index.php?p=profile&user=" . $comment['username'] . "' class='d-flex flex-column align-items-center p-2'>
							<img src='/@chan/_storage/profile-pictures/" . $comment['pfp'] . "' class='img-crop img-thumbnail' style='width: 100px !important; height: auto !important;' alt='Profile picture'>
							" . $comment['username'] . "
						</a>
					";
				} else {
					echo "
						<a href='/@chan/deleted.php?d=user' class='d-flex flex-column align-items-center p-2'>
							<img src='/@chan/_storage/profile-pictures/default-pfps/" . $comment['pfp'] . "' class='img-crop img-thumbnail' style='width: 100px !important; height: auto !important;' alt='Profile picture'>
							" . $comment['username'] . "
						</a>
					";
				}

				echo "<div class='flex-grow-1 border-start border-end p-2'>" . $comment['comment'] . "</div>";

				if (isset($_SESSION['username'])) {
					if ($comment['username'] === $_SESSION['username']) {
						echo "<div class='p-2'><a href='/@chan/delete.php?cid=" . $comment['cid'] . "&t=" . $comment['thread'] . "&at=" . $comment['channel'] . "' class='btn btn-outline-danger'><i class='bi bi-trash'></i></a></div>";
					}
				}

				echo "</div>";
			}
		}

		if ($num_pages > 1) {
			echo "
				<nav aria-label='Page navigation'>
					<ul class='pagination mt-2'>
						<li class='page-item $previous_disabled'><a class='page-link' href='/@chan/index.php?p=thread&tid=" . $thread_data['id'] . "&at=" . $thread_data['channel'] . "&page=1'>First</a></li>
						<li class='page-item $previous_disabled'><a class='page-link' href='/@chan/index.php?p=thread&tid=" . $thread_data['id'] . "&at=" . $thread_data['channel'] . "&page=$previous'>&laquo; Previous</a></li>
						<li class='page-item'>
							<div class='dropdown'>
								<button class='page-link dropdown-toggle' type='button' id='dropdownMenuButton' data-bs-toggle='dropdown' aria-expanded='false'>
									Page $current_page of $num_pages
								</button>
								<ul class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
			";

			for ($i = 1; $i <= $num_pages; $i++) {
				$is_active = $i == $current_page ? "active" : "";
				echo "<li class='$is_active'><a class='dropdown-item' href='/@chan/index.php?p=thread&tid=" . $thread_data['id'] . "&at=" . $thread_data['channel'] . "&page=$i'>$i</a></li>";
			}

			echo "
								</ul>
							</div>
						</li>
						<li class='page-item $next_disabled'><a class='page-link' href='/@chan/index.php?p=thread&tid=" . $thread_data['id'] . "&at=" . $thread_data['channel'] . "&page=$next'>Next &raquo;</a></li>
						<li class='page-item $next_disabled'><a class='page-link' href='/@chan/index.php?p=thread&tid=" . $thread_data['id'] . "&at=" . $thread_data['channel'] . "&page=$num_pages'>Last</a></li>
					</ul>
				</nav>
			";
		}
		?>
	</section>

	<div class="form-container">
		<form action="" method="post" enctype="multipart/form-data" class="w-100 p-3" id="form" novalidate>
			<div class="mb-3">
				<textarea name="comment" class="form-control" id="comment-text-area" rows="5" maxlength="10000"></textarea>
			</div>

			<?php
			if (isset($_SESSION['username'])) echo "<button type='submit' class='disabled btn btn-primary w-100 mb-1' id='post-comment-btn'>Post</button>";
			else echo "<button type='button' class='disabled btn btn-primary w-100 mb-1'>Login to comment.</button>";
			?>
		</form>
	</div>
</div>