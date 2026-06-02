const yearElement = document.getElementById("year");
const menuToggle = document.getElementById("menuToggle");
const topNav = document.getElementById("topNav");
const navLinks = document.querySelectorAll(".nav-link");
const contactForm = document.getElementById("contactForm");
const formStatus = document.getElementById("formStatus");
const siteHeader = document.getElementById("siteHeader");
const backToTop = document.getElementById("backToTop");
const themeToggle = document.getElementById("themeToggle");
const projectFilter = document.getElementById("projectFilter");
const projectCards = document.querySelectorAll(".project-card");
const galleryItems = document.querySelectorAll(".gallery-item");
const galleryPreview = document.getElementById("galleryPreview");
const faqItems = document.querySelectorAll(".faq-item");

const THEME_KEY = "wplab-theme";

if (yearElement) {
  yearElement.textContent = String(new Date().getFullYear());
}

function setActiveNavLink() {
  const scrollPos = window.scrollY + 120;
  let currentId = "home";

  document.querySelectorAll("main section[id]").forEach((section) => {
    if (scrollPos >= section.offsetTop) {
      currentId = section.id;
    }
  });

  navLinks.forEach((link) => {
    const href = link.getAttribute("href");
    link.classList.toggle("active", href === `#${currentId}`);
  });
}

function applyTheme(theme) {
  const isDark = theme === "dark";
  document.documentElement.setAttribute("data-theme", isDark ? "dark" : "light");
  if (themeToggle) {
    themeToggle.textContent = isDark ? "Light mode" : "Dark mode";
  }
  localStorage.setItem(THEME_KEY, theme);
}

function initTheme() {
  const saved = localStorage.getItem(THEME_KEY);
  const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
  applyTheme(saved || (prefersDark ? "dark" : "light"));
}

if (themeToggle) {
  themeToggle.addEventListener("click", () => {
    const current = document.documentElement.getAttribute("data-theme");
    applyTheme(current === "dark" ? "light" : "dark");
  });
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
    const offset = siteHeader ? siteHeader.offsetHeight + 12 : 0;
    const top = target.getBoundingClientRect().top + window.scrollY - offset;
    window.scrollTo({ top, behavior: "smooth" });
    topNav.classList.remove("open");
  });
});

window.addEventListener("scroll", () => {
  setActiveNavLink();
  if (backToTop) {
    backToTop.hidden = window.scrollY < 320;
  }
});

if (backToTop) {
  backToTop.addEventListener("click", () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
  });
}

if (projectFilter) {
  projectFilter.addEventListener("change", () => {
    const value = projectFilter.value;
    projectCards.forEach((card) => {
      const category = card.getAttribute("data-category");
      const show = value === "all" || category === value;
      card.classList.toggle("is-hidden", !show);
    });
  });
}

galleryItems.forEach((item) => {
  item.addEventListener("click", () => {
    if (!galleryPreview) return;
    const text = item.getAttribute("data-full");
    galleryPreview.textContent = text || "Preview unavailable.";
  });
});

faqItems.forEach((item) => {
  item.addEventListener("toggle", () => {
    if (!item.open) return;
    faqItems.forEach((other) => {
      if (other !== item && other.open) {
        other.open = false;
      }
    });
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

    if (!String(email.value).includes("@")) {
      formStatus.textContent = "Please enter a valid email address.";
      formStatus.className = "form-status error";
      return;
    }

    formStatus.textContent = "Message submitted successfully (demo only).";
    formStatus.className = "form-status success";
    contactForm.reset();
  });
}

initTheme();
setActiveNavLink();
