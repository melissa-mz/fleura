// =====================================================
// FLEURA — JavaScript principal
// =====================================================

document.addEventListener('DOMContentLoaded', () => {

    // ---------- Navbar scroll effect ----------
    const navbar = document.getElementById('navbar');
    if (navbar) {
        const onScroll = () => {
            if (window.scrollY > 30) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    // ---------- Hero parallax ----------
    const heroImage = document.querySelector('.hero__image');
    if (heroImage) {
        window.addEventListener('scroll', () => {
            const scrolled = window.scrollY;
            if (scrolled < window.innerHeight) {
                heroImage.style.transform = `scale(1.05) translateY(${scrolled * 0.3}px)`;
            }
        }, { passive: true });
    }

    // ---------- Reveal on scroll ----------
    const revealEls = document.querySelectorAll('.reveal');
    if (revealEls.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -50px 0px' });
        revealEls.forEach(el => observer.observe(el));
    }

    // ---------- Product gallery (product page) ----------
    const galleryMain = document.getElementById('galleryMain');
    const galleryThumbs = document.querySelectorAll('.product-gallery__thumb');
    if (galleryMain && galleryThumbs.length > 0) {
        galleryThumbs.forEach(thumb => {
            thumb.addEventListener('click', () => {
                const imgSrc = thumb.querySelector('img').dataset.full || thumb.querySelector('img').src;
                galleryMain.src = imgSrc;
                galleryThumbs.forEach(t => t.classList.remove('active'));
                thumb.classList.add('active');
            });
        });
    }

    // ---------- Product options (size/color selection) ----------
    document.querySelectorAll('.product-options__choices').forEach(group => {
        group.addEventListener('click', (e) => {
            const target = e.target.closest('.product-options__choice');
            if (!target) return;
            group.querySelectorAll('.product-options__choice').forEach(c => c.classList.remove('selected'));
            target.classList.add('selected');
        });
    });

    // ---------- Quantity selector ----------
    document.querySelectorAll('.qty-selector, .cart-qty').forEach(qty => {
        const input = qty.querySelector('input');
        const dec = qty.querySelector('button[data-action="decrease"]');
        const inc = qty.querySelector('button[data-action="increase"]');
        if (dec) dec.addEventListener('click', () => {
            const v = Math.max(1, parseInt(input.value) - 1);
            input.value = v;
            if (input.dataset.cartKey) updateCartForm(input);
        });
        if (inc) inc.addEventListener('click', () => {
            const v = parseInt(input.value) + 1;
            input.value = v;
            if (input.dataset.cartKey) updateCartForm(input);
        });
    });

    function updateCartForm(input) {
        const form = input.closest('form');
        if (form) form.submit();
    }

    // ---------- Delivery type toggle (checkout) ----------
    const deliveryRadios = document.querySelectorAll('input[name="delivery_type"]');
    const deliveryFields = document.querySelectorAll('.delivery-fields');
    function toggleDelivery() {
        deliveryRadios.forEach(radio => {
            if (radio.checked) {
                deliveryFields.forEach(df => {
                    df.classList.toggle('active', df.dataset.type === radio.value);
                });
            }
        });
    }
    if (deliveryRadios.length > 0) {
        deliveryRadios.forEach(r => r.addEventListener('change', toggleDelivery));
        toggleDelivery();
    }

    // ---------- Delivery option visual selection ----------
    document.querySelectorAll('.delivery-option').forEach(opt => {
        opt.addEventListener('click', () => {
            const radio = opt.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                toggleDelivery();
            }
            document.querySelectorAll('.delivery-option').forEach(o => o.classList.remove('selected'));
            opt.classList.add('selected');
        });
    });

    // ---------- Product tabs ----------
    const tabButtons = document.querySelectorAll('.product-tabs__nav button');
    const tabPanels = document.querySelectorAll('.product-tabs__panel');
    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.dataset.tab;
            tabButtons.forEach(b => b.classList.remove('active'));
            tabPanels.forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            const panel = document.getElementById('tab-' + target);
            if (panel) panel.classList.add('active');
        });
    });

    // ---------- Mobile filter toggle ----------
    const filterToggle = document.getElementById('filterToggle');
    const shopFilters = document.getElementById('shopFilters');
    if (filterToggle && shopFilters) {
        filterToggle.addEventListener('click', () => {
            shopFilters.classList.toggle('open');
        });
    }

   

});