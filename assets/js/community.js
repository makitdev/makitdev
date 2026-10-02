/* ==========================================================================
   makitdev — community rendering
   Turns GitHub data into repository cards, contributor avatars and roster
   cards. Every value rendered here comes from the API response or from the
   static roster in assets/data/contributors.js — nothing is invented.
   ========================================================================== */

(function () {
  "use strict";

  var GITHUB_ICON =
    '<svg class="repo-icon" viewBox="0 0 16 16" width="18" height="18" aria-hidden="true" focusable="false">' +
    '<path fill="currentColor" d="M8 0C3.58 0 0 3.58 0 8a8 8 0 0 0 5.47 7.59c.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82a7.4 7.4 0 0 1 2-.27c.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.01 8.01 0 0 0 16 8c0-4.42-3.58-8-8-8Z"/>' +
    "</svg>";

  function el(tag, className, text) {
    var node = document.createElement(tag);
    if (className) {
      node.className = className;
    }
    if (text !== undefined && text !== null) {
      node.textContent = text;
    }
    return node;
  }

  function chip(text, extraClass) {
    return el("span", "chip" + (extraClass ? " " + extraClass : ""), text);
  }

  function avatarUrl(url, login) {
    var base = url || "https://github.com/" + encodeURIComponent(login || "") + ".png";
    return base + (base.indexOf("?") === -1 ? "?" : "&") + "s=160";
  }

  function profileUrl(person) {
    return person.url || "https://github.com/" + encodeURIComponent(person.login || person.username);
  }

  function formatDate(iso) {
    if (!iso) {
      return "";
    }
    var date = new Date(iso);
    if (isNaN(date.getTime())) {
      return "";
    }
    return date.toLocaleDateString(undefined, {
      year: "numeric",
      month: "short",
      day: "numeric"
    });
  }

  /* ---- Repositories --------------------------------------------------------- */

  function repoCard(repo) {
    var item = document.createElement("a");
    item.className = "repo reveal";
    item.href = repo.url;
    item.target = "_blank";
    item.rel = "noopener noreferrer";

    var head = el("div", "repo-head");
    var name = el("span", "repo-name");
    name.innerHTML = GITHUB_ICON;
    name.appendChild(el("span", null, repo.fullName || repo.name));
    head.appendChild(name);
    head.appendChild(el("span", "repo-arrow", "↗"));
    item.appendChild(head);

    item.appendChild(
      el("p", "repo-desc", repo.description || "No description provided yet.")
    );

    var meta = el("div", "repo-meta");
    if (repo.language) {
      var lang = chip(repo.language);
      lang.insertBefore(el("span", "chip-dot"), lang.firstChild);
      meta.appendChild(lang);
    }
    if (repo.license && repo.license !== "NOASSERTION") {
      meta.appendChild(chip(repo.license));
    }
    // Star and fork counts only appear when the API actually sent them.
    if (typeof repo.stars === "number") {
      meta.appendChild(chip("★ " + repo.stars));
    }
    if (typeof repo.forks === "number") {
      meta.appendChild(chip("⑂ " + repo.forks));
    }
    item.appendChild(meta);

    return item;
  }

  function renderRepos(list, repos, note) {
    list.textContent = "";

    if (!repos || !repos.length) {
      var empty = el("li", "repo");
      var emptyBody = el("div");
      emptyBody.appendChild(el("p", "repo-desc", "Repositories are listed on the organization page."));
      var emptyLink = el("a", "action-link tlink", "Open github.com/makitdev");
      emptyLink.href = "https://github.com/makitdev";
      emptyLink.target = "_blank";
      emptyLink.rel = "noopener noreferrer";
      emptyBody.appendChild(emptyLink);
      empty.appendChild(emptyBody);
      list.appendChild(empty);
      if (note) {
        note.textContent = "Live GitHub data was unavailable, so nothing is counted here.";
      }
      return [];
    }

    var cards = repos.map(function (repo, index) {
      var card = repoCard(repo);
      card.style.setProperty("--reveal-delay", index * 70 + "ms");
      list.appendChild(card);
      return card;
    });

    if (note) {
      var stamp = formatDate(repos[0].updatedAt);
      note.textContent =
        "Live data from api.github.com · " +
        repos.length +
        (repos.length === 1 ? " public repository" : " public repositories") +
        (stamp ? " · last updated " + stamp : "");
    }
    return cards;
  }

  /* ---- People: avatars ------------------------------------------------------ */

  function renderPeople(list, countEl, people) {
    if (!list) {
      return;
    }
    list.textContent = "";

    (people || []).forEach(function (person) {
      var item = document.createElement("li");
      var link = document.createElement("a");
      link.href = profileUrl(person);
      link.target = "_blank";
      link.rel = "noopener noreferrer";
      link.title = person.login || person.username || "GitHub";

      var img = document.createElement("img");
      img.src = avatarUrl(person.avatar, person.login || person.username);
      img.alt = person.name || person.login || person.username || "Contributor";
      img.width = 88;
      img.height = 88;
      img.loading = "lazy";
      img.decoding = "async";

      link.appendChild(img);
      item.appendChild(link);
      list.appendChild(item);
    });

    if (countEl && people && people.length) {
      var commits = people.reduce(function (total, person) {
        return total + (person.contributions || 0);
      }, 0);
      countEl.textContent = commits
        ? people.length +
          (people.length === 1 ? " contributor · " : " contributors · ") +
          commits +
          (commits === 1 ? " commit" : " commits")
        : people.length + (people.length === 1 ? " contributor" : " contributors");
    }
  }

  /* ---- People: full roster cards (contributors page) ------------------------- */

  function contributorCard(person) {
    var item = el("li", "contributor reveal");
    var login = person.login || person.username;

    var img = document.createElement("img");
    img.className = "contributor-avatar";
    img.src = avatarUrl(person.avatar, login);
    img.alt = person.name || login;
    img.width = 144;
    img.height = 144;
    img.loading = "lazy";
    img.decoding = "async";

    var body = el("div", "contributor-body");
    var head = el("div", "contributor-head");
    var meta = el("div");
    meta.appendChild(el("h3", "contributor-name", person.name || "@" + login));
    meta.appendChild(el("p", "contributor-handle", "@" + login));
    head.appendChild(meta);

    var link = el("a", "contributor-link tlink", "GitHub");
    link.href = profileUrl(person);
    link.target = "_blank";
    link.rel = "noopener noreferrer";
    var arrow = el("span", "arrow", "↗");
    arrow.setAttribute("aria-hidden", "true");
    link.appendChild(arrow);
    head.appendChild(link);
    body.appendChild(head);

    if (typeof person.contributions === "number" && person.contributions > 0) {
      body.appendChild(
        el(
          "p",
          "contributor-role",
          person.contributions === 1
            ? "1 contribution"
            : person.contributions + " contributions"
        )
      );
    } else if (person.role) {
      body.appendChild(el("p", "contributor-role", person.role));
    }

    if (person.contribution) {
      body.appendChild(el("p", "contributor-desc", person.contribution));
    }

    item.appendChild(img);
    item.appendChild(body);
    return item;
  }

  function renderRoster(list, people, emptyMessage) {
    var usable = (people || []).filter(function (person) {
      return person && (person.login || person.username);
    });

    if (!usable.length) {
      list.textContent = "";
      list.parentNode.insertBefore(el("p", "statement-copy", emptyMessage), list);
      list.hidden = true;
      return [];
    }

    list.hidden = false;
    list.textContent = "";
    var cards = usable.map(function (person, index) {
      var card = contributorCard(person);
      card.style.setProperty("--reveal-delay", index * 60 + "ms");
      list.appendChild(card);
      return card;
    });
    return cards;
  }

  /* ---- Page wiring ---------------------------------------------------------- */

  function reveal(cards) {
    if (!cards || !cards.length || !("IntersectionObserver" in window)) {
      (cards || []).forEach(function (card) {
        card.classList.add("in-view");
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
    cards.forEach(function (card) {
      io.observe(card);
    });
  }

  function initIndex() {
    var repoList = document.getElementById("repos");
    var peopleList = document.getElementById("people-avatars");
    var countEl = document.getElementById("people-count");
    if (!repoList && !peopleList) {
      return;
    }

    // The static roster is only a fallback: if the API answers, live data
    // replaces it. Until then the skeleton placeholders stay in place.
    var fallback = window.MDK_CONTRIBUTORS || [];
    var note = document.getElementById("repo-note");

    if (!window.MDKGitHub) {
      if (repoList) {
        renderRepos(repoList, [], note);
      }
      if (peopleList) {
        renderPeople(peopleList, countEl, fallback);
      }
      return;
    }

    Promise.all([
      window.MDKGitHub.fetchRepos().catch(function () {
        return null;
      }),
      window.MDKGitHub.fetchContributors().catch(function () {
        return null;
      })
    ]).then(function (results) {
      if (repoList) {
        if (results[0]) {
          reveal(renderRepos(repoList, results[0], note));
        } else {
          renderRepos(repoList, [], note);
        }
      }

      if (peopleList) {
        if (results[1] && results[1].length) {
          renderPeople(peopleList, countEl, results[1]);
        } else if (fallback.length) {
          renderPeople(peopleList, countEl, fallback);
        }
      }
    });
  }

  window.MDKCommunity = {
    renderRepos: renderRepos,
    renderPeople: renderPeople,
    renderRoster: renderRoster,
    contributorCard: contributorCard,
    reveal: reveal,
    initIndex: initIndex
  };
})();