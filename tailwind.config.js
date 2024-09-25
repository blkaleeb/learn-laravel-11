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
                primary: "#14b8a6",
                secondary: "#64748b",
                dark: "#0f172a",
            },
        },
    },
    plugins: [],
};
