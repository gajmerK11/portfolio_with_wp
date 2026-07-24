/**
 * Portfolio theme — interactions.
 *
 * 1. Greeting typing intro (every load, first paint).
 * 2. Active nav item follows the section currently in view.
 * 3. Divider line grows along the sidebar/content border on scroll.
 *
 * Typing intro is independent of GSAP; scroll effects need gsap + ScrollTrigger.
 */
(function () {
  "use strict";

  /**
   * Drop any #section hash from the address bar without scrolling or adding a
   * history entry. Keeps the single-page URL clean as the user moves around.
   */
  function stripHash() {
    if (window.location.hash && window.history && window.history.replaceState) {
      window.history.replaceState(
        null,
        "",
        window.location.pathname + window.location.search,
      );
    }
  }

  function init() {
    // A hash may arrive from a bookmarked link or a previous visit; clear it
    // once the page has settled so the URL stays bare.
    stripHash();

    // Run without GSAP so these still work if the CDN fails.
    setupTypingIntro();
    setupProjectsScroll();
    setupProjectCarousels();
    setupNavClicks();

    if (typeof gsap === "undefined" || typeof ScrollTrigger === "undefined") {
      return;
    }
    gsap.registerPlugin(ScrollTrigger);

    setupDividerLine();
    setupActiveNav();
    setupWorkButton();
    setupDownloadCv();

    // Late-loading images (projects, quote) change the page height after
    // triggers are built. Recalculate positions once everything has loaded
    // so the divider line and active nav track scroll in both directions.
    window.addEventListener("load", function () {
      ScrollTrigger.refresh();
    });
  }

  /**
   * Type the greeting rows in sequence (01, then 02, then 03) with a
   * caret. Gated by the .pre-typing class set in the document head, so
   * it runs on every load and respects reduced motion. Preserves the
   * coloured spans by clipping width (mono font).
   */
  function setupTypingIntro() {
    var html = document.documentElement;
    if (!html.classList.contains("pre-typing")) {
      return;
    }

    var greeting = document.getElementById("fp-greeting");
    var rows = greeting ? greeting.querySelectorAll(":scope > div") : [];
    if (!rows.length) {
      html.classList.remove("pre-typing");
      return;
    }

    var items = Array.prototype.map.call(rows, function (row) {
      return { row: row, content: row.querySelector(".type-line") };
    });

    // Lock the block to its finished width before any clipping starts.
    // The rows are laid out full-width at this point (hidden only by
    // opacity), so this is exactly the width it ends at. Without it the
    // centred block would drift sideways as each line's clip changes
    // which row is the widest.
    var lockedWidth = Math.ceil(greeting.getBoundingClientRect().width);
    if (lockedWidth > 0) {
      greeting.style.width = lockedWidth + "px";
    }
    function unlockWidth() {
      greeting.style.width = "";
    }

    var i = 0;
    function typeRow() {
      if (i >= items.length) {
        html.classList.remove("pre-typing");
        unlockWidth();
        return;
      }
      var it = items[i];
      it.row.style.opacity = "1";

      var el = it.content;
      if (!el) {
        i++;
        typeRow();
        return;
      }

      el.style.display = "inline-block";
      el.style.overflow = "hidden";
      el.style.whiteSpace = "nowrap";
      el.style.verticalAlign = "bottom";
      el.style.width = "";
      var full = el.scrollWidth;
      var chars =
        (el.textContent || "").replace(/\s+/g, " ").trim().length || 10;
      var dur = Math.min(2.4, Math.max(0.7, chars * 0.07));

      el.style.width = "0px";
      el.classList.add("is-typing");
      void el.offsetWidth; // reflow so the transition runs.
      el.style.transition = "width " + dur + "s ease-out";
      el.style.width = full + "px";

      var done = function (e) {
        if (e.propertyName !== "width") {
          return;
        }
        el.removeEventListener("transitionend", done);
        // Clear the typing inline styles so the line returns to normal,
        // responsive flow now that it is fully revealed.
        el.style.transition = "";
        el.style.width = "";
        el.style.overflow = "";
        el.style.whiteSpace = "";
        el.classList.remove("is-typing");
        if (i === items.length - 1) {
          // Keep it inline-block so the blinking caret hugs the text end,
          // then drop the caret after a few seconds.
          el.classList.add("is-cursor");
          setTimeout(function () {
            el.classList.remove("is-cursor");
            el.style.display = "";
            el.style.verticalAlign = "";
          }, 3000);
        } else {
          el.style.display = "";
          el.style.verticalAlign = "";
        }
        i++;
        setTimeout(typeRow, 260);
      };
      el.addEventListener("transitionend", done);
    }

    typeRow();
  }

  /**
   * Grow the vertical accent line as the page scrolls down,
   * shrink it back toward the top. Scrubbed to scroll progress.
   */
  function setupDividerLine() {
    var line = document.getElementById("divider-line");
    if (!line) {
      return;
    }

    gsap.fromTo(
      line,
      { scaleY: 0 },
      {
        scaleY: 1,
        ease: "none",
        scrollTrigger: {
          trigger: document.body,
          start: "top top",
          end: "bottom bottom",
          scrub: true,
        },
      },
    );
  }

  /**
   * Light the clicked nav item immediately and smooth-scroll to its section.
   * Runs without GSAP so the selected state always responds to clicks.
   */
  function setupNavClicks() {
    var navItems = document.querySelectorAll(".nav-item[data-target]");
    if (!navItems.length) {
      return;
    }
    navItems.forEach(function (item) {
      item.addEventListener("click", function (e) {
        var section = document.getElementById(item.dataset.target);
        navItems.forEach(function (n) {
          n.classList.toggle("is-active", n === item);
        });
        if (section) {
          e.preventDefault();
          section.scrollIntoView({ behavior: "smooth", block: "start" });
          // preventDefault stops the jump, but the click can still leave the
          // hash behind — strip it so the URL never shows #section.
          stripHash();
        }
      });
    });
  }

  /**
   * Toggle the active nav item based on which section holds the
   * viewport center. Works for any number of [data-section] blocks.
   */
  function setupActiveNav() {
    var sections = document.querySelectorAll("[data-section]");
    var navItems = document.querySelectorAll(".nav-item[data-target]");
    if (!sections.length || !navItems.length) {
      return;
    }

    function setActive(target) {
      navItems.forEach(function (item) {
        item.classList.toggle("is-active", item.dataset.target === target);
      });
    }

    sections.forEach(function (section) {
      ScrollTrigger.create({
        trigger: section,
        start: "top center",
        end: "bottom center",
        onToggle: function (self) {
          if (self.isActive) {
            setActive(section.dataset.section);
          }
        },
      });
    });
  }

  /**
   * Grow and drift the fixed "Work with me" button inward while the
   * About section is on screen, back to the corner when leaving.
   */
  function setupWorkButton() {
    var btn = document.querySelector(".work-btn");
    var about = document.getElementById("about");
    if (!btn || !about) {
      return;
    }
    ScrollTrigger.create({
      trigger: about,
      start: "top 30%",
      onEnter: function () {
        btn.classList.add("is-grown");
      },
      onLeaveBack: function () {
        btn.classList.remove("is-grown");
      },
    });
  }

  /**
   * The "Download CV" tab shows on the hero and again on About — the two
   * places it has something to do — and is tucked away in between. Layering
   * alone can't keep it out of the way over the middle sections: those are
   * transparent, so it would show through the gaps between cards.
   */
  function setupDownloadCv() {
    var tab = document.getElementById("download-cv");
    var home = document.getElementById("home");
    var about = document.getElementById("about");
    if (!tab) {
      return;
    }

    function tuck() {
      tab.classList.add("is-tucked");
    }
    function show() {
      tab.classList.remove("is-tucked");
    }

    // Leaving the hero hides it; coming back up to the hero shows it again.
    if (home) {
      ScrollTrigger.create({
        trigger: home,
        start: "bottom 60%",
        onEnter: tuck,
        onLeaveBack: show,
      });
    }

    // Reaching About brings it back; scrolling back out of About hides it.
    if (about) {
      ScrollTrigger.create({
        trigger: about,
        start: "top 70%",
        onEnter: show,
        onLeaveBack: tuck,
      });
    }
  }

  /**
   * Every [data-carousel] row — project rows and the testimonials row — is its
   * own carousel. The < and > arrows slide it one viewport at a time and it
   * loops endlessly in both directions.
   *
   * The loop is done by appending a second copy of the cards, then silently
   * snapping the scroll position back by one copy's width once the user has
   * scrolled past it. Because the copy is identical, the reset is invisible and
   * the row keeps sliding the same way instead of rewinding to the start.
   *
   * Arrows only exist when the row actually overflows; CSS fades them in on
   * hover (see .carousel-nav).
   */
  function setupProjectsScroll() {
    var rows = document.querySelectorAll("[data-carousel]");
    if (!rows.length) {
      return;
    }

    rows.forEach(function (row) {
      var track = row.querySelector("[data-carousel-track]");
      var prev = row.querySelector("[data-carousel-prev]");
      var next = row.querySelector("[data-carousel-next]");
      if (!track) {
        return;
      }

      var looping = false;
      var settle = null;

      /** Width of one copy of the cards. */
      function loopWidth() {
        return looping ? track.scrollWidth / 2 : track.scrollWidth;
      }

      /** Jump without animating, whatever scroll-behavior the CSS asks for. */
      function jumpTo(left) {
        var previous = track.style.scrollBehavior;
        track.style.scrollBehavior = "auto";
        track.scrollLeft = left;
        track.style.scrollBehavior = previous;
      }

      function addClones() {
        if (looping) {
          return;
        }
        Array.prototype.slice.call(track.children).forEach(function (card) {
          var clone = card.cloneNode(true);
          clone.classList.add("is-clone");
          clone.setAttribute("aria-hidden", "true");
          // Clones are decorative duplicates — keep them off the tab order.
          clone.querySelectorAll("a, button").forEach(function (el) {
            el.setAttribute("tabindex", "-1");
          });
          track.appendChild(clone);
        });
        looping = true;
      }

      function removeClones() {
        if (!looping) {
          return;
        }
        track.querySelectorAll(".is-clone").forEach(function (clone) {
          clone.remove();
        });
        looping = false;
      }

      function refresh() {
        // Measure the real cards, so the clones can't make a row look like it
        // overflows when it doesn't.
        removeClones();
        var overflow = track.scrollWidth - track.clientWidth > 4;

        [prev, next].forEach(function (btn) {
          if (btn) {
            btn.style.display = overflow ? "flex" : "none";
          }
        });
        // Centre the cards while they all fit; once the row scrolls, centring
        // would clip the first card out of reach.
        track.classList.toggle("is-centered", !overflow);

        if (overflow) {
          addClones();
        } else {
          jumpTo(0);
        }
      }

      // After any scroll settles, fold the position back into the first copy.
      track.addEventListener("scroll", function () {
        window.clearTimeout(settle);
        settle = window.setTimeout(function () {
          if (!looping) {
            return;
          }
          var width = loopWidth();
          if (track.scrollLeft >= width) {
            jumpTo(track.scrollLeft - width);
          }
        }, 140);
      });

      if (next) {
        next.addEventListener("click", function () {
          track.scrollBy({ left: track.clientWidth, behavior: "smooth" });
        });
      }

      if (prev) {
        prev.addEventListener("click", function () {
          // Going back from the very start: hop forward a whole copy first, so
          // there is something to scroll back into.
          if (looping && track.scrollLeft <= 4) {
            jumpTo(loopWidth());
          }
          track.scrollBy({ left: -track.clientWidth, behavior: "smooth" });
        });
      }

      refresh();
      window.addEventListener("resize", refresh);
    });
  }

  /**
   * Auto-advance each project card's image carousel by sliding the track
   * horizontally, ping-ponging through the slides (0 → last → 0) so the motion
   * reads left→right then right→left. Cards with a single slide stay static.
   */
  function setupProjectCarousels() {
    var carousels = document.querySelectorAll(".project-carousel");
    if (!carousels.length) {
      return;
    }
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
      return;
    }

    carousels.forEach(function (car) {
      var track = car.querySelector(".pc-track");
      var slides = car.querySelectorAll(".pc-slide");
      if (!track || slides.length < 2) {
        return;
      }
      var i = 0;
      var dir = 1;

      setInterval(function () {
        i += dir;
        if (i >= slides.length - 1) {
          dir = -1;
        } else if (i <= 0) {
          dir = 1;
        }
        track.style.transform = "translateX(" + -i * 100 + "%)";
      }, 3200);
    });
  }


  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
