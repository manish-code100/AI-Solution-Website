(function () {
  "use strict";

  var navigationButtons = [
    { label: "Home", href: "index.html", page: "home" },
    { label: "Solutions", href: "solutions.html", page: "solutions" },
    { label: "Testimonials", href: "client-testimonial.html", page: "client-testimonial" },
    { label: "Articles", href: "articles.html", page: "articles" },
    { label: "Events", href: "events.html", page: "events" },
    { label: "Gallery", href: "gallery.html", page: "gallery" },
    { label: "Contact", href: "contact.html", page: "contact" }
  ];

  function getCurrentPage(nav) {
    if (nav && nav.getAttribute("data-active-page")) {
      return nav.getAttribute("data-active-page");
    }

    var path = window.location.pathname.split("/").pop() || "index.html";
    var match = navigationButtons.find(function (item) {
      return item.href === path;
    });

    return match ? match.page : "home";
  }

  function renderNavigationButtons() {
    var nav = document.querySelector("[data-button-nav]");

    if (!nav) {
      return;
    }

    var currentPage = getCurrentPage(nav);
    nav.innerHTML = navigationButtons.map(function (item) {
      var isActive = item.page === currentPage;

      if (item.mega) {
        return [
          "<div class=\"nav-item\">",
          "<button class=\"mega-trigger" + (isActive ? " is-active" : "") + "\" type=\"button\" aria-haspopup=\"true\">",
          item.label,
          "</button>",
          "<div class=\"mega-menu\" role=\"menu\">",
          "<div>",
          "<strong>AI Solution services</strong>",
          "<p>Explore intelligent automation, analytics, and custom AI systems for business teams.</p>",
          "</div>",
          "<div class=\"mega-links\">",
          item.mega.map(function (megaItem) {
            return "<a href=\"" + item.href + "\" role=\"menuitem\">" + megaItem + "</a>";
          }).join(""),
          "</div>",
          "</div>",
          "</div>"
        ].join("");
      }

      return [
        "<a",
        " href=\"" + item.href + "\"",
        " class=\"nav-button" + (isActive ? " is-active" : "") + "\"",
        isActive ? " aria-current=\"page\"" : "",
        ">",
        item.label,
        "</a>"
      ].join("");
    }).join("");
  }

  document.addEventListener("DOMContentLoaded", renderNavigationButtons);
}());
