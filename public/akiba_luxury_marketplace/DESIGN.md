---
name: Akiba Luxury Marketplace
colors:
  surface: '#fbf9f8'
  surface-dim: '#dbdad9'
  surface-bright: '#fbf9f8'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f5f3f3'
  surface-container: '#efeded'
  surface-container-high: '#e9e8e7'
  surface-container-highest: '#e4e2e2'
  on-surface: '#1b1c1c'
  on-surface-variant: '#444748'
  inverse-surface: '#303031'
  inverse-on-surface: '#f2f0f0'
  outline: '#747878'
  outline-variant: '#c4c7c7'
  surface-tint: '#5f5e5e'
  primary: '#000000'
  on-primary: '#ffffff'
  primary-container: '#1c1b1b'
  on-primary-container: '#858383'
  inverse-primary: '#c8c6c5'
  secondary: '#85532a'
  on-secondary: '#ffffff'
  secondary-container: '#febb8a'
  on-secondary-container: '#794921'
  tertiary: '#000000'
  on-tertiary: '#ffffff'
  tertiary-container: '#1c1c1a'
  on-tertiary-container: '#858481'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#e5e2e1'
  primary-fixed-dim: '#c8c6c5'
  on-primary-fixed: '#1c1b1b'
  on-primary-fixed-variant: '#474646'
  secondary-fixed: '#ffdcc4'
  secondary-fixed-dim: '#fbb887'
  on-secondary-fixed: '#2f1400'
  on-secondary-fixed-variant: '#693c15'
  tertiary-fixed: '#e5e2df'
  tertiary-fixed-dim: '#c8c6c3'
  on-tertiary-fixed: '#1c1c1a'
  on-tertiary-fixed-variant: '#474745'
  background: '#fbf9f8'
  on-background: '#1b1c1c'
  surface-variant: '#e4e2e2'
typography:
  headline-xl:
    fontFamily: Bodoni Moda
    fontSize: 48px
    fontWeight: '700'
    lineHeight: 56px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Bodoni Moda
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
    letterSpacing: -0.01em
  headline-lg-mobile:
    fontFamily: Bodoni Moda
    fontSize: 28px
    fontWeight: '600'
    lineHeight: 34px
  headline-md:
    fontFamily: Bodoni Moda
    fontSize: 24px
    fontWeight: '500'
    lineHeight: 32px
  body-lg:
    fontFamily: Hanken Grotesk
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Hanken Grotesk
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  label-sm:
    fontFamily: Hanken Grotesk
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.05em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 8px
  container-margin-mobile: 20px
  container-margin-desktop: 80px
  gutter: 16px
  section-gap: 64px
---

## Brand & Style
The design system for AkibaShop is anchored in **High-End Minimalism** with a focus on editorial clarity and premium fashion aesthetics. It serves a fashion-forward Gabonese audience that values exclusivity, quality, and modern sophistication. 

The visual narrative avoids cluttered layouts, instead utilizing expansive whitespace to let high-quality product photography act as the primary visual driver. The style combines the precision of a luxury boutique with the energetic pulse of modern Libreville, creating an emotional response of trust, aspiration, and effortless style.

## Colors
The palette is a sophisticated mix of deep "Obsidian Black" and "Crisp White," establishing a high-contrast foundation typical of luxury editorial design. 

- **Primary (Obsidian):** Used for core branding, typography, and primary navigation to ground the interface.
- **Secondary (Terracotta Gold):** A warm, vibrant accent used sparingly for calls-to-action, badges, and highlights. This color represents the Gabonese spirit—earthy yet radiant.
- **Tertiary (Sandstone):** A soft, warm grey used for background sections and subtle UI containers to prevent the interface from feeling clinical.
- **Neutrals:** A range of greys used primarily for secondary text and borders to maintain a hierarchical balance.

## Typography
The typography strategy utilizes a "Modern Editorial" approach. **Bodoni Moda** provides the high-contrast serif flair required for luxury fashion, used exclusively for headers and prominent product titles. **Hanken Grotesk** offers a sharp, contemporary sans-serif counterpart for body copy and UI elements, ensuring maximum legibility on mobile devices.

Maintain tight tracking for large headlines and slightly increased tracking for uppercase labels to enhance the premium feel. All French text (currency in FCFA) should adhere to standard typographic rules for spacing and punctuation.

## Layout & Spacing
The layout follows a **Fluid-Fixed hybrid model**. On mobile, a 4-column grid is used with 20px side margins to accommodate comfortable thumb reach. On desktop, a 12-column grid is utilized with a maximum content width of 1440px.

A 8px baseline grid governs all vertical spacing. Generous "breathing room" is mandatory between product cards and sections (using the `section-gap` token) to maintain the minimalist luxury aesthetic. Product grids should utilize a 2-column layout on mobile to keep photography large and impactful.

## Elevation & Depth
This design system employs **Tonal Layering** and **Ambient Shadows** to create a sense of tactile luxury. 

- **Surfaces:** Use the Tertiary color (Sandstone) for background surfaces to create soft depth against pure white cards.
- **Shadows:** Avoid heavy, dark shadows. Use ultra-diffused "Atmospheric Shadows" (Hex #000000 at 4-6% opacity) with a large blur radius (20px+) for product cards and modals.
- **Interactive States:** When a user interacts with a product card, the elevation should subtly increase, creating a "lifting" effect.

## Shapes
The shape language is **Rounded**, using a consistent 8px to 12px radius. This softens the high-contrast color palette, making the premium experience feel approachable rather than intimidating.

- **Standard Buttons & Inputs:** 8px corner radius.
- **Product Cards:** 12px corner radius for a softer, more modern framing of photography.
- **Search Bars:** Should utilize the `rounded-xl` (24px) for a more organic, "pill" feel that invites interaction.

## Components
- **Buttons:** Primary buttons are solid Obsidian Black with white text. Secondary buttons use an outline style with the Terracotta accent. Always use a minimum touch target of 48px for mobile.
- **Product Cards:** Minimalist design with no borders. Use high-quality imagery that fills the card to the edges of the 12px radius. Titles in Bodoni Moda, prices in bold Hanken Grotesk.
- **Input Fields:** Bottom-border only or very light grey stroke (1px). Focus state uses the Terracotta Gold accent.
- **Chips/Badges:** Use for "Nouveau" or "Exclusif" tags. Small, uppercase text with generous horizontal padding.
- **Navigation:** A persistent bottom navigation bar on mobile for high-frequency actions (Home, Search, Cart, Profile), using thin-stroke icons.
- **Checkout:** Optimize for FCFA display; ensure numeric inputs are large and the "Payer" button is anchored to the bottom of the viewport for easy mobile access.