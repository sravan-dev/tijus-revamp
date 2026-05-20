# Design: About Us Section Redesign

Redesign the "About Us" page content to match the design provided in `issues/d4.png`, while keeping the existing page banner.

## Requirements

1. **Page Banner (Hero)**:
    - **Keep existing implementation unchanged.**
2. **Main Content Section**:
    - Decorative blue bar (`60px` x `3px`, color: `#2e2b70`) centered above the title.
    - Title: "ABOUT TIJU'S ACADEMY - No: 1 OET / IELTS / CBT & PTE Coaching Centre in Kerala", centered, bold, uppercase.
    - Paragraphs: Justified text as per current implementation and user preference.

## Implementation Details

### 1. Template Changes (`template-parts/static-about.php`)
- Ensure the decorative bar and main title have correct spacing and alignment.
- Update the main title to match the bold, uppercase style in the screenshot.

### 2. CSS Changes (`assets/css/custom-about.css` or append to `style.css`)
- Ensure the `.section-title` and `.main-title` styles match the design specifications.
- Adjust spacing between the decorative bar and the title.

## Approaches Considered

1. **Approach 1: Static Template Update (Recommended)**
   - Directly modify `template-parts/static-about.php`.
   - Pros: Immediate effect, respects existing architecture, keeps banner as requested.
   - Cons: Hardcoded content.

## Verification Plan

1. **Visual Check**: Compare the rendered page content (excluding banner) with `issues/d4.png`.
2. **Responsive Check**: Ensure the content looks good on mobile and tablet.
