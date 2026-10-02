/* ==========================================================================
   makitdev — GitHub data layer
   Reads public organization data from the GitHub REST API so the page can
   show real repositories, real stars/forks and real contributor counts.
   Nothing here invents numbers: if the API is unreachable or rate-limited the
   callers fall back to static copy (see community.js).
   No dependencies, no API key, no auth.
   ========================================================================== */

(function () {
  "use strict";

  var ORG = "makitdev";
  var API = "https://api.github.com";
  var MAX_REPOS = 12; // stay well inside the unauthenticated rate limit
  var CACHE_TTL = 15 * 60 * 1000;
  var TIMEOUT = 8000;

  function cacheKey(path) {
    return "mdk:" + path;
  }

  function readCache(path) {
    try {
      var raw = window.sessionStorage.getItem(cacheKey(path));
      if (!raw) {
        return null;
      }
      var entry = JSON.parse(raw);
      if (!entry || Date.now() - entry.t > CACHE_TTL) {
        return null;
      }
      return entry.d;
    } catch (error) {
      return null;
    }
  }

  function writeCache(path, data) {
    try {
      window.sessionStorage.setItem(
        cacheKey(path),
        JSON.stringify({ t: Date.now(), d: data })
      );
    } catch (error) {
      /* Private mode or a full quota: the page still works, just uncached. */
    }
  }

  function get(path) {
    var cached = readCache(path);
    if (cached) {
      return Promise.resolve(cached);
    }

    var controller =
      typeof AbortController !== "undefined" ? new AbortController() : null;
    var timer = controller
      ? window.setTimeout(function () {
          controller.abort();
        }, TIMEOUT)
      : null;

    return fetch(API + path, {
      headers: { Accept: "application/vnd.github+json" },
      signal: controller ? controller.signal : undefined,
      mode: "cors"
    })
      .then(function (response) {
        if (timer) {
          window.clearTimeout(timer);
        }
        // 403/429 here means the unauthenticated rate limit is used up.
        if (!response.ok) {
          throw new Error("GitHub responded " + response.status);
        }
        return response.json();
      })
      .then(function (data) {
        writeCache(path, data);
        return data;
      })
      .catch(function (error) {
        if (timer) {
          window.clearTimeout(timer);
        }
        throw error;
      });
  }

  /** Public, non-fork repositories, most recently updated first. */
  function fetchRepos() {
    return get("/orgs/" + ORG + "/repos?per_page=100&sort=updated").then(function (
      repos
    ) {
      if (!Array.isArray(repos)) {
        return [];
      }
      return repos
        .filter(function (repo) {
          return !repo.fork && repo.html_url && repo.name;
        })
        .slice(0, MAX_REPOS)
        .map(function (repo) {
          return {
            name: repo.name,
            fullName: repo.full_name,
            url: repo.html_url,
            description: repo.description || "",
            language: repo.language || "",
            stars: typeof repo.stargazers_count === "number" ? repo.stargazers_count : null,
            forks: typeof repo.forks_count === "number" ? repo.forks_count : null,
            license: repo.license && repo.license.spdx_id ? repo.license.spdx_id : "",
            updatedAt: repo.updated_at || "",
            topics: Array.isArray(repo.topics) ? repo.topics.slice(0, 4) : []
          };
        });
    });
  }

  /**
   * Contributors across the organization's repositories, merged by login and
   * summed. One extra request per repository, capped by MAX_REPOS.
   */
  function fetchContributors() {
    return fetchRepos().then(function (repos) {
      if (!repos.length) {
        return [];
      }
      return Promise.all(
        repos.map(function (repo) {
          return get("/repos/" + repo.fullName + "/contributors?per_page=50")
            .then(function (list) {
              return Array.isArray(list) ? list : [];
            })
            .catch(function () {
              return [];
            });
        })
      ).then(function (lists) {
        var byLogin = {};
        lists.forEach(function (list) {
          list.forEach(function (person) {
            if (!person || !person.login) {
              return;
            }
            var existing = byLogin[person.login];
            var contributions =
              typeof person.contributions === "number" ? person.contributions : 0;
            if (existing) {
              existing.contributions += contributions;
            } else {
              byLogin[person.login] = {
                login: person.login,
                name: person.name || person.login,
                avatar: person.avatar_url || "",
                url: person.html_url || "https://github.com/" + person.login,
                contributions: contributions
              };
            }
          });
        });

        return Object.keys(byLogin)
          .map(function (login) {
            return byLogin[login];
          })
          .sort(function (a, b) {
            return b.contributions - a.contributions || a.login.localeCompare(b.login);
          });
      });
    });
  }

  window.MDKGitHub = {
    org: ORG,
    fetchRepos: fetchRepos,
    fetchContributors: fetchContributors
  };
})();