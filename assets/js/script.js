const sidebar = document.getElementById("sidebar");
const sidebarToggle = document.getElementById("sidebarToggle");
const sidebarClose = document.getElementById("sidebarClose");
const sidebarOverlay = document.getElementById("sidebarOverlay");

sidebarToggle.addEventListener("click", () => {
	sidebar.classList.add("show");
	sidebarOverlay.classList.add("show");
});

sidebarClose.addEventListener("click", closeSidebar);
sidebarOverlay.addEventListener("click", closeSidebar);

function closeSidebar() {
	sidebar.classList.remove("show");
	sidebarOverlay.classList.remove("show");
}
