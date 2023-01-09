<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/channel.service.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/user.service.php");

$channel = new ChannelService();
$user = new UserService();

$channel_data;
$isCreator = false;

if (isset($_GET['at'])) {
	$channel_data = $channel->GetChannelDataByName($_GET['at'], "");

	if (isset($_SESSION['username'])) {
		$userid = $user->GetUserDataByUsername($_SESSION['username'])['id'];

		if ($channel_data['creator'] === $userid) {
			$isCreator = true;
		} else $isCreator = false;
	}

	if (!is_numeric($_GET['at'])) $channel_data = $channel->GetChannelDataByName($_GET['at']);
	if (is_numeric($_GET['at'])) $channel_data = $channel->GetChannelDataById($_GET['at']);

	echo "<script>document.title = '@" . $channel_data['name'] . "'</script>";

	$limit = 50;
	$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
	$threads_result = $channel->GetThreads($channel_data['id'], $current_page, $limit);

	if ($threads_result !== null) {
		$threads = $threads_result['threads'];
		$num_rows = $threads_result['num_rows'];
		$num_pages = ceil($num_rows / $limit);
		$previous = $current_page - 1;
		$next = $current_page + 1;
		$previous_disabled = $current_page == 1 ? 'disabled' : '';
		$next_disabled = $current_page == $num_pages ? 'disabled' : '';

		if (isset($_GET['page']) && $num_pages < 2) {
			if ($_GET['page'] > 1) header("Location: /@chan/error.php?e=404");
		}
	} else $num_pages = 0;
} else header("Location: /@chan/error.php?e=404");

if ($channel_data === null) header("Location: /@chan/error.php?e=404");
if (isset($_GET['page']) && $num_pages < 2) header("Location: /@chan/error.php?e=404");
?>

<div class="w-100 bg-light border-start border-end" style="min-height: 87.9vh">
	<header class="w-100">
		<?php
		if (empty($channel_data['banner'])) echo "<div class='banner'></div>";
		if (!empty($channel_data['banner'])) echo "<img src='/@chan/_storage/channel-banners/" . $channel_data['banner'] .  "' class='banner img-fluid'></img>";
		?>

		<div class="container border-bottom mb-2 p-3">
			<div class="d-flex align-items-center">
				<h3 class="py-2 px-2"><?php echo $channel_data['name']; ?></h3>
				<?php if ($isCreator) echo "<a href='/@chan/index.php?p=channel-panel&at=" . $channel_data['name'] . "' class='btn btn-outline-secondary'><i class='bi bi-pencil-square'></i></a>"; ?>
			</div>
			<div class="text-muted">
				<small>Threads: <?php echo $channel_data['threads']; ?> - </small>
				<small>Comments: <?php echo $channel_data['comments']; ?></small>
			</div>
			<p><?php echo $channel_data['description']; ?></p>
		</div>
	</header>

	<section class="container d-flex flex-column jujstify-content-center align-items-center px-md-5 px-1 bg-light">
		<?php
		if (isset($_SESSION['username'])) {
			echo "<a class='btn btn-primary my-3' href='/@chan/index.php?p=create-thread&at=" . $channel_data['name'] . "&cid=" . $channel_data['id'] . "'><i class='bi bi-node-plus'></i> Start a new thread!</a>";
		}

		if ($num_pages > 1) {
			echo "
				<nav aria-label='Page navigation'>
					<ul class='pagination mt-2'>
						<li class='page-item $previous_disabled'><a class='page-link' href='/@chan/index.php?p=channel&at=" . $channel_data['name'] . "&page=1'>First</a></li>
						<li class='page-item $previous_disabled'><a class='page-link' href='/@chan/index.php?p=channel&at=" . $channel_data['name'] . "&page=$previous'>&laquo; Previous</a></li>
						<li class='page-item'>
							<div class='dropdown'>
								<button class='page-link dropdown-toggle' type='button' id='dropdownMenuButton' data-bs-toggle='dropdown' aria-expanded='false'>
									Page $current_page of $num_pages
								</button>
								<ul class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
			";

			for ($i = 1; $i <= $num_pages; $i++) {
				$is_active = $i == $current_page ? "active" : "";
				echo "<li class='$is_active'><a class='dropdown-item' href='/@chan/index.php?p=channel&at=" . $channel_data['name'] . "&page=$i'>$i</a></li>";
			}

			echo "
								</ul>
							</div>
						</li>
						<li class='page-item $next_disabled'><a class='page-link' href='/@chan/index.php?p=channel&at=" . $channel_data['name'] . "&page=$next'>Next &raquo;</a></li>
						<li class='page-item $next_disabled'><a class='page-link' href='/@chan/index.php?p=channel&at=" . $channel_data['name'] . "&page=$num_pages'>Last</a></li>
					</ul>
				</nav>
			";
		}

		if (empty($threads)) {
			echo "<h3 class='text-muted'>Nothing to see here...</h3>";
		} else {
			foreach ($threads as $thread) {
				echo "
					<div class='card mb-3 w-100'>
						<div class='card-header'>
							" . $thread['created'] . "
				";

				if ($userid === $thread['creator']) {
					echo "<a href='/@chan/delete.php?tid=" . $thread['id'] . "&at=" . $thread['channel'] . "' class='btn btn-outline-danger ms-3'><i class='bi bi-trash'></i> Delete</a>";
				}

				echo "
					</div>
					<a href='/@chan/index.php?p=thread&tid=" . $thread['id'] . "&at=" . $thread['channel'] . "'>
						<div class='card-body'>
							<h5 class='card-title'>" . $thread['title'] . "</h5>
						</div>
					</a>
				";

				echo "</div>";
			}
		}

		if ($num_pages > 1) {
			echo "
				<nav aria-label='Page navigation'>
					<ul class='pagination mt-2'>
						<li class='page-item $previous_disabled'><a class='page-link' href='/@chan/index.php?p=channel&at=" . $channel_data['name'] . "&page=1'>First</a></li>
						<li class='page-item $previous_disabled'><a class='page-link' href='/@chan/index.php?p=channel&at=" . $channel_data['name'] . "&page=$previous'>&laquo; Previous</a></li>
						<li class='page-item'>
							<div class='dropdown'>
								<button class='page-link dropdown-toggle' type='button' id='dropdownMenuButton' data-bs-toggle='dropdown' aria-expanded='false'>
									Page $current_page of $num_pages
								</button>
								<ul class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
			";

			for ($i = 1; $i <= $num_pages; $i++) {
				$is_active = $i == $current_page ? "active" : "";
				echo "<li class='$is_active'><a class='dropdown-item' href='/@chan/index.php?p=channel&at=" . $channel_data['name'] . "&page=$i'>$i</a></li>";
			}

			echo "
								</ul>
							</div>
						</li>
						<li class='page-item $next_disabled'><a class='page-link' href='/@chan/index.php?p=channel&at=" . $channel_data['name'] . "&page=$next'>Next &raquo;</a></li>
						<li class='page-item $next_disabled'><a class='page-link' href='/@chan/index.php?p=channel&at=" . $channel_data['name'] . "&page=$num_pages'>Last</a></li>
					</ul>
				</nav>
			";
		}
		?>
	</section>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>