/* ==========================================================================
   makitdev — interactions
   Reveals, floating navigation, hero parallax + topology canvas, cursor glow
   and the animated workflow. No dependencies.

   Everything here is progressive enhancement: with JavaScript off (or
   prefers-reduced-motion) the page is a complete, static document.
   ========================================================================== */

(function () {
  "use strict";

  var root = document.documentElement;
  var reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  var finePointer = window.matchMedia("(hover: hover) and (pointer: fine)");

  function on(el, event, handler, opts) {
    if (el) {
      el.addEventListener(event, handler, opts);
    }
  }

  /* ---- Reveal on scroll ----------------------------------------------------- */

  function revealObserver() {
    var items = document.querySelectorAll(".reveal, .reveal-x");
    if (!("IntersectionObserver" in window) || reduceMotion.matches) {
      Array.prototype.forEach.call(items, function (el) {
        el.classList.add("in-view");
      });
      return;
    }
    var io = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add("in-view");
            io.unobserve(entry.target);
          }
        });
      },
      { rootMargin: "0px 0px -8% 0px", threshold: 0.08 }
    );
    Array.prototype.forEach.call(items, function (el) {
      io.observe(el);
    });
  }

  /* ---- Floating navigation -------------------------------------------------- */

  function headerState() {
    var header = document.querySelector(".site-header");
    if (!header) {
      return;
    }
    var sync = function () {
      header.classList.toggle("is-scrolled", window.scrollY > 24);
    };
    on(window, "scroll", sync, { passive: true });
    sync();
  }

  function mobileNav() {
    var header = document.querySelector(".site-header");
    var toggle = document.querySelector(".nav-toggle");
    var drawer = document.getElementById("nav-drawer");
    var scrim = document.querySelector(".nav-scrim");
    if (!header || !toggle || !drawer) {
      return;
    }

    if (scrim) {
      scrim.hidden = false;
    }
    drawer.hidden = false;

    var open = false;

    function setOpen(next) {
      open = next;
      toggle.setAttribute("aria-expanded", String(next));
      drawer.classList.toggle("is-open", next);
      header.classList.toggle("is-open", next);
      if (scrim) {
        scrim.classList.toggle("is-open", next);
      }
      document.body.classList.toggle("is-locked", next);
    }

    on(toggle, "click", function () {
      setOpen(!open);
    });
    on(scrim, "click", function () {
      setOpen(false);
    });
    on(document, "keydown", function (event) {
      if (event.key === "Escape" && open) {
        setOpen(false);
        toggle.focus();
      }
    });
    drawer.addEventListener("click", function (event) {
      if (event.target.closest("a")) {
        setOpen(false);
      }
    });

    // Leaving the mobile breakpoint with the drawer open would strand the
    // scroll lock, so reset it when the desktop layout takes over.
    if (window.matchMedia("(min-width: 901px)")) {
      var wide = window.matchMedia("(min-width: 901px)");
      var onChange = function (event) {
        if (event.matches && open) {
          setOpen(false);
        }
      };
      if (wide.addEventListener) {
        wide.addEventListener("change", onChange);
      } else if (wide.addListener) {
        wide.addListener(onChange);
      }
    }
  }

  function scrollSpy() {
    var links = document.querySelectorAll('.nav-links a[href^="#"]');
    if (!links.length || !("IntersectionObserver" in window)) {
      return;
    }
    var map = {};
    var sections = [];
    Array.prototype.forEach.call(links, function (link) {
      var section = document.querySelector(link.getAttribute("href"));
      if (section) {
        map[section.id] = link;
        sections.push(section);
      }
    });

    var io = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          var link = map[entry.target.id];
          if (!link) {
            return;
          }
          if (entry.isIntersecting) {
            Array.prototype.forEach.call(links, function (other) {
              other.classList.remove("is-current");
            });
            link.classList.add("is-current");
          }
        });
      },
      { rootMargin: "-45% 0px -50% 0px" }
    );
    sections.forEach(function (section) {
      io.observe(section);
    });
  }

  /* ---- Hero parallax -------------------------------------------------------- */

  function heroParallax() {
    var hero = document.querySelector(".hero");
    var media = document.querySelector(".hero-media");
    if (!hero || !media || reduceMotion.matches) {
      return;
    }

    var ticking = false;

    function frame() {
      var rect = hero.getBoundingClientRect();
      var progress = Math.min(1, Math.max(0, -rect.top / rect.height));
      media.style.setProperty("--hero-shift", (progress * rect.height * 0.16).toFixed(2) + "px");
      ticking = false;
    }

    function onScroll() {
      if (!ticking) {
        ticking = true;
        window.requestAnimationFrame(frame);
      }
    }

    on(window, "scroll", onScroll, { passive: true });
    on(window, "resize", onScroll);
    frame();
  }

  /* ---- Hero topology canvas ------------------------------------------------- */

  function topology() {
    var canvas = document.querySelector(".hero-canvas");
    if (!canvas || !canvas.getContext) {
      return;
    }
    var context = canvas.getContext("2d");
    var hero = canvas.closest(".hero") || document.body;

    var nodes = [];
    var pointer = { x: 0, y: 0, active: false };
    var visible = true;
    var running = false;
    var width = 0;
    var height = 0;
    var ratio = 1;

    function seed() {
      var count = width < 720 ? 14 : width < 1200 ? 22 : 30;
      nodes = [];
      for (var i = 0; i < count; i += 1) {
        nodes.push({
          x: Math.random(),
          y: 0.12 + Math.random() * 0.76,
          r: 0.6 + Math.random() * 1.5,
          ax: Math.random() * Math.PI * 2,
          ay: Math.random() * Math.PI * 2,
          sp: 0.08 + Math.random() * 0.22,
          accent: Math.random() < 0.16
        });
      }
    }

    function resize() {
      var rect = canvas.getBoundingClientRect();
      width = rect.width;
      height = rect.height;
      ratio = Math.min(2, window.devicePixelRatio || 1);
      canvas.width = Math.max(1, Math.round(width * ratio));
      canvas.height = Math.max(1, Math.round(height * ratio));
      context.setTransform(ratio, 0, 0, ratio, 0, 0);
      seed();
    }

    function draw(time, singleFrame) {
      if (!visible) {
        return;
      }
      context.clearRect(0, 0, width, height);

      var t = time * 0.00006;
      var threshold = Math.max(140, Math.min(width, height) * 0.28);
      var points = nodes.map(function (node) {
        var nx = node.x * width + Math.cos(t * node.sp + node.ax) * 18 + pointer.x * 26;
        var ny = node.y * height + Math.sin(t * node.sp + node.ay) * 14 + pointer.y * 18;
        return { x: nx, y: ny, r: node.r, accent: node.accent };
      });

      context.lineWidth = 1;
      for (var i = 0; i < points.length; i += 1) {
        for (var j = i + 1; j < points.length; j += 1) {
          var dx = points[i].x - points[j].x;
          var dy = points[i].y - points[j].y;
          var dist = Math.sqrt(dx * dx + dy * dy);
          if (dist > threshold) {
            continue;
          }
          var alpha = (1 - dist / threshold) * 0.16;
          context.strokeStyle = points[i].accent
            ? "rgba(184, 255, 101," + alpha + ")"
            : "rgba(154, 178, 168," + alpha * 0.8 + ")";
          context.beginPath();
          context.moveTo(points[i].x, points[i].y);
          context.lineTo(points[j].x, points[j].y);
          context.stroke();
        }
      }

      for (var k = 0; k < points.length; k += 1) {
        var point = points[k];
        context.fillStyle = point.accent
          ? "rgba(184, 255, 101, 0.5)"
          : "rgba(190, 205, 198, 0.32)";
        context.beginPath();
        context.arc(point.x, point.y, point.r, 0, Math.PI * 2);
        context.fill();
      }

      if (!singleFrame) {
        window.requestAnimationFrame(draw);
      }
    }

    function loop(time) {
      if (reduceMotion.matches) {
        // One static frame is enough when motion is not wanted.
        running = false;
        draw(0, true);
        return;
      }
      draw(time);
    }

    resize();
    on(window, "resize", debounce(resize, 200));

    if ("IntersectionObserver" in window) {
      new IntersectionObserver(
        function (entries) {
          visible = entries[0].isIntersecting;
          if (visible && !running) {
            running = true;
            window.requestAnimationFrame(loop);
          }
        },
        { threshold: 0 }
      ).observe(hero);
    } else {
      window.requestAnimationFrame(loop);
      running = true;
    }

    if (finePointer.matches && !reduceMotion.matches) {
      on(
        hero,
        "pointermove",
        function (event) {
          var rect = hero.getBoundingClientRect();
          pointer.x = (event.clientX - rect.left) / rect.width - 0.5;
          pointer.y = (event.clientY - rect.top) / rect.height - 0.5;
        },
        { passive: true }
      );
      on(
        hero,
        "pointerleave",
        function () {
          pointer.x = 0;
          pointer.y = 0;
        },
        { passive: true }
      );
    }

    on(document, "visibilitychange", function () {
      if (document.hidden) {
        running = false;
      } else if (visible && !running) {
        running = true;
        window.requestAnimationFrame(loop);
      }
    });
  }

  function debounce(fn, wait) {
    var timer;
    return function () {
      window.clearTimeout(timer);
      timer = window.setTimeout(fn, wait);
    };
  }

  /* ---- Cursor-following glow ------------------------------------------------ */

  function cardGlow() {
    var cards = document.querySelectorAll("[data-glow]");
    if (!cards.length || !finePointer.matches) {
      return;
    }
    Array.prototype.forEach.call(cards, function (card) {
      on(
        card,
        "pointermove",
        function (event) {
          var rect = card.getBoundingClientRect();
          card.style.setProperty("--mx", ((event.clientX - rect.left) / rect.width) * 100 + "%");
          card.style.setProperty("--my", ((event.clientY - rect.top) / rect.height) * 100 + "%");
        },
        { passive: true }
      );
    });
  }

  /* ---- Workflow: line progression + node illumination ---------------------- */

  function workflow() {
    var flow = document.querySelector("[data-flow]");
    if (!flow) {
      return;
    }
    var steps = flow.querySelectorAll(".flow-step");
    var ticking = false;

    function frame() {
      ticking = false;
      var rect = flow.getBoundingClientRect();
      var anchor = window.innerHeight * 0.55;
      var progress = (anchor - rect.top) / Math.max(1, rect.height);
      progress = Math.min(1, Math.max(0, progress));
      flow.style.setProperty("--flow-progress", reduceMotion.matches ? "1" : progress.toFixed(3));

      Array.prototype.forEach.call(steps, function (step) {
        var stepRect = step.getBoundingClientRect();
        var lit = stepRect.top + stepRect.height / 2 < anchor + 24;
        step.classList.toggle("is-lit", lit);
      });
    }

    function onScroll() {
      if (!ticking) {
        ticking = true;
        window.requestAnimationFrame(frame);
      }
    }

    on(window, "scroll", onScroll, { passive: true });
    on(window, "resize", onScroll);
    frame();
  }

  /* ---- Boot ----------------------------------------------------------------- */

  function boot() {
    revealObserver();
    headerState();
    mobileNav();
    scrollSpy();
    heroParallax();
    topology();
    cardGlow();
    workflow();

    if (window.MDKCommunity) {
      window.MDKCommunity.initIndex();
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();