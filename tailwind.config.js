/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./**/*.php", "./assets/js/**/*.js", "./template-parts/**/*.php"],
  theme: {
    extend: {
      colors: {
        primary: "#EB4526",
        neutral: "#999999",
        dark: "#111111",
      },
      fontFamily: {
        mono: ['"Fira Code"', "monospace"],
        sans: ['"Inter"', "sans-serif"],
      },
      borderRadius: {
        "4xl": "2rem",
      },
      boxShadow: {
        "code-box": "0 4px 20px rgba(0,0,0,0.05)",
        "btn-glow": "0 0 20px rgba(235, 69, 38, 0.4)",
      },
    },
  },
  plugins: [],
};
