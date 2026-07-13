import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

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

Alpine.start();
