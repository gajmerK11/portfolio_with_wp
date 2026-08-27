/**
 * 404 "cosmos" — an endless fall through a starfield.
 *
 * Stars live in a 3D frustum: a point on a disc, plus a depth z in (0,1]. Each
 * frame every z shrinks, and the star is drawn as the line between where it
 * projected last frame and where it projects now — which is what makes the
 * streaks, and why they lengthen toward the edges without any length being
 * written anywhere.
 *
 * The tails run longer than one frame's travel because the canvas is never
 * cleared, only veiled with translucent black: what survives the veil is the
 * fading tail of every streak.
 *
 * There is no end state. The ramp eases up to cruise speed once, and after
 * that it simply falls.
 */
(function () {
  "use strict";

  var canvas = document.getElementById("cosmos-warp");
  if (!canvas || !canvas.getContext) {
    return;
  }
  var ctx = canvas.getContext("2d");
  if (!ctx) {
    return;
  }

  var reduce =
    window.matchMedia &&
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  var NEAR = 0.06; // Depth at which a star has passed the viewer.
  var CRUISE = 0.0072; // Depth eaten per frame at full speed (~60fps).
  var RAMP = 2600; // ms from rest to cruise, once, at load.
  var VEIL = 0.22; // Black laid over each frame — lower means longer tails.

  var stars = [];
  var w = 0;
  var h = 0;
  var cx = 0;
  var cy = 0;
  var dpr = 1;
  var spread = 0;

  var started = 0;
  var raf = null;
  var resizeTimer = null;

  // Vanishing point drift. The fall is dead centre otherwise, and a void that
  // never shifts reads as a screensaver; following the pointer a little makes
  // it feel like the page is falling rather than playing.
  var aimX = 0;
  var aimY = 0;
  var driftX = 0;
  var driftY = 0;

  function rand(min, max) {
    return min + Math.random() * (max - min);
  }

  /**
   * Sampled on a disc, not a square: a square puts far more stars along the
   * diagonals than along the axes, and the bunching shows as brighter corners
   * once everything radiates from one point. sqrt() on the radius is what keeps
   * the disc even rather than crowding the centre.
   *
   * @param {number} z Starting depth.
   */
  function makeStar(z) {
    var a = rand(0, Math.PI * 2);
    var r = Math.sqrt(Math.random());
    return {
      x: Math.cos(a) * r,
      y: Math.sin(a) * r,
      z: z,
      pz: z
    };
  }

  function resize() {
    // Capped at 2: past that the pixel count costs more than the sharpness is
    // worth on a full-screen canvas repainted every frame.
    dpr = Math.min(window.devicePixelRatio || 1, 2);
    w = canvas.clientWidth || window.innerWidth;
    h = canvas.clientHeight || window.innerHeight;

    canvas.width = Math.max(1, Math.round(w * dpr));
    canvas.height = Math.max(1, Math.round(h * dpr));
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

    cx = w / 2;
    cy = h / 2;
    spread = Math.max(w, h) * 0.62;

    // Density by area, so a phone is not drawing a desktop's star count.
    var target = Math.round(Math.min(1100, Math.max(240, (w * h) / 1900)));
    while (stars.length < target) {
      stars.push(makeStar(rand(NEAR, 1)));
    }
    stars.length = target;

    ctx.fillStyle = "#000";
    ctx.fillRect(0, 0, w, h);
    ctx.strokeStyle = "#fff";
    ctx.lineCap = "round";
  }

  function frame(now) {
    raf = window.requestAnimationFrame(frame);

    if (!started) {
      started = now;
    }
    var t = Math.min(1, (now - started) / RAMP);
    var speed = CRUISE * (t * t * (3 - 2 * t)); // smoothstep

    driftX += (aimX - driftX) * 0.035;
    driftY += (aimY - driftY) * 0.035;
    var ox = cx + driftX * w * 0.06;
    var oy = cy + driftY * h * 0.06;

    ctx.fillStyle = "rgba(0,0,0," + VEIL + ")";
    ctx.fillRect(0, 0, w, h);

    var margin = spread;
    for (var i = 0; i < stars.length; i++) {
      var s = stars[i];
      s.pz = s.z;
      s.z -= speed;

      if (s.z < NEAR) {
        stars[i] = makeStar(1);
        continue;
      }

      var kx = s.x * spread;
      var ky = s.y * spread;
      var x2 = ox + kx / s.z;
      var y2 = oy + ky / s.z;

      // Gone past the edge: recycle now rather than keep projecting a star
      // nobody can see until its depth finally runs out.
      if (x2 < -margin || x2 > w + margin || y2 < -margin || y2 > h + margin) {
        stars[i] = makeStar(1);
        continue;
      }

      var near = 1 - s.z; // 0 far, 1 close.
      ctx.globalAlpha = Math.min(1, 0.14 + near * near * 1.1);
      ctx.lineWidth = 0.35 + near * near * 2.1;
      ctx.beginPath();
      ctx.moveTo(ox + kx / s.pz, oy + ky / s.pz);
      ctx.lineTo(x2, y2);
      ctx.stroke();
    }
    ctx.globalAlpha = 1;
  }

  /** Reduced motion: the same sky, standing still. */
  function still() {
    ctx.fillStyle = "#000";
    ctx.fillRect(0, 0, w, h);
    ctx.fillStyle = "#fff";
    for (var i = 0; i < stars.length; i++) {
      var s = stars[i];
      var x = cx + (s.x * spread) / s.z;
      var y = cy + (s.y * spread) / s.z;
      if (x < 0 || x > w || y < 0 || y > h) {
        continue;
      }
      var near = 1 - s.z;
      ctx.globalAlpha = 0.25 + near * 0.6;
      ctx.beginPath();
      ctx.arc(x, y, 0.4 + near * 1.5, 0, Math.PI * 2);
      ctx.fill();
    }
    ctx.globalAlpha = 1;
  }

  function start() {
    if (raf === null) {
      raf = window.requestAnimationFrame(frame);
    }
  }

  function stop() {
    if (raf !== null) {
      window.cancelAnimationFrame(raf);
      raf = null;
    }
  }

  resize();

  if (reduce) {
    still();
    window.addEventListener("resize", function () {
      resize();
      still();
    });
    return;
  }

  window.addEventListener("pointermove", function (e) {
    aimX = (e.clientX / w) * 2 - 1;
    aimY = (e.clientY / h) * 2 - 1;
  });

  // Resizing reseeds the field, so it is throttled — a dragged window edge
  // would otherwise rebuild a thousand stars on every frame of the drag.
  window.addEventListener("resize", function () {
    window.clearTimeout(resizeTimer);
    resizeTimer = window.setTimeout(resize, 150);
  });

  // A hidden tab still runs rAF in some browsers, and this one paints the whole
  // viewport every frame. Nothing is lost by parking it.
  document.addEventListener("visibilitychange", function () {
    if (document.hidden) {
      stop();
    } else {
      start();
    }
  });

  start();
})();
