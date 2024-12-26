const menuButton = document.getElementById('mobile-menu-button');
const mobileMenu = document.getElementById('mobile-menu');

menuButton.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
});

const userMenuButton = document.getElementById('user-menu-button');
const userMenu = document.getElementById('user-menu');

// userMenuButton & userMenu are null when the user is not connected
if (userMenuButton && userMenu) {
    userMenuButton.addEventListener('click', () => {
        userMenu.classList.toggle('hidden');
    });

    document.addEventListener('click', (event) => {
        if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
            userMenu.classList.add('hidden');
        }
    });
}

