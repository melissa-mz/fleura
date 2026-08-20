</div>
    </main>
</div>

<script>
// Menu mobile burger
const navBurger = document.getElementById('navBurger');
const navMenu = document.querySelector('.navbar__menu');
if (navBurger && navMenu) {
    navBurger.addEventListener('click', () => {
        navBurger.classList.toggle('active');
        navMenu.classList.toggle('open');
    });
}
</script>

</body>
</html>