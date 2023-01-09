var commentTextArea = document.getElementById("comment-text-area");

var postCommentButton = document.getElementById("post-comment-btn");

commentTextArea.addEventListener("keyup", () => {
	if (commentTextArea.value.length > 0 && commentTextArea.value !== " ") {
		postCommentButton.classList.remove("disabled");
	} else {
		postCommentButton.classList.add("disabled");
	}
});

window.addEventListener("load", () => {
	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
});
