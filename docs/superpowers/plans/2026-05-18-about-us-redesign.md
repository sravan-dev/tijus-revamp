# About Us Section Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Redesign the About Us content area to match `issues/d4.png` while keeping the existing banner.

**Architecture:** Surgical updates to the static template part and theme stylesheet.

**Tech Stack:** WordPress (PHP), CSS.

---

### Task 1: Update Static Template Structure

**Files:**
- Modify: `wp-content/themes/tijus-theme/template-parts/static-about.php`

- [ ] **Step 1: Update HTML structure**
  Adjust the section title area to ensure consistency with the design, focusing on the decorative bar and title text.

```php
<<<<
                            <div class="section-title text-center mb-5">
                                <span style="display: block; width: 60px; height: 3px; background: #2e2b70; margin: 0 auto 30px;"></span>
                                <h2 class="main-title">ABOUT TIJU'S ACADEMY - No: 1 OET / IELTS / CBT & PTE Coaching Centre in Kerala</h2>
                            </div>
====
                            <div class="section-title text-center mb-5 about-section-title">
                                <span class="title-bar"></span>
                                <h2 class="main-title">ABOUT TIJU'S ACADEMY - No: 1 OET / IELTS / CBT & PTE Coaching Centre in Kerala</h2>
                            </div>
>>>>
```

- [ ] **Step 2: Verify changes**
  Check the file content to ensure the new classes and structure are correct.

- [ ] **Step 3: Commit**
```bash
git add wp-content/themes/tijus-theme/template-parts/static-about.php
git commit -m "refactor: update about us template structure"
```

### Task 2: Apply Design Styles

**Files:**
- Modify: `wp-content/themes/tijus-theme/style.css`

- [ ] **Step 1: Add custom styles**
  Append styles to handle the decorative bar and uppercase title.

```css
/* About Section Redesign */
.about-section-title .title-bar {
    display: block;
    width: 60px;
    height: 3px;
    background: #2e2b70;
    margin: 0 auto 30px;
}

.about-section-title .main-title {
    text-transform: uppercase;
    font-weight: 700;
    line-height: 1.3;
}
```

- [ ] **Step 2: Verify styles**
  Ensure the styles are appended correctly to the end of `style.css`.

- [ ] **Step 3: Commit**
```bash
git add wp-content/themes/tijus-theme/style.css
git commit -m "style: add redesign styles for about us section"
```
