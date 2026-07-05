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

  function init() {
    // Runs without GSAP so the intro still plays if the CDN fails.
    setupTypingIntro();

    if (typeof gsap === "undefined" || typeof ScrollTrigger === "undefined") {
      return;
    }
    gsap.registerPlugin(ScrollTrigger);

    setupDividerLine();
    setupActiveNav();
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

    var i = 0;
    function typeRow() {
      if (i >= items.length) {
        html.classList.remove("pre-typing");
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

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
