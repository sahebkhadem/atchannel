var toTopButton = document.getElementById("to-top-btn");

window.addEventListener("scroll", () => {
	if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
		toTopButton.style.display = "block";
	} else {
		toTopButton.style.display = "none";
	}
});

toTopButton.addEventListener("click", () => {
	window.scrollTo({ top: 0, behavior: "smooth" });
});
