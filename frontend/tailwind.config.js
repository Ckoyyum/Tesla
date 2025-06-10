// tailwind.config.js (Keep as is from previous response)
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./index.html", "./src/**/*.{vue,js,ts,jsx,tsx}"],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: "#6D28D9",
          50: "#F5F3FF",
          100: "#EDE9FE",
          200: "#DDD6FE",
          300: "#C4B5FD",
          400: "#A78BFA",
          500: "#8B5CF6",
          600: "#7C3AED",
          700: "#6D28D9",
          800: "#5B21B6",
          900: "#4C1D95",
          950: "#2E1065",
        },
        accent: {
          DEFAULT: "#EC4899",
          50: "#FFF0F5",
          100: "#FFE8EF",
          200: "#FDD7E4",
          300: "#FBCBE0",
          400: "#FAAFC8",
          500: "#F98EAF",
          600: "#F76D96",
          700: "#EC4899",
          800: "#D6277B",
          900: "#A7185C",
        },
        darkblue: {
          DEFAULT: "#1E293B",
          950: "#0F172A",
        },
      },
      fontFamily: {
        sans: ['"Poppins"', "sans-serif"],
      },
      keyframes: {
        float: {
          "0%, 100%": { transform: "translateY(0px)" },
          "50%": { transform: "translateY(-15px)" },
        },
      },
      animation: {
        "float-subtle": "float 6s ease-in-out infinite",
      },
      boxShadow: {
        "glass-xl":
          "0 10px 30px rgba(0, 0, 0, 0.2), inset 0 0 10px rgba(255, 255, 255, 0.05)",
        "glass-2xl":
          "0 15px 45px rgba(0, 0, 0, 0.25), inset 0 0 15px rgba(255, 255, 255, 0.07)",
      },
    },
  },
  plugins: [],
};
