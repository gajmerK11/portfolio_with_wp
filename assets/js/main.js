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

    // Hold the typing intro until the logo overlay is gone, so the greeting
    // starts on a visible page rather than behind the preloader.
    setupPreloader(setupTypingIntro);

    // Run without GSAP so these still work if the CDN fails.
    setupProjectsScroll();
    setupProjectCarousels();
    setupNavClicks();
    setupSidebarDrag();
    setupContactPanel();
    setupAboutMascot();

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
   * Hold the "Kumar" logo overlay over the page until the window has finished
   * loading (with a short minimum so it doesn't just flash), then fade it out
   * and take it out of the flow. Reduced motion skips the minimum hold.
   *
   * onDone runs once the overlay is fully gone, so callers can start on-screen
   * animations (the typing intro) only after the page is actually visible. When
   * there is no overlay, onDone runs immediately.
   *
   * @param {Function} [onDone] Called after the overlay has been removed.
   */
  function setupPreloader(onDone) {
    var done = typeof onDone === "function" ? onDone : function () {};
    var pl = document.getElementById("site-preloader");
    if (!pl) {
      done();
      return;
    }
    var reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    // Hold long enough for a couple of pulse cycles before fading.
    var minShow = reduce ? 0 : 2400;
    var start = Date.now();

    function hide() {
      var wait = Math.max(0, minShow - (Date.now() - start));
      setTimeout(function () {
        pl.classList.add("is-hidden");
        // Remove from the flow once the fade finishes, then hand off.
        setTimeout(function () {
          pl.style.display = "none";
          done();
        }, 520);
      }, wait);
    }

    if (document.readyState === "complete") {
      hide();
    } else {
      window.addEventListener("load", hide);
    }
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
        // Normally already done by the last row's transitionend; this covers
        // rows that had no content to type.
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
          // Last row is fully revealed: release the gate now rather than on
          // the next tick, so the quote starts fading in on the final
          // character instead of after the inter-row pause.
          html.classList.remove("pre-typing");
          unlockWidth();

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
   * Let the headphoned mascot pace the foot of the About section while that
   * strip is on screen.
   *
   * The observer only ever switches it on, then disconnects. Toggling the
   * class off when the strip scrolled away meant every return trip restarted
   * the CSS animations from their first frame, so the creature snapped back to
   * the left edge and the border streak jumped. Once started it keeps pacing
   * for the rest of the session, independent of scrolling.
   *
   * Gated on first sight rather than started at load so a visitor who never
   * reaches About never pays for it.
   *
   * No IntersectionObserver (or reduced motion requested) means it simply
   * never starts, which is the correct outcome either way — it is pure
   * decoration.
   */
  function setupAboutMascot() {
    var strip = document.querySelector(".mascot--about");
    if (!strip || !("IntersectionObserver" in window)) {
      return;
    }
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
      return;
    }

    var observer = new IntersectionObserver(
      function (entries) {
        var entry = entries[0];
        if (!entry || !entry.isIntersecting) {
          return;
        }
        strip.classList.add("is-walking");
        observer.disconnect();
      },
      { threshold: 0 }
    );
    observer.observe(strip);
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
   * Slide-in contact panel. "Work with me" (and any [data-contact-open]) opens
   * it; the Back button, the overlay, and Escape close it. Floating labels
   * reveal as their field fills. Submit posts to admin-ajax (wp_mail).
   */
  function setupContactPanel() {
    var panel = document.querySelector(".contact-panel");
    var overlay = document.querySelector(".contact-back");
    if (!panel || !overlay) {
      return;
    }
    var openers = document.querySelectorAll("[data-contact-open]");
    var closers = document.querySelectorAll("[data-contact-close]");

    function open(e) {
      if (e) {
        e.preventDefault();
      }
      panel.classList.add("is-open");
      overlay.classList.add("is-open");
      panel.setAttribute("aria-hidden", "false");
      var first = panel.querySelector("input, textarea");
      if (first) {
        // Wait out the slide before focusing, so the page doesn't jump.
        setTimeout(function () {
          first.focus();
        }, 520);
      }
    }
    function close() {
      panel.classList.remove("is-open");
      overlay.classList.remove("is-open");
      panel.setAttribute("aria-hidden", "true");
    }

    openers.forEach(function (btn) {
      btn.addEventListener("click", open);
    });
    closers.forEach(function (btn) {
      btn.addEventListener("click", close);
    });
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && panel.classList.contains("is-open")) {
        close();
      }
    });

    // Floating labels: show the .cp-cour above a field once it has content.
    var form = document.getElementById("cp-form");
    if (!form) {
      return;
    }
    form.querySelectorAll("input, textarea").forEach(function (field) {
      var group = field.closest(".cp-group");
      var label = group ? group.previousElementSibling : null;
      if (!label || !label.classList.contains("cp-cour")) {
        return;
      }
      field.addEventListener("input", function () {
        label.classList.toggle("is-shown", field.value.trim() !== "");
      });
    });

    setupContactSubmit(form, close);
  }

  /** Wire the contact form's AJAX submit + success / error messaging. */
  function setupContactSubmit(form, close) {
    var cfg = window.PortfolioContact;
    var err = form.querySelector(".cp-err");
    var succ = form.querySelector(".cp-succ");
    var button = form.querySelector('button[type="submit"]');

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      err.textContent = "";
      succ.textContent = "";

      var data = new FormData(form);
      var email = (data.get("email") || "").trim();
      var name = (data.get("name") || "").trim();
      var message = (data.get("message") || "").trim();

      if (!email || !name || !message) {
        err.textContent = "Please fill in every field.";
        return;
      }
      if (!cfg || !cfg.ajax) {
        err.textContent = "Contact form is not configured.";
        return;
      }

      var body = new URLSearchParams();
      body.set("action", "portfolio_contact");
      body.set("nonce", cfg.nonce);
      body.set("email", email);
      body.set("name", name);
      body.set("message", message);

      button.disabled = true;
      var original = button.textContent;
      button.textContent = "Sending…";

      fetch(cfg.ajax, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: body.toString(),
      })
        .then(function (r) {
          return r.json();
        })
        .then(function (res) {
          if (res && res.success) {
            succ.textContent =
              (res.data && res.data.message) || "Message sent. Thank you!";
            form.reset();
            form.querySelectorAll(".cp-cour").forEach(function (l) {
              l.classList.remove("is-shown");
            });
          } else {
            err.textContent =
              (res && res.data && res.data.message) ||
              "Something went wrong. Please try again.";
          }
        })
        .catch(function () {
          err.textContent = "Network error. Please try again.";
        })
        .finally(function () {
          button.disabled = false;
          button.textContent = original;
        });
    });
  }

  /**
   * Every [data-carousel] row — project rows and the testimonials row — is its
   * own carousel. The < and > arrows advance it one card per click and it
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

      /**
       * Distance one copy of the cards covers — i.e. the offset of the first
       * clone, which includes the gap that follows the last real card. Halving
       * scrollWidth misses that gap, which left the fold a gap-width off; the
       * arrows now land on exact card boundaries, so that slop would show as a
       * short step every time the loop wrapped.
       */
      function loopWidth() {
        var clone = looping ? track.querySelector(".is-clone") : null;
        if (!clone) {
          return track.scrollWidth;
        }
        var origin = track.getBoundingClientRect().left - track.scrollLeft;
        return Math.round(clone.getBoundingClientRect().left - origin);
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

      /**
       * Scroll offset of every card within the track, ascending.
       *
       * Measured off the live boxes rather than derived from a card width plus
       * a gap: the gap lives in CSS and card widths can differ, so a computed
       * fixed step would drift out of alignment after a few clicks. Reading the
       * boxes also keeps this independent of which ancestor each card happens
       * to be positioned against.
       */
      function cardOffsets() {
        var origin = track.getBoundingClientRect().left - track.scrollLeft;
        return Array.prototype.map.call(track.children, function (card) {
          return Math.round(card.getBoundingClientRect().left - origin);
        });
      }

      /**
       * Slide to the next card boundary in `dir` (+1 forward, -1 back) — one
       * card per click, rather than a whole viewport's worth at a time.
       */
      function step(dir) {
        var offsets = cardOffsets();
        var at = track.scrollLeft;
        var target = dir > 0 ? track.scrollWidth : 0;

        for (var i = 0; i < offsets.length; i++) {
          // A couple of px of slack: a settled smooth scroll rarely lands on
          // the exact integer offset it was aimed at.
          if (dir > 0 && offsets[i] > at + 2) {
            target = offsets[i];
            break;
          }
          // Offsets ascend, so the last one still behind us is the nearest.
          if (dir < 0 && offsets[i] < at - 2) {
            target = offsets[i];
          }
        }

        track.scrollTo({ left: target, behavior: "smooth" });
      }

      if (next) {
        next.addEventListener("click", function () {
          step(1);
        });
      }

      if (prev) {
        prev.addEventListener("click", function () {
          // Going back from the very start: hop forward a whole copy first, so
          // there is something to scroll back into.
          if (looping && track.scrollLeft <= 4) {
            jumpTo(loopWidth());
          }
          step(-1);
        });
      }

      refresh();
      window.addEventListener("resize", refresh);
    });
  }

  /**
   * Let the floating nav bar be dragged clear of whatever it is covering on
   * small screens. The reference does this with GSAP Draggable
   * (index.html:1186); Pointer Events cover it without the extra dependency,
   * and the reference's `inertia: true` needs a paid GSAP plugin anyway.
   *
   * CSS centres the bar with `top: 50%` plus a `translateY(-50%)`, which a drag
   * cannot build on — moving it would fight the centring transform. So the
   * first drag freezes the bar into plain left/top pixels read off its current
   * box and drops the transform; from then on the drag only moves those. The
   * inline styles are cleared again if the viewport grows into the column
   * layout, handing position back to CSS.
   *
   * Only the handle drags, never the bar itself, so a tap on a nav icon still
   * navigates.
   */
  function setupSidebarDrag() {
    var bar = document.querySelector(".site-sidebar");
    var handle = bar && bar.querySelector("[data-sidebar-drag]");
    if (!handle || !window.PointerEvent) {
      return;
    }

    // Must match the `nav` screen in tailwind.config.js — the width the bar's
    // two layouts switch at. A 768px tablet still gets the floating bar.
    var column = window.matchMedia("(min-width: 769px)");
    var EDGE = 8;
    var STEP = 16;
    var placed = false;
    var startX = 0;
    var startY = 0;
    var originX = 0;
    var originY = 0;

    /** Freeze the CSS-centred bar into absolute pixels a drag can move. */
    function place() {
      // Measured before the transform is dropped, so the bar does not shift.
      var rect = bar.getBoundingClientRect();
      bar.style.transform = "none";
      bar.style.right = "auto";
      bar.style.bottom = "auto";
      bar.style.left = rect.left + "px";
      bar.style.top = rect.top + "px";
      placed = true;
    }

    /** Hand positioning back to CSS. */
    function release() {
      bar.style.transform = "";
      bar.style.right = "";
      bar.style.bottom = "";
      bar.style.left = "";
      bar.style.top = "";
      placed = false;
    }

    /** Move to left/top, kept fully on screen. */
    function moveTo(left, top) {
      var rect = bar.getBoundingClientRect();
      var maxLeft = Math.max(EDGE, window.innerWidth - rect.width - EDGE);
      var maxTop = Math.max(EDGE, window.innerHeight - rect.height - EDGE);
      bar.style.left = Math.max(EDGE, Math.min(left, maxLeft)) + "px";
      bar.style.top = Math.max(EDGE, Math.min(top, maxTop)) + "px";
    }

    function currentLeft() {
      return parseFloat(bar.style.left) || 0;
    }

    function currentTop() {
      return parseFloat(bar.style.top) || 0;
    }

    handle.addEventListener("pointerdown", function (event) {
      if (column.matches) {
        return;
      }
      if (!placed) {
        place();
      }
      startX = event.clientX;
      startY = event.clientY;
      originX = currentLeft();
      originY = currentTop();
      bar.classList.add("is-dragging");
      // Capture keeps the moves coming even when the pointer outruns the
      // handle, which it will — the handle is only a few px across.
      handle.setPointerCapture(event.pointerId);
      event.preventDefault();
    });

    handle.addEventListener("pointermove", function (event) {
      if (!bar.classList.contains("is-dragging")) {
        return;
      }
      moveTo(
        originX + (event.clientX - startX),
        originY + (event.clientY - startY)
      );
    });

    function stop() {
      bar.classList.remove("is-dragging");
    }
    handle.addEventListener("pointerup", stop);
    handle.addEventListener("pointercancel", stop);

    // Arrow keys nudge it too, so the handle is not a pointer-only control.
    handle.addEventListener("keydown", function (event) {
      var dx = 0;
      var dy = 0;
      if ("ArrowLeft" === event.key) {
        dx = -1;
      } else if ("ArrowRight" === event.key) {
        dx = 1;
      } else if ("ArrowUp" === event.key) {
        dy = -1;
      } else if ("ArrowDown" === event.key) {
        dy = 1;
      } else {
        return;
      }
      if (column.matches) {
        return;
      }
      if (!placed) {
        place();
      }
      moveTo(currentLeft() + dx * STEP, currentTop() + dy * STEP);
      event.preventDefault();
    });

    // A bar left near an edge must not end up off screen when the window
    // changes, nor stay pinned in pixels once it is a column again.
    window.addEventListener("resize", function () {
      if (!placed) {
        return;
      }
      if (column.matches) {
        release();
        return;
      }
      moveTo(currentLeft(), currentTop());
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

      // Hovering fans the slides out into a grid (CSS), so advancing underneath
      // it would be invisible churn — and would land on a different slide than
      // the one the pointer left. Freeze the index while the card is hovered.
      var paused = false;
      var card = car.closest(".project-card");
      if (card) {
        card.addEventListener("mouseenter", function () {
          paused = true;
        });
        card.addEventListener("mouseleave", function () {
          paused = false;
        });
      }

      setInterval(function () {
        if (paused) {
          return;
        }
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
