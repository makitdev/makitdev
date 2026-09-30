/* ==========================================================================
   makitdev — contributors page renderer
   Reads window.MDK_CONTRIBUTORS (defined in assets/data/contributors.js)
   and builds the contributor cards. No fetch, no server required, so the
   page works when opened directly from disk. No dependencies.

   assets/data/contributors.js is the single source of truth for the roster.
   There is deliberately no hardcoded copy of it in this file: a second copy
   was previously kept here as a "fallback" and silently drifted out of sync
   with the data file, so contributors added via scripts/add-contributor.js
   were missing from it. If the data script fails to load we say so instead
   of rendering a stale roster.

   Cards are injected after main.js has run, so this file also handles the
   scroll-reveal for the cards it adds.
   ========================================================================== */

(function () {
  "use strict";

  var grid = document.getElementById("contributors-grid");
  if (!grid) {
    return;
  }

  var reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)");

  function avatarUrl(contributor) {
    var base =
      contributor.avatar ||
      "https://github.com/" + encodeURIComponent(contributor.username) + ".png";
    var sep = base.indexOf("?") === -1 ? "?" : "&";
    return base + sep + "s=144";
  }

  function githubUrl(contributor) {
    return (
      contributor.github ||
      "https://github.com/" + encodeURIComponent(contributor.username)
    );
  }

  function card(contributor) {
    var li = document.createElement("li");
    li.className = "contributor reveal";

    var img = document.createElement("img");
    img.className = "contributor-avatar";
    img.src = avatarUrl(contributor);
    img.alt = contributor.name || contributor.username;
    img.width = 144;
    img.height = 144;
    img.loading = "lazy";

    var body = document.createElement("div");
    body.className = "contributor-body";

    var head = document.createElement("div");
    head.className = "contributor-head";

    var meta = document.createElement("div");
    var name = document.createElement("h3");
    name.className = "contributor-name";
    name.textContent = contributor.name || ("@" + contributor.username);
    var handle = document.createElement("p");
    handle.className = "contributor-handle";
    handle.textContent = "@" + contributor.username;
    meta.appendChild(name);
    meta.appendChild(handle);

    var link = document.createElement("a");
    link.className = "contributor-link tlink";
    link.href = githubUrl(contributor);
    link.target = "_blank";
    link.rel = "noopener noreferrer";
    link.appendChild(document.createTextNode("GitHub"));
    var arrow = document.createElement("span");
    arrow.className = "arrow";
    arrow.setAttribute("aria-hidden", "true");
    arrow.textContent = "\u2197";
    link.appendChild(arrow);

    head.appendChild(meta);
    head.appendChild(link);
    body.appendChild(head);

    if (contributor.role) {
      var role = document.createElement("p");
      role.className = "contributor-role";
      role.textContent = contributor.role;
      body.appendChild(role);
    }

    if (contributor.contribution) {
      var desc = document.createElement("p");
      desc.className = "contributor-desc";
      desc.textContent = contributor.contribution;
      body.appendChild(desc);
    }

    li.appendChild(img);
    li.appendChild(body);
    return li;
  }

  function reveal(items) {
    if (!items.length) {
      return;
    }
    if (reduceMotion.matches || !("IntersectionObserver" in window)) {
      items.forEach(function (el) {
        el.classList.add("in-view");
      });
      return;
    }
    var io = new window.IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add("in-view");
            io.unobserve(entry.target);
          }
        });
      },
      { rootMargin: "0px 0px -8% 0px", threshold: 0.1 }
    );
    items.forEach(function (el) {
      io.observe(el);
    });
  }

  function showEmpty(message) {
    var p = document.createElement("p");
    p.className = "statement-copy";
    p.textContent = message;
    grid.parentNode.insertBefore(p, grid);
    grid.textContent = "";
  }

  function renderList(list) {
    // Mirror the WordPress template: entries without a username cannot produce a
    // valid avatar or profile link, so drop them instead of rendering a card
    // that points at github.com/undefined.
    var usable = list.filter(function (c) {
      return c && typeof c === "object" && c.username;
    });

    if (!usable.length) {
      showEmpty("No contributors listed yet.");
      return;
    }
    grid.textContent = "";
    var cards = usable.map(card);
    cards.forEach(function (el) {
      grid.appendChild(el);
    });
    reveal(cards);
  }

  var data = window.MDK_CONTRIBUTORS;

  if (data === undefined) {
    showEmpty("Contributor data could not be loaded.");
    return;
  }

  if (Array.isArray(data) && data.length) {
    renderList(data);
  } else {
    showEmpty("No contributors listed yet.");
  }
})();