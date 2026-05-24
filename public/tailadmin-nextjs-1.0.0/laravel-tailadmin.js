document.addEventListener("DOMContentLoaded", function () {
    var toggle = document.querySelector("[data-sidebar-toggle]");

    if (toggle) {
        toggle.addEventListener("click", function () {
            document.body.classList.toggle("ta-sidebar-open");
        });
    }

    document.querySelectorAll(".ta-sidebar .nav-link").forEach(function (link) {
        link.addEventListener("click", function () {
            document.body.classList.remove("ta-sidebar-open");
        });
    });
});
