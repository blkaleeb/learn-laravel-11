/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                primary: "#0072c6",
                secondary: "#ffb119",
                dark: "#0f172a",
            },
        },
    },
    plugins: [],
};
