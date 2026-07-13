// Shared Alpine state for every static prototype page.
// Mirrors resources/js/app.js in the real Laravel app so behavior/UX ports 1:1.
document.addEventListener('alpine:init', () => {
    Alpine.store('darkMode', {
        on: localStorage.getItem('darkMode') === 'true'
            || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),

        toggle() {
            this.on = !this.on;
            localStorage.setItem('darkMode', this.on);
            document.documentElement.classList.toggle('dark', this.on);
        },
    });

    Alpine.store('sidebar', {
        open: false,
        toggle() {
            this.open = !this.open;
        },
    });
});
