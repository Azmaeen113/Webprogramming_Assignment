const yearElement = document.getElementById("year");
const menuToggle = document.getElementById("menuToggle");
const topNav = document.getElementById("topNav");
const navLinks = document.querySelectorAll(".nav-link");
const contactForm = document.getElementById("contactForm");
const formStatus = document.getElementById("formStatus");
const siteHeader = document.getElementById("siteHeader");
const backToTop = document.getElementById("backToTop");
const serviceFilter = document.getElementById("serviceFilter");
const serviceCards = document.querySelectorAll(".service-card");
const faqItems = document.querySelectorAll(".faq-item");
const statNumbers = document.querySelectorAll(".stat-num");

const FILTER_KEY = "freelancehub-service-filter";

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

function animateCounters() {
  statNumbers.forEach((counter) => {
    const target = parseInt(counter.getAttribute("data-target"), 10);
    if (!target) return;

    let current = 0;
    const step = target / 60;

    const timer = setInterval(() => {
      current += step;
      if (current >= target) {
        counter.textContent = String(target);
        clearInterval(timer);
      } else {
        counter.textContent = String(Math.floor(current));
      }
    }, 25);
  });
}

if (menuToggle && topNav) {
  menuToggle.addEventListener("click", () => {
    const isOpen = topNav.classList.toggle("open");
    menuToggle.setAttribute("aria-expanded", String(isOpen));
  });
}

navLinks.forEach((link) => {
  link.addEventListener("click", (event) => {
    const targetId = link.getAttribute("href");
    const target = targetId ? document.querySelector(targetId) : null;
    if (!target) return;

    event.preventDefault();
    target.classList.add("is-visible");
    const offset = siteHeader ? siteHeader.offsetHeight + 12 : 0;
    const top = target.getBoundingClientRect().top + window.scrollY - offset;
    window.scrollTo({ top, behavior: "smooth" });
    topNav.classList.remove("open");
    if (menuToggle) {
      menuToggle.setAttribute("aria-expanded", "false");
    }
  });
});

function updateScrollState() {
  setActiveNavLink();
  if (siteHeader) {
    siteHeader.classList.toggle("is-scrolled", window.scrollY > 8);
  }
  if (backToTop) {
    backToTop.hidden = window.scrollY < 320;
  }
}

window.addEventListener("scroll", updateScrollState);

if (backToTop) {
  backToTop.addEventListener("click", () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
  });
}

if (serviceFilter) {
  const savedFilter = localStorage.getItem(FILTER_KEY);
  if (savedFilter) {
    serviceFilter.value = savedFilter;
  }

  const applyFilter = (value) => {
    serviceCards.forEach((card) => {
      const category = card.getAttribute("data-category");
      const show = value === "all" || category === value;
      card.classList.toggle("is-hidden", !show);
    });
  };

  applyFilter(serviceFilter.value);

  serviceFilter.addEventListener("change", () => {
    const value = serviceFilter.value;
    applyFilter(value);
    localStorage.setItem(FILTER_KEY, value);
  });
}

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

const heroStats = document.querySelector(".hero-stats");
if (heroStats && statNumbers.length) {
  const counterObserver = new IntersectionObserver(
    (entries) => {
      if (entries[0].isIntersecting) {
        animateCounters();
        counterObserver.disconnect();
      }
    },
    { threshold: 0.4 }
  );
  counterObserver.observe(heroStats);
}

const revealSections = document.querySelectorAll("main section");

function markVisibleSections(sections) {
  sections.forEach((section) => {
    const rect = section.getBoundingClientRect();
    if (rect.top < window.innerHeight * 0.92 && rect.bottom > 0) {
      section.classList.add("is-visible");
    }
  });
}

if (revealSections.length && !window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
  document.documentElement.classList.add("js-reveal");

  revealSections.forEach((section) => {
    section.classList.add("reveal-section");
  });

  markVisibleSections(revealSections);

  if ("IntersectionObserver" in window) {
    const revealObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            revealObserver.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.08, rootMargin: "0px 0px -5% 0px" }
    );

    revealSections.forEach((section) => {
      if (!section.classList.contains("is-visible")) {
        revealObserver.observe(section);
      }
    });
  } else {
    revealSections.forEach((section) => section.classList.add("is-visible"));
  }

  window.addEventListener("scroll", () => markVisibleSections(revealSections), { passive: true });
}

updateScrollState();
