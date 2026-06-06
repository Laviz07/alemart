const form = document.getElementById("loginForm");
const errorAlert = document.getElementById("errorAlert");

const togglePassword = document.getElementById("togglePassword");

togglePassword.addEventListener("click", function () {
	const password = document.getElementById("password");

	const icon = this.querySelector("i");

	if (password.type === "password") {
		password.type = "text";

		icon.classList.remove("bi-eye");
		icon.classList.add("bi-eye-slash");
	} else {
		password.type = "password";

		icon.classList.remove("bi-eye-slash");
		icon.classList.add("bi-eye");
	}
});
