// Function to add 'visible' class when a section enters the viewport
const sections = document.querySelectorAll("section");

const revealOnScroll = () => {
  const triggerBottom = window.innerHeight * 0.85; // When the section top is 85% of the window height
  sections.forEach(section => {
    const sectionTop = section.getBoundingClientRect().top;
    if (sectionTop < triggerBottom) {
      section.classList.add("visible");
    }
  });
};

window.addEventListener("scroll", revealOnScroll);
window.addEventListener("load", revealOnScroll);

// JavaScript for toggling the navbar
const menuIcon = document.getElementById("menu-icon");
const navbar = document.querySelector(".navbar");

menuIcon.addEventListener("click", () => {
  navbar.classList.toggle("active"); // Toggle the active class to show/hide the menu
});

// Replace with Typed.js for typing animation (ensure library is included)
if (typeof Typed !== 'undefined') {
  const typed = new Typed('.typed-name', {
    strings: ['Ms. ASHWITHA C THOMAS'],
    typeSpeed: 100,
    backSpeed: 50,
    backDelay: 2000,
    loop: true
  });
} else {
  console.error('Typed.js library is not loaded. Please include <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script> in your HTML.');
}

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if (target) {
      window.scrollTo({
        top: target.offsetTop - 80, // Adjust based on your header height (e.g., 80px)
        behavior: 'smooth'
      });
      // Close mobile menu if open
      navbar.classList.remove('active');
    }
  });
});