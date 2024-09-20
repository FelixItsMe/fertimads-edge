import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./public/js/weather.js"
    ],

    theme: {
        fontFamily: {
            sans: ["Figtree", ...defaultTheme.fontFamily.sans],
        },
        extend: {
            backgroundColor: {
                'primary': '#05CD99'
            }
        }
    },

    plugins: [forms, typography],
};
