---
name: Vibrant Luxury Marketplace
colors:
  surface: '#fbf9f9'
  surface-dim: '#dbdad9'
  surface-bright: '#fbf9f9'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f5f3f3'
  surface-container: '#efeded'
  surface-container-high: '#e9e8e7'
  surface-container-highest: '#e3e2e2'
  on-surface: '#1b1c1c'
  on-surface-variant: '#594138'
  inverse-surface: '#303031'
  inverse-on-surface: '#f2f0f0'
  outline: '#8d7166'
  outline-variant: '#e2bfb3'
  surface-tint: '#a63b00'
  primary: '#a63b00'
  on-primary: '#ffffff'
  primary-container: '#f4641e'
  on-primary-container: '#501800'
  inverse-primary: '#ffb598'
  secondary: '#5f5e5e'
  on-secondary: '#ffffff'
  secondary-container: '#e2dfde'
  on-secondary-container: '#636262'
  tertiary: '#5e5e5b'
  on-tertiary: '#ffffff'
  tertiary-container: '#92918d'
  on-tertiary-container: '#2a2a27'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#ffdbce'
  primary-fixed-dim: '#ffb598'
  on-primary-fixed: '#370e00'
  on-primary-fixed-variant: '#7f2b00'
  secondary-fixed: '#e5e2e1'
  secondary-fixed-dim: '#c8c6c5'
  on-secondary-fixed: '#1c1b1b'
  on-secondary-fixed-variant: '#474746'
  tertiary-fixed: '#e4e2dd'
  tertiary-fixed-dim: '#c8c6c2'
  on-tertiary-fixed: '#1b1c19'
  on-tertiary-fixed-variant: '#474744'
  background: '#fbf9f9'
  on-background: '#1b1c1c'
  surface-variant: '#e3e2e2'
typography:
  display-lg:
    fontFamily: Bodoni Moda
    fontSize: 64px
    fontWeight: '700'
    lineHeight: 72px
    letterSpacing: -0.02em
  display-lg-mobile:
    fontFamily: Bodoni Moda
    fontSize: 40px
    fontWeight: '700'
    lineHeight: 48px
    letterSpacing: -0.01em
  headline-lg:
    fontFamily: Bodoni Moda
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
  headline-md:
    fontFamily: Bodoni Moda
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  body-lg:
    fontFamily: Manrope
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Manrope
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  label-lg:
    fontFamily: Manrope
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.05em
  label-sm:
    fontFamily: Manrope
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.02em
spacing:
  unit: 8px
  container-max: 1280px
  gutter: 24px
  margin-desktop: 64px
  margin-mobile: 20px
---

## Brand & Style
This design system establishes a high-end, editorial-inspired atmosphere for a premium marketplace. It balances traditional luxury with a contemporary, energetic edge. The brand personality is sophisticated, exclusive, and confident, targeting a discerning audience that appreciates both heritage and modern vibrancy.

The visual style is a blend of **Minimalism** and **High-Contrast**. We utilize expansive whitespace, refined serif typography, and a "less-is-more" philosophy for layout, while injecting a high-energy accent color to create a memorable, modern identity. The interface should feel like a high-end digital lookbook—clean, airy, and expensive.

## Colors
The palette is anchored by a vibrant, high-saturation orange derived from the brand logo. This color is used strategically as a high-contrast accent rather than a dominant surface color to maintain a premium feel.

- **Primary (Vibrant Orange):** Reserved for Call-to-Actions (CTAs), active states, and critical highlights.
- **Secondary (Onyx Black):** Used for primary headings and icons to provide grounding and authority.
- **Tertiary (Champagne Cream):** A soft, warm off-white used for subtle section backgrounds to soften the starkness of pure white.
- **Neutral (Slate Gray):** Used for body copy and secondary metadata to ensure readability without competing with the primary branding.

## Typography
The typography system pairs the high-fashion elegance of **Bodoni Moda** with the technical precision of **Manrope**.

- **Display & Headlines:** Bodoni Moda brings a rhythmic, high-contrast serif look that evokes luxury editorial design. Use for large titles and product names.
- **Body & UI Elements:** Manrope provides a clean, modern sans-serif counterpoint that ensures legibility in descriptions, navigation, and functional UI components.
- **Labels:** Uppercase styling with slight letter-spacing should be applied to `label-lg` roles (like category headers or button text) to reinforce the sophisticated brand voice.

## Layout & Spacing
The layout follows a **Fixed Grid** philosophy on desktop to maintain the "editorial" feel, transitioning to a fluid model on mobile. 

- **Grid Model:** A 12-column grid is used for desktop. Content should be generously padded to create a sense of "luxury through space."
- **Rhythm:** An 8px linear scale governs all padding and margins. 
- **Breakpoints:** 
    - **Desktop (1280px+):** Max-width containers with 64px outer margins.
    - **Tablet (768px - 1279px):** 8-column grid with 40px margins.
    - **Mobile (<768px):** 4-column grid with 20px margins; typography scales down to mobile-specific tokens.

## Elevation & Depth
Depth is communicated through **Tonal Layers** and **Low-Contrast Outlines** rather than heavy shadows. This keeps the interface feeling light and modern.

- **Flat Surfaces:** Use the Tertiary color (Champagne Cream) to define distinct content areas against the primary white background.
- **Soft Borders:** Use 1px borders in a very light neutral tint (#E0E0E0) to separate cards or sections.
- **Subtle Lift:** For interactive elements like "Hovered Cards," use a very soft, diffused ambient shadow (0px 10px 30px rgba(0,0,0,0.04)) to suggest elevation without breaking the minimalist aesthetic.

## Shapes
To maintain a high-end, architectural feel, the design system utilizes **Sharp (0px)** corners for almost all structural elements.

- **Primary Elements:** Product cards, hero images, and primary buttons use sharp 90-degree angles to convey precision and luxury.
- **Secondary Elements:** Small UI tags or decorative icons may use a 1px "softening" only if strictly necessary for optical balance, but the default remains sharp.

## Components
- **Buttons:** Primary buttons feature a solid Vibrant Orange background with white uppercase text. Secondary buttons use a sharp Onyx Black outline with no fill.
- **Input Fields:** Use thin bottom-only borders (editorial style) or full sharp-edged boxes with 1px light gray strokes. Focus state is indicated by a 2px Vibrant Orange bottom border.
- **Cards:** Product cards should be borderless, using whitespace and sharp image crops to define their boundaries. Price and labels should be aligned to a strict grid.
- **Chips/Tags:** Small, sharp-edged rectangles with light neutral backgrounds and Slate Gray text for secondary info (e.g., "In Stock").
- **Checkboxes/Radios:** Square (sharp) geometry. Active states must use the Vibrant Orange fill to ensure high visibility.
- **Lists:** Clean, spacious lists with light horizontal dividers (1px) to separate items without cluttering the view.