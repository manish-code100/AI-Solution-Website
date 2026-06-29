(function () {
  "use strict";

  var SERVICE_PREFILL_KEY = "ai_solution_service_prefill";
  function $(selector, root) {
    return (root || document).querySelector(selector);
  }

  function $all(selector, root) {
    return Array.prototype.slice.call((root || document).querySelectorAll(selector));
  }

  function setFieldError(field, message) {
    var label = field.closest("label");
    var error = label ? $(".field-error", label) : null;

    if (label) {
      label.classList.toggle("is-invalid", Boolean(message));
    }

    if (error) {
      error.textContent = message || "";
    }
  }

  function clearFormErrors(form) {
    $all("input, textarea, select", form).forEach(function (field) {
      setFieldError(field, "");
    });
  }

  function validateEnquiry(form) {
    var valid = true;
    var name = form.elements.name;
    var email = form.elements.email;
    var phone = form.elements.phone;
    var message = form.elements.message || form.elements.details;

    clearFormErrors(form);

    if (name && !name.value.trim()) {
      setFieldError(name, "Name is required.");
      valid = false;
    }

    if (email && !email.value.trim()) {
      setFieldError(email, "Email is required.");
      valid = false;
    } else if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
      setFieldError(email, "Enter a valid email address.");
      valid = false;
    }

    if (phone && phone.value.trim() && !/^[0-9+\-\s()]{7,20}$/.test(phone.value.trim())) {
      setFieldError(phone, "Enter a valid phone number.");
      valid = false;
    }

    if (message && !message.value.trim()) {
      setFieldError(message, "Message is required.");
      valid = false;
    } else if (message && message.value.trim().length < 10) {
      setFieldError(message, "Add at least 10 characters.");
      valid = false;
    }

    return valid;
  }

  function initHeader() {
    var header = $("[data-header]");
    var toggle = $("[data-menu-toggle]");
    var nav = $("[data-nav]");

    if (!header) {
      return;
    }

    function syncHeader() {
      header.classList.toggle("is-scrolled", window.scrollY > 12);
    }

    syncHeader();
    window.addEventListener("scroll", syncHeader, { passive: true });

    if (toggle && nav) {
      toggle.addEventListener("click", function () {
        var open = !nav.classList.contains("is-open");
        nav.classList.toggle("is-open", open);
        header.classList.toggle("is-open", open);
        document.body.classList.toggle("menu-open", open);
        toggle.setAttribute("aria-expanded", String(open));
      });

      nav.addEventListener("click", function (event) {
        if (event.target.tagName.toLowerCase() === "a") {
          nav.classList.remove("is-open");
          header.classList.remove("is-open");
          document.body.classList.remove("menu-open");
          toggle.setAttribute("aria-expanded", "false");
        }
      });
    }
  }

  function initLoadingScreen() {
    var loader;

    if (document.body.classList.contains("admin-page")) {
      return;
    }

    loader = document.createElement("div");
    loader.className = "loading-screen";
    loader.innerHTML = "<div class=\"loader-core\" aria-label=\"Loading AI Solution\"></div>";
    document.body.appendChild(loader);

    window.addEventListener("load", function () {
      setTimeout(function () {
        loader.classList.add("is-hidden");
      }, 360);

      setTimeout(function () {
        loader.remove();
      }, 900);
    });
  }

  function initScrollProgress() {
    var progress;

    if (document.body.classList.contains("admin-page")) {
      return;
    }

    progress = document.createElement("div");
    progress.className = "scroll-progress";
    document.body.appendChild(progress);

    function sync() {
      var max = document.documentElement.scrollHeight - window.innerHeight;
      var percent = max > 0 ? (window.scrollY / max) * 100 : 0;
      progress.style.width = percent + "%";
    }

    sync();
    window.addEventListener("scroll", sync, { passive: true });
    window.addEventListener("resize", sync, { passive: true });
  }

  function initMagneticButtons() {
    var reduceMotion = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    if (reduceMotion) {
      return;
    }

    $all(".btn, .service-card, .value-card").forEach(function (element) {
      element.addEventListener("mousemove", function (event) {
        var rect = element.getBoundingClientRect();
        var x = event.clientX - rect.left - rect.width / 2;
        var y = event.clientY - rect.top - rect.height / 2;
        element.style.transform = "translate(" + (x * 0.025) + "px, " + (y * 0.025) + "px)";
      });

      element.addEventListener("mouseleave", function () {
        element.style.transform = "";
      });
    });
  }

  function initRevealAnimations() {
    var targets = $all([
      ".section-heading",
      ".glass-card",
      ".service-card",
      ".value-card",
      ".stat-card",
      ".article-card",
      ".testimonial-card",
      ".event-item",
      ".industry-card",
      ".gallery-grid figure",
      ".contact-form",
      ".contact-intro"
    ].join(","));

    if (!("IntersectionObserver" in window)) {
      targets.forEach(function (target) {
        target.classList.add("is-visible");
      });
      return;
    }

    targets.forEach(function (target) {
      target.classList.add("reveal");
    });

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.14 });

    targets.forEach(function (target) {
      observer.observe(target);
    });
  }

  function initCounters() {
    var counters = $all("[data-count]");

    function animateCounter(element) {
      var target = Number(element.getAttribute("data-count")) || 0;
      var suffix = element.getAttribute("data-suffix") || "";
      var duration = 1100;
      var start = performance.now();

      function tick(now) {
        var progress = Math.min((now - start) / duration, 1);
        var eased = 1 - Math.pow(1 - progress, 3);
        element.textContent = Math.round(target * eased).toLocaleString() + suffix;

        if (progress < 1) {
          requestAnimationFrame(tick);
        }
      }

      requestAnimationFrame(tick);
    }

    if (!("IntersectionObserver" in window)) {
      counters.forEach(animateCounter);
      return;
    }

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.4 });

    counters.forEach(function (counter) {
      observer.observe(counter);
    });
  }

  function initParticles() {
    var canvas = $("[data-particles]");
    var reduceMotion = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    var context;
    var particles = [];
    var animationId;

    if (!canvas || reduceMotion) {
      return;
    }

    context = canvas.getContext("2d");

    function resize() {
      var rect = canvas.getBoundingClientRect();
      var pixelRatio = Math.min(window.devicePixelRatio || 1, 2);
      canvas.width = Math.floor(rect.width * pixelRatio);
      canvas.height = Math.floor(rect.height * pixelRatio);
      context.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
      particles = Array.from({ length: Math.min(80, Math.floor(rect.width / 18)) }, function () {
        return {
          x: Math.random() * rect.width,
          y: Math.random() * rect.height,
          vx: (Math.random() - 0.5) * 0.34,
          vy: (Math.random() - 0.5) * 0.34,
          size: Math.random() * 2.2 + 0.8
        };
      });
    }

    function draw() {
      var rect = canvas.getBoundingClientRect();
      context.clearRect(0, 0, rect.width, rect.height);
      particles.forEach(function (particle, index) {
        particle.x += particle.vx;
        particle.y += particle.vy;

        if (particle.x < 0 || particle.x > rect.width) {
          particle.vx *= -1;
        }
        if (particle.y < 0 || particle.y > rect.height) {
          particle.vy *= -1;
        }

        context.beginPath();
        context.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
        context.fillStyle = "rgba(0, 212, 255, 0.68)";
        context.fill();

        particles.slice(index + 1).forEach(function (other) {
          var dx = particle.x - other.x;
          var dy = particle.y - other.y;
          var distance = Math.sqrt(dx * dx + dy * dy);

          if (distance < 110) {
            context.beginPath();
            context.moveTo(particle.x, particle.y);
            context.lineTo(other.x, other.y);
            context.strokeStyle = "rgba(108, 99, 255, " + (0.18 - distance / 800) + ")";
            context.lineWidth = 1;
            context.stroke();
          }
        });
      });

      animationId = requestAnimationFrame(draw);
    }

    resize();
    draw();
    window.addEventListener("resize", function () {
      cancelAnimationFrame(animationId);
      resize();
      draw();
    }, { passive: true });
  }

  function initTestimonialSliders() {
    $all("[data-testimonial-slider]").forEach(function (slider) {
      var track = $(".testimonial-track", slider);
      var slides = $all(".testimonial-slide", slider);
      var buttons = $all("[data-slide]", slider);
      var index = 0;

      function render() {
        if (track) {
          track.style.transform = "translateX(-" + (index * 100) + "%)";
        }

        buttons.forEach(function (button) {
          var active = button.getAttribute("data-slide") === String(index);
          button.setAttribute("aria-pressed", String(active));
        });
      }

      buttons.forEach(function (button) {
        button.addEventListener("click", function () {
          index = Number(button.getAttribute("data-slide")) || 0;
          render();
        });
      });

      if (slides.length > 1) {
        setInterval(function () {
          index = (index + 1) % slides.length;
          render();
        }, 5200);
      }

      render();
    });
  }

  function initParallax() {
    var targets = $all("[data-parallax]");

    if (!targets.length) {
      return;
    }

    function sync() {
      var scroll = window.scrollY;
      targets.forEach(function (target) {
        var speed = Number(target.getAttribute("data-parallax")) || 0.05;
        target.style.transform = "translate3d(0, " + (scroll * speed) + "px, 0)";
      });
    }

    sync();
    window.addEventListener("scroll", sync, { passive: true });
  }

  function prefillService(service) {
    var form = $("#enquiryForm");
    var message;

    if (!form) {
      sessionStorage.setItem(SERVICE_PREFILL_KEY, service);
      window.location.href = "enquiry.html";
      return;
    }

    if (form.elements.service) {
      form.elements.service.value = service;
    }

    message = form.elements.message || form.elements.details;
    if (message) {
      message.value = "I am interested in " + service + ". Please contact me with more information.";
    }

    $("#contact").scrollIntoView({ behavior: "smooth", block: "start" });
  }

  function initPrefillButtons() {
    $all("[data-service]").forEach(function (button) {
      button.addEventListener("click", function () {
        prefillService(button.getAttribute("data-service"));
      });
    });
  }

  function initEnquiryForm() {
    var form = $("#enquiryForm");
    var service = sessionStorage.getItem(SERVICE_PREFILL_KEY);
    var params = new URLSearchParams(window.location.search);
    var status = form ? $(".form-status", form) : null;
    var message;

    if (!form) {
      return;
    }

    if (status && params.get("submitted") === "1") {
      status.textContent = "Thank you. Your enquiry has been stored and the admin team can now review it.";
      status.classList.remove("is-error");
    } else if (status && params.get("error") === "validation") {
      status.textContent = "Please check your details and submit the form again.";
      status.classList.add("is-error");
    } else if (status && params.get("error") === "database") {
      status.textContent = "The database could not save your enquiry. Please check the XAMPP MySQL setup.";
      status.classList.add("is-error");
    }

    if (service) {
      if (form.elements.service) {
        form.elements.service.value = service;
      }

      message = form.elements.message || form.elements.details;
      if (message) {
        message.value = "I am interested in " + service + ". Please contact me with more information.";
      }

      sessionStorage.removeItem(SERVICE_PREFILL_KEY);
    }

    form.addEventListener("submit", function (event) {
      var isStaticForm = form.hasAttribute("data-static-form");

      if (!validateEnquiry(form)) {
        event.preventDefault();
        if (status) {
          status.textContent = "Please fix the highlighted fields.";
          status.classList.add("is-error");
        }
        return;
      }

      if (isStaticForm) {
        event.preventDefault();

        if (status) {
          status.textContent = "Thank you. This frontend prototype has captured your message preview.";
          status.classList.remove("is-error");
        }

        form.reset();
        return;
      }

      if (status) {
        status.textContent = "Submitting your enquiry securely...";
        status.classList.remove("is-error");
      }
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    document.body.classList.add("light-mode");
    initLoadingScreen();
    initHeader();
    initScrollProgress();
    initMagneticButtons();
    initParticles();
    initRevealAnimations();
    initCounters();
    initTestimonialSliders();
    initParallax();
    initPrefillButtons();
    initEnquiryForm();
  });
}());
