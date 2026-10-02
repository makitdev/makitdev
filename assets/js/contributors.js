/* ==========================================================================
   makitdev — contributors page controller
   Renders window.MDK_CONTRIBUTORS immediately (so the page is complete
   offline and with GitHub blocked), then refreshes from the live API when it
   answers. No stale hardcoded copy lives here: the roster in
   assets/data/contributors.js is the single source of truth.
   ========================================================================== */

(function () {
  "use strict";

  var grid = document.getElementById("contributors-grid");
  var note = document.getElementById("roster-note");

  if (!grid || !window.MDKCommunity) {
    return;
  }

  var community = window.MDKCommunity;
  var emptyMessage = "No contributors listed yet.";

  /**
   * Overlay live GitHub data (avatar, profile link, commit count) onto the
   * curated roster, then append anyone GitHub knows about who is not listed
   * yet — so editorial notes survive while the numbers stay real.
   */
  function merge(staticList, liveList) {
    var liveByLogin = {};
    var known = {};
    var merged = [];

    (liveList || []).forEach(function (person) {
      if (person && person.login) {
        liveByLogin[person.login.toLowerCase()] = person;
      }
    });

    (staticList || []).forEach(function (person) {
      if (!person || !(person.login || person.username)) {
        return;
      }
      var key = String(person.login || person.username).toLowerCase();
      known[key] = true;

      var copy = {};
      Object.keys(person).forEach(function (field) {
        copy[field] = person[field];
      });

      var live = liveByLogin[key];
      if (live) {
        copy.login = live.login;
        copy.avatar = live.avatar || copy.avatar;
        copy.url = live.url || copy.url;
        copy.name = person.name || live.name;
        copy.contributions = live.contributions;
      }
      merged.push(copy);
    });

    (liveList || []).forEach(function (person) {
      if (person.login && !known[person.login.toLowerCase()]) {
        merged.push(person);
      }
    });

    return merged;
  }

  function render(people) {
    community.reveal(community.renderRoster(grid, people, emptyMessage));
  }

  var staticRoster = window.MDK_CONTRIBUTORS || [];
  render(staticRoster);

  if (note) {
    note.textContent = staticRoster.length
      ? "Roster maintained in the repository and refreshed from GitHub."
      : "";
  }

  if (!window.MDKGitHub) {
    return;
  }

  window.MDKGitHub.fetchContributors().then(
    function (people) {
      if (!people || !people.length) {
        return;
      }
      render(merge(staticRoster, people));
      if (note) {
        var commits = people.reduce(function (total, person) {
          return total + (person.contributions || 0);
        }, 0);
        note.textContent =
          "Live data from api.github.com · " +
          people.length +
          (people.length === 1 ? " contributor" : " contributors") +
          (commits
            ? " · " + commits + (commits === 1 ? " commit" : " commits")
            : "");
      }
    },
    function () {
      // Offline or rate-limited: the static roster already on screen stands.
    }
  );
})();