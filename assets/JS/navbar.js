// Mobile menu toggle script
const menuButton = document.getElementById('mobile-menu-button');
const mobileMenu = document.getElementById('mobile-menu');

menuButton.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
});

// JavaScript to toggle the dropdown menu
const userMenuButton = document.getElementById('user-menu-button');
const userMenu = document.getElementById('user-menu');

userMenuButton.addEventListener('click', () => {
    // Toggle the 'hidden' class to show or hide the dropdown menu
    userMenu.classList.toggle('hidden');
});

// Close the dropdown menu when clicking outside
document.addEventListener('click', (event) => {
    if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
        userMenu.classList.add('hidden');
    }
});