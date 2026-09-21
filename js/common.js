let menuBtn = document.getElementById("menuBtn");
let navMenu = document.getElementById("navMenu");

if (menuBtn && navMenu) {
    menuBtn.addEventListener("click", function() {
        navMenu.classList.toggle("show");
    });
}

let themeBtn = document.getElementById("themeBtn");

if (themeBtn) {
    themeBtn.addEventListener("click", function() {
        document.body.classList.toggle("dark");
    });
}