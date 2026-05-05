const yearElement = document.getElementById("year");
const menuToggle = document.getElementById("menuToggle");
const topNav = document.querySelector(".top-nav");
const navLinks = document.querySelectorAll('.top-nav a[href^="#"]');
const contactForm = document.getElementById("contactForm");
const formStatus = document.getElementById("formStatus");

if (yearElement) {
  yearElement.textContent = String(new Date().getFullYear());
}

if (menuToggle && topNav) {
  menuToggle.addEventListener("click", () => {
    topNav.classList.toggle("open");
  });
}

navLinks.forEach((link) => {
  link.addEventListener("click", (event) => {
    const targetId = link.getAttribute("href");
    const target = targetId ? document.querySelector(targetId) : null;
    if (!target) return;

    event.preventDefault();
    target.scrollIntoView({ behavior: "smooth", block: "start" });
    if (topNav) topNav.classList.remove("open");
  });
});

if (contactForm && formStatus) {
  contactForm.addEventListener("submit", (event) => {
    event.preventDefault();

    const name = document.getElementById("name");
    const email = document.getElementById("email");
    const message = document.getElementById("message");
    const hasValue = (el) => Boolean(el && String(el.value).trim());

    if (!hasValue(name) || !hasValue(email) || !hasValue(message)) {
      formStatus.textContent = "Please fill in all fields before submitting.";
      formStatus.className = "form-status error";
      return;
    }

    formStatus.textContent = "Message submitted successfully (demo only).";
    formStatus.className = "form-status success";
    contactForm.reset();
  });
}
