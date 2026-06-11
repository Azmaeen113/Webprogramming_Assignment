<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/functions.php';

$approved_members = get_approved_members();
?>
<?php include 'includes/header.php'; ?>
<body>
<?php include 'includes/navbar.php'; ?>

  <main id="mainContent">
    <section id="home" class="hero">
      <div class="container hero-grid">
        <div class="hero-content">
          <span class="eyebrow">Est. 2024 · KUET Campus</span>
          <h2>Build skills. Earn income. Lead your future.</h2>
          <p>
            The official freelancing and digital skills club of
            <strong>Khulna University of Engineering & Technology</strong>.
            We help students learn marketable skills and start earning online.
          </p>
          <div class="hero-stats">
            <?php foreach ($stats as $stat): ?>
            <div class="stat">
              <span class="stat-num" data-target="<?php echo htmlspecialchars($stat['value']); ?>">0</span><?php echo htmlspecialchars($stat['suffix']); ?>
              <span class="stat-label"><?php echo htmlspecialchars($stat['label']); ?></span>
            </div>
            <?php endforeach; ?>
          </div>
          <div class="hero-actions">
            <a class="btn btn-primary" href="#join">Join the Club</a>
            <a class="btn btn-secondary" href="#about">Learn More</a>
          </div>
          <p class="hero-platforms">
            <span>Platforms we train for:</span>
            <strong>Fiverr</strong>
            <strong>Upwork</strong>
            <strong>LinkedIn</strong>
          </p>
        </div>
        <figure class="hero-media">
          <img src="image.png" alt="KUET campus landmark at Khulna University of Engineering and Technology" width="640" height="480" loading="eager">
          <figcaption>Khulna University of Engineering &amp; Technology</figcaption>
        </figure>
      </div>
    </section>

    <section id="about" class="section">
      <div class="container">
        <div class="section-header">
          <span class="section-tag">About Us</span>
          <h2 class="section-title">Who We Are</h2>
          <p class="section-lead">
            freelanceHUB-KUET is a student-led club dedicated to empowering engineering students
            with freelancing skills, marketplace knowledge, and a strong professional network.
          </p>
        </div>
        <div class="about-grid">
          <ul class="about-list">
            <li>Weekly skill-building workshops on campus</li>
            <li>Peer mentoring and buddy system for new members</li>
            <li>Real client-style project simulations</li>
            <li>Portfolio building sessions for Fiverr &amp; Upwork</li>
          </ul>
          <div class="about-highlight">
            <p class="about-quote">
              &ldquo;From campus to clients — we bridge the gap between classroom learning and real-world earning.&rdquo;
            </p>
            <p class="about-meta">Student-led · Campus-based · Career-focused</p>
          </div>
        </div>
      </div>
    </section>

    <section id="services" class="section section-alt">
      <div class="container">
        <div class="section-header">
          <span class="section-tag">Programs</span>
          <h2 class="section-title">What We Do</h2>
          <p class="section-lead">Six core tracks designed for KUET students entering the freelance world.</p>
        </div>
        <div class="feature-grid" id="serviceGrid">
          <?php foreach ($skill_tracks as $track): ?>
          <article class="card service-card" data-category="<?php echo htmlspecialchars($track['category']); ?>">
            <span class="card-tag"><?php echo htmlspecialchars($track['badge']); ?></span>
            <h3><?php echo htmlspecialchars($track['name']); ?></h3>
            <p><?php echo htmlspecialchars($track['description']); ?></p>
          </article>
          <?php endforeach; ?>
        </div>
        <div class="filter-bar">
          <label for="serviceFilter">Filter skill track</label>
          <select id="serviceFilter">
            <option value="all">All Tracks</option>
            <?php foreach ($skill_tracks as $track): ?>
            <option value="<?php echo htmlspecialchars($track['category']); ?>"><?php echo htmlspecialchars($track['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </section>

    <section id="events" class="section">
      <div class="container">
        <div class="section-header">
          <span class="section-tag">Calendar</span>
          <h2 class="section-title">Upcoming Events</h2>
          <p class="section-lead">Workshops, bootcamps, and talks happening at KUET this semester.</p>
        </div>
        <div class="events-list">
          <?php foreach ($events as $event): ?>
          <article class="event-card">
            <div class="event-date"><span><?php echo htmlspecialchars($event['date_day']); ?></span><small><?php echo htmlspecialchars($event['date_month']); ?></small></div>
            <div class="event-body">
              <span class="event-type"><?php echo htmlspecialchars($event['type']); ?></span>
              <h3><?php echo htmlspecialchars($event['title']); ?></h3>
              <p><?php echo htmlspecialchars($event['location']); ?> · <?php echo htmlspecialchars($event['time']); ?></p>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section id="team" class="section section-alt">
      <div class="container">
        <div class="section-header">
          <span class="section-tag">Leadership</span>
          <h2 class="section-title">Meet Our Core Team</h2>
          <p class="section-lead">Students leading students — that's the freelanceHUB way.</p>
        </div>
        <div class="team-grid">
          <?php foreach ($team as $member): ?>
          <article class="team-card" style="--member-accent: <?php echo htmlspecialchars($member['accent_color']); ?>">
            <img class="team-avatar" src="<?php echo htmlspecialchars($member['photo']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>" width="80" height="80" loading="lazy">
            <h3><?php echo htmlspecialchars($member['name']); ?></h3>
            <p class="team-role"><?php echo htmlspecialchars($member['role']); ?></p>
            <p><?php echo htmlspecialchars($member['details']); ?></p>
          </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section id="members" class="section">
      <div class="container">
        <div class="section-header">
          <span class="section-tag">Community</span>
          <h2 class="section-title">Our Members</h2>
          <p class="section-lead">KUET students building freelancing careers with freelanceHUB.</p>
        </div>
        <?php if (count($approved_members) > 0): ?>
        <div class="team-grid members-grid">
          <?php foreach ($approved_members as $member): ?>
          <article class="team-card member-card">
            <img class="team-avatar" src="<?php echo htmlspecialchars($member['photo']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>" width="80" height="80" loading="lazy">
            <h3><?php echo htmlspecialchars($member['name']); ?></h3>
            <p class="team-role"><?php echo htmlspecialchars($member['designation'] ?? 'Member'); ?></p>
            <p><?php echo htmlspecialchars($member['department']); ?> · <?php echo htmlspecialchars($member['track']); ?></p>
            <?php if (!empty($member['message'])): ?>
            <p class="member-bio"><?php echo htmlspecialchars($member['message']); ?></p>
            <?php endif; ?>
          </article>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="members-empty">Approved members will appear here after applications are reviewed by the club team.</p>
        <?php endif; ?>
      </div>
    </section>

    <section id="faq" class="section">
      <div class="container container-narrow">
        <div class="section-header">
          <span class="section-tag">Support</span>
          <h2 class="section-title">Frequently Asked Questions</h2>
        </div>
        <div class="faq-list">
          <?php foreach ($faq as $item): ?>
          <details class="faq-item">
            <summary><?php echo htmlspecialchars($item['question']); ?></summary>
            <p><?php echo htmlspecialchars($item['answer']); ?></p>
          </details>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section id="join" class="section section-join">
      <div class="container">
        <div class="join-grid">
          <div class="join-intro">
            <span class="section-tag">Membership</span>
            <h2 class="section-title">Join freelanceHUB Today</h2>
            <p class="section-lead">Fill out the form and our team will contact you within 48 hours with onboarding details.</p>
            <ul class="join-perks">
              <li>Free membership for all KUET students</li>
              <li>Hands-on workshops every week</li>
              <li>Mentorship from experienced freelancers</li>
            </ul>
          </div>
          <form class="contact-form" id="contactForm" method="post" action="form-handler.php" enctype="multipart/form-data">
            <?php if (isset($_GET['submitted']) && $_GET['submitted'] === '1'): ?>
            <p class="form-status success" role="status">
              Your application has been submitted. We will contact you within 48 hours.
            </p>
            <?php endif; ?>
            <?php if (isset($_GET['error']) && $_GET['error'] === 'photo'): ?>
            <p class="form-status error" role="alert">
              Please upload a valid profile photo (JPG, PNG, or WebP, max 2MB).
            </p>
            <?php elseif (isset($_GET['error'])): ?>
            <p class="form-status error" role="alert">
              Please complete all required fields with valid information.
            </p>
            <?php endif; ?>
            <div class="form-row">
              <div class="form-group">
                <label for="name">Full Name</label>
                <input id="name" name="name" type="text" placeholder="e.g. Rahim Uddin" required>
              </div>
              <div class="form-group">
                <label for="studentId">Student ID</label>
                <input id="studentId" name="student_id" type="text" placeholder="e.g. 2103041" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="dept">Department</label>
                <select id="dept" name="department" required>
                  <option value="">Select Department</option>
                  <option>Computer Science & Engineering</option>
                  <option>Electrical & Electronic Engineering</option>
                  <option>Mechanical Engineering</option>
                  <option>Civil Engineering</option>
                  <option>Other</option>
                </select>
              </div>
              <div class="form-group">
                <label for="track">Preferred Skill Track</label>
                <select id="track" name="track" required>
                  <option value="">Choose a Track</option>
                  <?php foreach ($skill_tracks as $skill): ?>
                  <option><?php echo htmlspecialchars($skill['name']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input id="email" name="email" type="email" placeholder="you@student.kuet.ac.bd" required>
            </div>
            <div class="form-group">
              <label for="photo">Profile Photo</label>
              <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" required>
            </div>
            <div class="form-group">
              <label for="message">Why do you want to join? <span class="optional">(optional)</span></label>
              <textarea id="message" name="message" rows="3" placeholder="Tell us about your goals..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Submit Application</button>
            <p class="form-status" id="formStatus" role="status" aria-live="polite"></p>
          </form>
        </div>
      </div>
    </section>
  </main>

<?php include 'includes/footer.php'; ?>
