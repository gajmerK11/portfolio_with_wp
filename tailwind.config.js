/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./**/*.php", "./assets/js/**/*.js", "./template-parts/**/*.php"],
  // Classes users may type into Customizer fields (stored in DB, not scanned).
  safelist: ["text-primary", "text-neutral"],
  theme: {
    extend: {
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
        panel: "5px 5px 40px 0px rgba(255, 0, 0, 0.1)",
        "card-in": "inset 5px 5px 40px 0px rgba(0, 0, 0, 0.066)",
      },
    },
  },
  plugins: [],
};
