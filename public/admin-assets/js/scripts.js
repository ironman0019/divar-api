function toggleDropdown(id) {
    const dropdown = document.getElementById(`${id}-dropdown`);
    const arrow = document.getElementById(`${id}-arrow`);

    dropdown.classList.toggle("hidden");
    arrow.classList.toggle("rotate-180");
}

function toggleMobileSidebar() {
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebar-overlay");

    sidebar.classList.toggle("translate-x-full");
    overlay.classList.toggle("hidden");
}

// Close sidebar when clicking outside on mobile
document.addEventListener("click", function (event) {
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebar-overlay");
    const menuButton = event.target.closest("button");

    if (
        window.innerWidth < 1024 &&
        !sidebar.contains(event.target) &&
        !menuButton?.onclick?.toString().includes("toggleMobileSidebar")
    ) {
        if (!sidebar.classList.contains("translate-x-full")) {
            toggleMobileSidebar();
        }
    }
});

// Handle window resize
window.addEventListener("resize", function () {
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebar-overlay");

    if (window.innerWidth >= 1024) {
        sidebar.classList.remove("translate-x-full");
        overlay.classList.add("hidden");
    } else {
        sidebar.classList.add("translate-x-full");
        overlay.classList.add("hidden");
    }
});

// Animate cards on load
document.addEventListener("DOMContentLoaded", function () {
    const cards = document.querySelectorAll(".card-hover");
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = "0";
            card.style.transform = "translateY(20px)";
            card.style.transition = "all 0.5s ease";
            setTimeout(() => {
                card.style.opacity = "1";
                card.style.transform = "translateY(0)";
            }, 100);
        }, index * 100);
    });
});
