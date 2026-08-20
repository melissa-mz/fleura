</main>

<!-- ===================================================== -->
<!-- FOOTER — Encadré, arrondi, avec marge                 -->
<!-- ===================================================== -->
<footer class="footer">
    <div class="footer__inner">
        <div class="footer__col footer__col--brand">
            <h3 class="footer__logo">FLEURA</h3>
            <p class="footer__tagline">L'élégance au quotidien.</p>
        </div>

        <div class="footer__col">
            <h4>Navigation</h4>
            <ul>
                <li><a href="<?= e(SITE_URL) ?>/index.php">Accueil</a></li>
                <li><a href="<?= e(SITE_URL) ?>/shop.php">Collection</a></li>
                <li><a href="<?= e(SITE_URL) ?>/shop.php?filter=new">Nouveautés</a></li>
                <li><a href="<?= e(SITE_URL) ?>/about.php">À propos</a></li>
                <li><a href="<?= e(SITE_URL) ?>/contact.php">Contact</a></li>
            </ul>
        </div>

        <div class="footer__col">
            <h4>Catégories</h4>
            <ul>
                <li><a href="<?= e(SITE_URL) ?>/shop.php?category=1">Robes</a></li>
                <li><a href="<?= e(SITE_URL) ?>/shop.php?category=2">Vêtements</a></li>
                <li><a href="<?= e(SITE_URL) ?>/shop.php?category=3">Sacs</a></li>
                <li><a href="<?= e(SITE_URL) ?>/shop.php?category=4">Foulards</a></li>
                <li><a href="<?= e(SITE_URL) ?>/shop.php?category=6">Accessoires</a></li>
            </ul>
        </div>

        <div class="footer__col">
            <h4>Contact</h4>
            <p class="footer__info" style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:1.2rem;">📞</span>
                <a href="tel:+213670063731" style="color:rgba(255,255,255,0.8); text-decoration:none; transition:color 0.2s;">
                    0670 06 37 31
                </a>
            </p>
            <p class="footer__info">Koléa, Algérie</p>
            <div class="footer__social">
                <!-- Facebook -->
                <a href="https://www.facebook.com/Boutiquefleura/" target="_blank" rel="noopener" aria-label="Facebook" class="footer__social-link footer__social-link--facebook">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12c0-5.5-4.5-10-10-10S2 6.5 2 12c0 5 3.7 9.1 8.4 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.3v7C18.3 21.1 22 17 22 12Z"/></svg>
                </a>
                <!-- Instagram -->
                <a href="https://www.instagram.com/Boutiquefleura" target="_blank" rel="noopener" aria-label="Instagram" class="footer__social-link footer__social-link--instagram">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg>
                </a>
            </div>
        </div>
    </div>

    <div class="footer__bottom">
        <p>&copy; 2026 FLEURA — Tous droits réservés.</p>
    </div>
</footer>

</div> <!-- fin .page-wrapper -->

<script>
// ============================================================
// MENU MOBILE BURGER
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    const burger = document.getElementById('burger');
    const navMenu = document.querySelector('.navbar__menu');

    if (burger && navMenu) {
        burger.addEventListener('click', function() {
            burger.classList.toggle('active');
            navMenu.classList.toggle('is-open');
        });
    }

    // ============================================================
    // DROPDOWN ARTICLES (ouverture au clic sur mobile)
    // ============================================================
    const dropdownTrigger = document.querySelector('.navbar__dropdown-trigger');
    const dropdown = document.querySelector('.navbar__dropdown');
    
    if (dropdownTrigger && dropdown) {
        dropdownTrigger.addEventListener('click', function(e) {
            e.preventDefault(); // empêche le comportement par défaut du bouton
            dropdown.classList.toggle('open');
        });
    }
});
</script>

<script src="<?= e(SITE_URL) ?>/assets/js/main.js"></script>
</body>
</html>