function initMenu(){
  const menuBtn = document.getElementById("menuBtn");
  const sideNav = document.getElementById("sideNav");
  const overlay = document.getElementById("navOverlay");
  const closeBtn = document.getElementById("closeBtn");

  if (!menuBtn || !sideNav || !overlay) return;

  const openMenu = () => {
    sideNav.classList.add("open");
    overlay.classList.add("show");
    sideNav.setAttribute("aria-hidden", "false");
  };

  const closeMenu = () => {
    sideNav.classList.remove("open");
    overlay.classList.remove("show");
    sideNav.setAttribute("aria-hidden", "true");
  };

  menuBtn.addEventListener("click", openMenu);
  overlay.addEventListener("click", closeMenu);

  if (closeBtn) closeBtn.addEventListener("click", closeMenu);

  // Optional: close with ESC
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeMenu();
  });
}