document.addEventListener("DOMContentLoaded", () => {
  console.log("Website Loaded - BDS Energy & Marine Services");

  // Contact form handler
  const form = document.querySelector(".contact-form");
  if (form) {
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      alert("Thank you for contacting us. We will get back to you shortly.");
      form.reset();
    });
  }

  // Mobile nav toggle
  const hamburger = document.querySelector(".hamburger");
  const navLinks = document.querySelector(".nav-links");
  if (hamburger && navLinks) {
    hamburger.addEventListener("click", () => {
      navLinks.classList.toggle("active");
    });
  }

  // Slideshow (handles both hero + generic slideshows)
  const slides = document.querySelectorAll(".slide, .slideshow img");
  let index = 0;

  if (slides.length > 0) {
    slides[0].classList.add("active"); // show first slide immediately
    setInterval(() => {
      slides[index].classList.remove("active");
      index = (index + 1) % slides.length;
      slides[index].classList.add("active");
    }, 5000); // adjust timing as needed
  }
});
document.addEventListener("DOMContentLoaded", () => {
  const hamburger = document.querySelector(".hamburger");
  const navLinks = document.querySelector(".nav-links");

  hamburger.addEventListener("click", () => {
    navLinks.classList.toggle("active");
  });
});
