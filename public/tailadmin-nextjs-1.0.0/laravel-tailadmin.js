document.addEventListener("DOMContentLoaded", function () {
    var root = document.documentElement;
    var toggle = document.querySelector("[data-sidebar-toggle]");
    var desktopQuery = window.matchMedia("(min-width: 992px)");

    function syncToggleState() {
        if (!toggle) return;

        if (desktopQuery.matches) {
            var collapsed = root.classList.contains("ta-sidebar-collapsed");
            toggle.setAttribute("aria-expanded", collapsed ? "false" : "true");
            toggle.setAttribute("title", collapsed ? "Perbesar sidebar" : "Ciutkan sidebar");
            return;
        }

        toggle.setAttribute("aria-expanded", document.body.classList.contains("ta-sidebar-open") ? "true" : "false");
        toggle.setAttribute("title", "Buka/tutup menu");
    }

    if (toggle) {
        toggle.addEventListener("click", function () {
            if (desktopQuery.matches) {
                var collapsed = !root.classList.contains("ta-sidebar-collapsed");
                root.classList.toggle("ta-sidebar-collapsed", collapsed);
                try {
                    localStorage.setItem("ta-sidebar-collapsed", collapsed ? "1" : "0");
                } catch (e) {}
            } else {
                document.body.classList.toggle("ta-sidebar-open");
            }

            syncToggleState();
        });
    }

    document.querySelectorAll(".ta-sidebar .nav-link").forEach(function (link) {
        link.addEventListener("click", function () {
            document.body.classList.remove("ta-sidebar-open");
            syncToggleState();
        });
    });

    if (desktopQuery.addEventListener) {
        desktopQuery.addEventListener("change", function () {
            document.body.classList.remove("ta-sidebar-open");
            syncToggleState();
        });
    } else if (desktopQuery.addListener) {
        desktopQuery.addListener(function () {
            document.body.classList.remove("ta-sidebar-open");
            syncToggleState();
        });
    }

    syncToggleState();
});
