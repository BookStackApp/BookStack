/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
      './resources/**/*.{html,js,php}'
    ],
    theme: {
        extend: {},
    },
    mode: 'jit',
    corePlugins: {
        preflight: false,
    }
}
;