// =====================================================
// FLEURA — JavaScript principal
// =====================================================

document.addEventListener('DOMContentLoaded', () => {

    // =====================================================
    // NAVBAR
    // =====================================================

    const navbar = document.getElementById('navbar');
    const burger = document.getElementById('burger');
    const navMenu = document.getElementById('navMenu');
    const articlesDropdown = document.getElementById('articlesDropdown');


    // =====================================================
    // NAVBAR — EFFET AU SCROLL
    // =====================================================

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


    // =====================================================
    // MENU HAMBURGER MOBILE
    // =====================================================

    if (burger && navMenu) {

        burger.addEventListener('click', (event) => {

            event.preventDefault();
            event.stopPropagation();

            const isOpen =
                navMenu.classList.toggle('open');

            // Burger → X
            burger.classList.toggle(
                'active',
                isOpen
            );

            // Accessibilité
            burger.setAttribute(
                'aria-expanded',
                isOpen ? 'true' : 'false'
            );

            burger.setAttribute(
                'aria-label',
                isOpen
                    ? 'Fermer le menu'
                    : 'Ouvrir le menu'
            );

            // Bloquer le scroll derrière le menu
            document.body.classList.toggle(
                'menu-open',
                isOpen
            );

        });

    }


    // =====================================================
    // SOUS-MENU "ARTICLES"
    // =====================================================

    if (articlesDropdown) {

        const trigger =
            articlesDropdown.querySelector(
                '.navbar__dropdown-trigger'
            );

        if (trigger) {

            trigger.addEventListener(
                'click',
                (event) => {

                    event.preventDefault();
                    event.stopPropagation();

                    const isOpen =
                        articlesDropdown.classList.toggle(
                            'open'
                        );

                    // Accessibilité
                    trigger.setAttribute(
                        'aria-expanded',
                        isOpen ? 'true' : 'false'
                    );

                }
            );

        }

    }


    // =====================================================
    // FERMER LE MENU APRÈS CLIC SUR UN LIEN
    // =====================================================

    if (navMenu) {

        const links =
            navMenu.querySelectorAll('a');

        links.forEach((link) => {

            link.addEventListener(
                'click',
                () => {

                    // Fermer burger
                    if (burger) {

                        burger.classList.remove(
                            'active'
                        );

                        burger.setAttribute(
                            'aria-expanded',
                            'false'
                        );

                        burger.setAttribute(
                            'aria-label',
                            'Ouvrir le menu'
                        );

                    }

                    // Fermer menu
                    navMenu.classList.remove(
                        'open'
                    );

                    // Réactiver le scroll
                    document.body.classList.remove(
                        'menu-open'
                    );

                }
            );

        });

    }


    // =====================================================
    // FERMER AVEC LA TOUCHE ESC
    // =====================================================

    document.addEventListener(
        'keydown',
        (event) => {

            if (event.key !== 'Escape') {
                return;
            }

            // Fermer burger
            if (burger) {

                burger.classList.remove(
                    'active'
                );

                burger.setAttribute(
                    'aria-expanded',
                    'false'
                );

                burger.setAttribute(
                    'aria-label',
                    'Ouvrir le menu'
                );

            }

            // Fermer menu
            if (navMenu) {

                navMenu.classList.remove(
                    'open'
                );

            }

            // Fermer Articles
            if (articlesDropdown) {

                articlesDropdown.classList.remove(
                    'open'
                );

                const trigger =
                    articlesDropdown.querySelector(
                        '.navbar__dropdown-trigger'
                    );

                if (trigger) {

                    trigger.setAttribute(
                        'aria-expanded',
                        'false'
                    );

                }

            }

            // Réactiver scroll
            document.body.classList.remove(
                'menu-open'
            );

        }
    );


    // =====================================================
    // HERO — PARALLAX
    // =====================================================

    const heroImage =
        document.querySelector('.hero__image');

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


    // =====================================================
    // REVEAL ON SCROLL
    // =====================================================

    const revealEls =
        document.querySelectorAll('.reveal');

    if (revealEls.length > 0) {

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
            (el) => observer.observe(el)
        );

    }


    // =====================================================
    // PRODUCT GALLERY
    // =====================================================

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


    // =====================================================
    // PRODUCT OPTIONS
    // TAILLE / COULEUR
    // =====================================================

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


    // =====================================================
    // QUANTITY SELECTOR
    // =====================================================

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


                // -----------------------------------------
                // DIMINUER
                // -----------------------------------------

                if (dec && input) {

                    dec.addEventListener(
                        'click',
                        () => {

                            const currentValue =
                                parseInt(
                                    input.value,
                                    10
                                ) || 1;

                            const value =
                                Math.max(
                                    1,
                                    currentValue - 1
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

                if (inc && input) {

                    inc.addEventListener(
                        'click',
                        () => {

                            const currentValue =
                                parseInt(
                                    input.value,
                                    10
                                ) || 1;

                            const value =
                                currentValue + 1;

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


    // =====================================================
    // UPDATE CART FORM
    // =====================================================

    function updateCartForm(input) {

        const form =
            input.closest('form');

        if (form) {

            form.submit();

        }

    }


    // =====================================================
    // DELIVERY TYPE
    // CHECKOUT
    // =====================================================

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


    if (deliveryRadios.length > 0) {

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


    // =====================================================
    // DELIVERY OPTION VISUAL SELECTION
    // =====================================================

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


    // =====================================================
    // PRODUCT TABS
    // =====================================================

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

                    // Retirer active des boutons
                    tabButtons.forEach(
                        (btn) => {

                            btn.classList.remove(
                                'active'
                            );

                        }
                    );

                    // Masquer les panels
                    tabPanels.forEach(
                        (panel) => {

                            panel.classList.remove(
                                'active'
                            );

                        }
                    );

                    // Activer bouton
                    button.classList.add(
                        'active'
                    );

                    // Activer panel correspondant
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


    // =====================================================
    // MOBILE FILTER TOGGLE
    // =====================================================

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


    // =====================================================
    // FERMER LE MENU SI ON PASSE SUR DESKTOP
    // =====================================================

    window.addEventListener(
        'resize',
        () => {

            if (
                window.innerWidth > 900
            ) {

                if (burger) {

                    burger.classList.remove(
                        'active'
                    );

                    burger.setAttribute(
                        'aria-expanded',
                        'false'
                    );

                    burger.setAttribute(
                        'aria-label',
                        'Ouvrir le menu'
                    );

                }

                if (navMenu) {

                    navMenu.classList.remove(
                        'open'
                    );

                }

                if (articlesDropdown) {

                    articlesDropdown.classList.remove(
                        'open'
                    );

                    const trigger =
                        articlesDropdown.querySelector(
                            '.navbar__dropdown-trigger'
                        );

                    if (trigger) {

                        trigger.setAttribute(
                            'aria-expanded',
                            'false'
                        );

                    }

                }

                document.body.classList.remove(
                    'menu-open'
                );

            }

        }
    );


});