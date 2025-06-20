function toggleSidebar() {
  const sidebar = document.getElementById("sidebar");
  sidebar.style.marginLeft = (sidebar.style.marginLeft === "-16rem") ? "0" : "-16rem";
}

function toggleAccount() {
  const menu = document.getElementById("accountMenu");
  menu.classList.toggle("hidden");
}

// Tutup dropdown jika klik di luar menu
document.addEventListener("click", function (event) {
  const button = document.querySelector('button[onclick="toggleAccount()"]');
  const menu = document.getElementById("accountMenu");

  if (!button.contains(event.target) && !menu.contains(event.target)) {
    menu.classList.add("hidden");
  }
});