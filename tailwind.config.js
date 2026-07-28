/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./**/*.php", "./assets/js/**/*.js", "./template-parts/**/*.php"],
  // Classes users may type into Customizer fields (stored in DB, not scanned).
  safelist: ["text-primary", "text-neutral"],
  theme: {
    extend: {
      // The sidebar switches from its floating glass bar to the full column one
      // pixel above Tailwind's lg, so that a 1024px tablet still gets the bar —
      // the whole 640-1024 range is one layout, the tablet one.
      //
      // Everything tied to that switch uses this screen and never md or lg: the
      // bar itself, the nav items, the Download CV tab, and — in header.php —
      // the divider line and the content's left offset. They must move together
      // or the content indents for a sidebar that is not there. Two more copies
      // of this number live outside Tailwind and have to be kept in step: the
      // drag handle's media query in assets/js/main.js, and the hero type's
      // largest step, which uses xl (1280px) precisely so it cannot land inside
      // this range the way lg (1024px) did.
      screens: {
        nav: "1025px",
      },
      colors: {
        primary: "#020073", // deep navy — headline accents, progress line
        accent: "#e94ad9", // pink — buttons, links, active nav
        "accent-dark": "#000036", // button hover
        paper: "#F6F9F7", // active nav label background
        badge: "#9BD4D7", // Download CV side tab
        neutral: "#808080",
        dark: "#111111",
      },
      fontFamily: {
        sans: ['"Sora"', "sans-serif"],
        hero: ['"Fira Sans"', "sans-serif"],
        mono: ['"Fira Sans"', "sans-serif"],
      },
      // Sidebar geometry lives in CSS variables (see src/input.css) so the
      // sidebar, the divider line and the content offset can never drift apart.
      spacing: {
        sidebar: "var(--sidebar-w)",
        profile: "var(--profile-size)",
      },
      borderRadius: {
        "4xl": "2rem",
      },
      boxShadow: {
        "code-box": "0 4px 20px rgba(0,0,0,0.05)",
        "card-in": "inset 5px 5px 40px 0px rgba(0, 0, 0, 0.066)",
      },
    },
  },
  plugins: [],
};
