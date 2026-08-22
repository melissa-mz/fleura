// =====================================================
// FLEURA — JavaScript principal
// =====================================================

document.addEventListener('DOMContentLoaded', () => {

    // =================================================
    // NAVBAR
    // =================================================

    const navbar = document.getElementById('navbar');
    const burger = document.getElementById('burger');
    const navMenu = document.getElementById('navMenu');

    const articlesDropdown =
        document.getElementById('articlesDropdown');

    const articlesTrigger =
        articlesDropdown
            ? articlesDropdown.querySelector(
                '.navbar__dropdown-trigger'
            )
            : null;

    const articlesPanel =
        articlesDropdown
            ? articlesDropdown.querySelector(
                '.navbar__dropdown-panel'
            )
            : null;


    // =================================================
    // NAVBAR — EFFET AU SCROLL
    // =================================================

    if (navbar) {

        const onScroll = () => {

            if (window.scrollY > 30) {

                navbar.classList.add('scrolled');

            } else {

                navbar.classList.remove('scrolled');

            }

        };

        window.addEventListener(
            'scroll',
            onScroll,
            { passive: true }
        );

        onScroll();
    }


    // =================================================
    // RECHERCHE — PANNEAU DYNAMIQUE
    // =================================================

    const searchToggle = document.getElementById('searchToggle');
    const searchPanel = document.getElementById('searchPanel');
    const searchOverlay = document.getElementById('searchOverlay');
    const searchClose = document.getElementById('searchClose');
    const searchInput = document.getElementById('searchInput');

    function openSearchPanel() {

        if (!searchPanel) {
            return;
        }

        // Fermer le menu burger s'il est ouvert
        closeMobileMenu();

        searchPanel.classList.add('open');

        if (searchOverlay) {
            searchOverlay.classList.add('open');
        }

        if (searchToggle) {
            searchToggle.setAttribute('aria-expanded', 'true');
        }

        document.body.classList.add('search-open');

        setTimeout(() => {

            if (searchInput) {
                searchInput.focus();
            }

        }, 200);

    }

    function closeSearchPanel() {

        if (!searchPanel) {
            return;
        }

        searchPanel.classList.remove('open');

        if (searchOverlay) {
            searchOverlay.classList.remove('open');
        }

        if (searchToggle) {
            searchToggle.setAttribute('aria-expanded', 'false');
        }

        document.body.classList.remove('search-open');

    }

    if (searchToggle && searchPanel) {

        searchToggle.addEventListener(
            'click',
            (event) => {

                event.preventDefault();
                event.stopPropagation();

                const isOpen =
                    searchPanel.classList.contains('open');

                if (isOpen) {

                    closeSearchPanel();

                } else {

                    openSearchPanel();

                }

            }
        );

    }

    if (searchClose) {

        searchClose.addEventListener(
            'click',
            closeSearchPanel
        );

    }

    if (searchOverlay) {

        searchOverlay.addEventListener(
            'click',
            closeSearchPanel
        );

    }

    // Fermer si on clique en dehors du panneau (hors overlay/toggle déjà gérés)
    document.addEventListener(
        'click',
        (event) => {

            if (!searchPanel || !searchToggle) {
                return;
            }

            const isOpen =
                searchPanel.classList.contains('open');

            if (!isOpen) {
                return;
            }

            const clickedInsidePanel =
                searchPanel.contains(event.target);

            const clickedToggle =
                searchToggle.contains(event.target);

            if (!clickedInsidePanel && !clickedToggle) {

                closeSearchPanel();

            }

        }
    );

    // ESC — fermer la recherche
    document.addEventListener(
        'keydown',
        (event) => {

            if (
                (event.key === 'Escape' || event.key === 'Esc') &&
                searchPanel &&
                searchPanel.classList.contains('open')
            ) {

                closeSearchPanel();

            }

        }
    );


    // =================================================
    // FONCTION : FERMER LE MENU MOBILE
    // =================================================

    function closeMobileMenu() {

        if (!burger || !navMenu) {
            return;
        }

        burger.classList.remove('active');

        navMenu.classList.remove('open');

        burger.setAttribute(
            'aria-expanded',
            'false'
        );

        burger.setAttribute(
            'aria-label',
            'Ouvrir le menu'
        );

        document.body.classList.remove(
            'menu-open'
        );

        // Fermer également Articles
        if (articlesDropdown) {

            articlesDropdown.classList.remove(
                'open'
            );

        }

        if (articlesTrigger) {

            articlesTrigger.setAttribute(
                'aria-expanded',
                'false'
            );

        }

    }


    // =================================================
    // FONCTION : OUVRIR LE MENU MOBILE
    // =================================================

    function openMobileMenu() {

        if (!burger || !navMenu) {
            return;
        }

        // Fermer la recherche si elle est ouverte
        closeSearchPanel();

        burger.classList.add('active');

        navMenu.classList.add('open');

        burger.setAttribute(
            'aria-expanded',
            'true'
        );

        burger.setAttribute(
            'aria-label',
            'Fermer le menu'
        );

        document.body.classList.add(
            'menu-open'
        );

    }


    // =================================================
    // HAMBURGER
    // =================================================

    if (burger && navMenu) {

        burger.addEventListener(
            'click',
            (event) => {

                event.preventDefault();

                event.stopPropagation();

                const isOpen =
                    navMenu.classList.contains(
                        'open'
                    );

                if (isOpen) {

                    closeMobileMenu();

                } else {

                    openMobileMenu();

                }

            }
        );

    }


    // =================================================
    // ARTICLES — DROPDOWN MOBILE
    // =================================================

    if (
        articlesDropdown &&
        articlesTrigger &&
        articlesPanel
    ) {

        articlesTrigger.addEventListener(
            'click',
            (event) => {

                event.preventDefault();

                event.stopPropagation();

                const isOpen =
                    articlesDropdown.classList.contains(
                        'open'
                    );


                // -----------------------------------------
                // OUVRIR
                // -----------------------------------------

                if (!isOpen) {

                    articlesDropdown.classList.add(
                        'open'
                    );

                    articlesTrigger.setAttribute(
                        'aria-expanded',
                        'true'
                    );

                }


                // -----------------------------------------
                // FERMER
                // -----------------------------------------

                else {

                    articlesDropdown.classList.remove(
                        'open'
                    );

                    articlesTrigger.setAttribute(
                        'aria-expanded',
                        'false'
                    );

                }

            }
        );

    }


    // =================================================
    // LIENS DU MENU
    // =================================================

    if (navMenu) {

        const menuLinks =
            navMenu.querySelectorAll(
                'a'
            );


        menuLinks.forEach((link) => {

            link.addEventListener(
                'click',
                () => {

                    /*
                     * Si on clique sur un lien :
                     * on ferme le menu mobile.
                     *
                     * Cela concerne aussi les
                     * catégories dans Articles.
                     */

                    closeMobileMenu();

                }
            );

        });

    }


    // =================================================
    // FERMER SI ON CLIQUE SUR L'OVERLAY
    // =================================================

    document.addEventListener(
        'click',
        (event) => {

            if (!navMenu || !burger) {
                return;
            }

            const isMenuOpen =
                navMenu.classList.contains(
                    'open'
                );

            if (!isMenuOpen) {
                return;
            }


            const clickedInsideMenu =
                navMenu.contains(
                    event.target
                );

            const clickedBurger =
                burger.contains(
                    event.target
                );


            if (
                !clickedInsideMenu &&
                !clickedBurger
            ) {

                closeMobileMenu();

            }

        }
    );


    // =================================================
    // ESC — FERMER LE MENU
    // =================================================

    document.addEventListener(
        'keydown',
        (event) => {

            if (
                event.key === 'Escape' ||
                event.key === 'Esc'
            ) {

                closeMobileMenu();

            }

        }
    );


    // =================================================
    // RESPONSIVE
    // Fermer le menu si on repasse en desktop
    // =================================================

    window.addEventListener(
        'resize',
        () => {

            if (
                window.innerWidth > 900
            ) {

                closeMobileMenu();

            }

        }
    );


    // =================================================
    // HERO PARALLAX
    // =================================================

    const heroImage =
        document.querySelector(
            '.hero__image'
        );

    if (heroImage) {

        window.addEventListener(
            'scroll',
            () => {

                const scrolled =
                    window.scrollY;

                if (
                    scrolled <
                    window.innerHeight
                ) {

                    heroImage.style.transform =
                        `scale(1.05) translateY(${scrolled * 0.3}px)`;

                }

            },
            { passive: true }
        );

    }


    // =================================================
    // REVEAL ON SCROLL
    // =================================================

    const revealEls =
        document.querySelectorAll(
            '.reveal'
        );

    if (
        revealEls.length > 0 &&
        'IntersectionObserver' in window
    ) {

        const observer =
            new IntersectionObserver(
                (entries) => {

                    entries.forEach(
                        (entry) => {

                            if (
                                entry.isIntersecting
                            ) {

                                entry.target.classList.add(
                                    'visible'
                                );

                                observer.unobserve(
                                    entry.target
                                );

                            }

                        }
                    );

                },
                {
                    threshold: 0.12,

                    rootMargin:
                        '0px 0px -50px 0px'
                }
            );


        revealEls.forEach(
            (el) => {

                observer.observe(el);

            }
        );

    }


    // =================================================
    // PRODUCT GALLERY
    // =================================================

    const galleryMain =
        document.getElementById(
            'galleryMain'
        );

    const galleryThumbs =
        document.querySelectorAll(
            '.product-gallery__thumb'
        );


    if (
        galleryMain &&
        galleryThumbs.length > 0
    ) {

        galleryThumbs.forEach(
            (thumb) => {

                thumb.addEventListener(
                    'click',
                    () => {

                        const image =
                            thumb.querySelector(
                                'img'
                            );

                        if (!image) {
                            return;
                        }


                        const imgSrc =
                            image.dataset.full ||
                            image.src;


                        galleryMain.src =
                            imgSrc;


                        galleryThumbs.forEach(
                            (t) => {

                                t.classList.remove(
                                    'active'
                                );

                            }
                        );


                        thumb.classList.add(
                            'active'
                        );

                    }
                );

            }
        );

    }


    // =================================================
    // PRODUCT OPTIONS
    // =================================================

    document
        .querySelectorAll(
            '.product-options__choices'
        )
        .forEach(
            (group) => {

                group.addEventListener(
                    'click',
                    (event) => {

                        const target =
                            event.target.closest(
                                '.product-options__choice'
                            );

                        if (!target) {
                            return;
                        }


                        group
                            .querySelectorAll(
                                '.product-options__choice'
                            )
                            .forEach(
                                (choice) => {

                                    choice.classList.remove(
                                        'selected'
                                    );

                                }
                            );


                        target.classList.add(
                            'selected'
                        );

                    }
                );

            }
        );


    // =================================================
    // QUANTITY SELECTOR
    // =================================================

    document
        .querySelectorAll(
            '.qty-selector, .cart-qty'
        )
        .forEach(
            (qty) => {

                const input =
                    qty.querySelector(
                        'input'
                    );

                const dec =
                    qty.querySelector(
                        'button[data-action="decrease"]'
                    );

                const inc =
                    qty.querySelector(
                        'button[data-action="increase"]'
                    );


                if (!input) {
                    return;
                }


                // -----------------------------------------
                // DIMINUER
                // -----------------------------------------

                if (dec) {

                    dec.addEventListener(
                        'click',
                        () => {

                            const current =
                                parseInt(
                                    input.value
                                ) || 1;


                            const value =
                                Math.max(
                                    1,
                                    current - 1
                                );


                            input.value =
                                value;


                            if (
                                input.dataset.cartKey
                            ) {

                                updateCartForm(
                                    input
                                );

                            }

                        }
                    );

                }


                // -----------------------------------------
                // AUGMENTER
                // -----------------------------------------

                if (inc) {

                    inc.addEventListener(
                        'click',
                        () => {

                            const current =
                                parseInt(
                                    input.value
                                ) || 1;


                            const value =
                                current + 1;


                            input.value =
                                value;


                            if (
                                input.dataset.cartKey
                            ) {

                                updateCartForm(
                                    input
                                );

                            }

                        }
                    );

                }

            }
        );


    // =================================================
    // UPDATE CART
    // =================================================

    function updateCartForm(input) {

        const form =
            input.closest('form');

        if (form) {

            form.submit();

        }

    }


    // =================================================
    // DELIVERY TYPE
    // =================================================

    const deliveryRadios =
        document.querySelectorAll(
            'input[name="delivery_type"]'
        );

    const deliveryFields =
        document.querySelectorAll(
            '.delivery-fields'
        );


    function toggleDelivery() {

        deliveryRadios.forEach(
            (radio) => {

                if (radio.checked) {

                    deliveryFields.forEach(
                        (field) => {

                            field.classList.toggle(
                                'active',
                                field.dataset.type ===
                                radio.value
                            );

                        }
                    );

                }

            }
        );

    }


    if (
        deliveryRadios.length > 0
    ) {

        deliveryRadios.forEach(
            (radio) => {

                radio.addEventListener(
                    'change',
                    toggleDelivery
                );

            }
        );


        toggleDelivery();

    }


    // =================================================
    // DELIVERY OPTION
    // =================================================

    document
        .querySelectorAll(
            '.delivery-option'
        )
        .forEach(
            (option) => {

                option.addEventListener(
                    'click',
                    () => {

                        const radio =
                            option.querySelector(
                                'input[type="radio"]'
                            );


                        if (radio) {

                            radio.checked =
                                true;

                            toggleDelivery();

                        }


                        document
                            .querySelectorAll(
                                '.delivery-option'
                            )
                            .forEach(
                                (item) => {

                                    item.classList.remove(
                                        'selected'
                                    );

                                }
                            );


                        option.classList.add(
                            'selected'
                        );

                    }
                );

            }
        );


    // =================================================
    // PRODUCT TABS
    // =================================================

    const tabButtons =
        document.querySelectorAll(
            '.product-tabs__nav button'
        );

    const tabPanels =
        document.querySelectorAll(
            '.product-tabs__panel'
        );


    tabButtons.forEach(
        (button) => {

            button.addEventListener(
                'click',
                () => {

                    const target =
                        button.dataset.tab;


                    tabButtons.forEach(
                        (btn) => {

                            btn.classList.remove(
                                'active'
                            );

                        }
                    );


                    tabPanels.forEach(
                        (panel) => {

                            panel.classList.remove(
                                'active'
                            );

                        }
                    );


                    button.classList.add(
                        'active'
                    );


                    const panel =
                        document.getElementById(
                            'tab-' + target
                        );


                    if (panel) {

                        panel.classList.add(
                            'active'
                        );

                    }

                }
            );

        }
    );


    // =================================================
    // MOBILE FILTER
    // =================================================

    const filterToggle =
        document.getElementById(
            'filterToggle'
        );

    const shopFilters =
        document.getElementById(
            'shopFilters'
        );


    if (
        filterToggle &&
        shopFilters
    ) {

        filterToggle.addEventListener(
            'click',
            () => {

                shopFilters.classList.toggle(
                    'open'
                );

            }
        );

    }


});