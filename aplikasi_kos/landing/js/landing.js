// ===============================
// AOS
// ===============================

AOS.init({
    duration: 800,
    once: true,
    offset: 100
});


// ===============================
// SWIPER
// ===============================

new Swiper(".mySwiper", {
    loop: true,

    autoplay: {
        delay: 3000,
        disableOnInteraction: false,
    },

    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },

    breakpoints: {

        320: {
            slidesPerView: 1,
            spaceBetween: 20
        },

        768: {
            slidesPerView: 2,
            spaceBetween: 30
        },

        1200: {
            slidesPerView: 3,
            spaceBetween: 30
        }

    }
});


// ===============================
// NAVBAR SCROLL EFFECT
// ===============================

const navbar = document.querySelector('.navbar');

window.addEventListener('scroll', () => {

    if (window.scrollY > 50) {

        navbar.style.background = '#ffffff';
        navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.08)';

    } else {

        navbar.style.background = '#ffffff';
        navbar.style.boxShadow = 'none';

    }

});


// ===============================
// CLOSE MOBILE MENU
// ===============================

const navLinks = document.querySelectorAll('.nav-link');
const navbarCollapse = document.querySelector('.navbar-collapse');

navLinks.forEach(link => {
    link.addEventListener('click', () => {
        if (navbarCollapse.classList.contains('show')) {
            const collapseInstance = bootstrap.Collapse.getInstance(navbarCollapse) || new bootstrap.Collapse(navbarCollapse, {toggle: false});
            collapseInstance.hide();
        }
    });
});


// ===============================
// ACTIVE NAV LINK
// ===============================

const sections = document.querySelectorAll("section[id]");

window.addEventListener("scroll", () => {

    let current = "";

    sections.forEach(section => {

        const sectionTop = section.offsetTop - 120;
        const sectionHeight = section.clientHeight;

        if (window.scrollY >= sectionTop) {

            current = section.getAttribute("id");

        }

    });

    navLinks.forEach(link => {

        link.classList.remove("active");

        if (
            link.getAttribute("href") === "#" + current
        ) {
            link.classList.add("active");
        }

    });

});