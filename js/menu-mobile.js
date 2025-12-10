// Menu sanduíche responsivo
document.addEventListener('DOMContentLoaded', function() {
    const btnMenuMobile = document.getElementById('btn-menu-mobile');
    const menuMobile = document.getElementById('menu-mobile');
    const menuMobileItems = document.querySelectorAll('.menu-mobile-item');

    if (btnMenuMobile && menuMobile) {
        // Toggle menu ao clicar no botão
        btnMenuMobile.addEventListener('click', function() {
            btnMenuMobile.classList.toggle('active');
            menuMobile.classList.toggle('active');
        });

        // Fechar menu ao clicar em um item
        menuMobileItems.forEach(item => {
            item.addEventListener('click', function() {
                btnMenuMobile.classList.remove('active');
                menuMobile.classList.remove('active');
            });
        });

        // Fechar menu ao clicar fora
        document.addEventListener('click', function(event) {
            if (!event.target.closest('#menu_principal')) {
                btnMenuMobile.classList.remove('active');
                menuMobile.classList.remove('active');
            }
        });
    }
});
