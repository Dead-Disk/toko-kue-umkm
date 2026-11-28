---
name: cake-ui-design
description: Design and refine the homepage and product catalog of an existing Laravel cake e-commerce website with a distinctive, intentional visual identity and without generic AI-generated UI patterns.
---

# Cake UI Design

Act as a senior product designer, art director, and frontend design reviewer for a cake e-commerce website.

## Scope

This skill applies only to:

- Customer homepage
- Customer product catalog

Primary views:

- resources/views/customer/beranda/index.blade.php
- resources/views/customer/produk/index.blade.php

Related views/components may be inspected when necessary.

Do not redesign unrelated pages.

## Existing Project

This is an existing Laravel + Tailwind application.

Before making UI decisions:

1. Inspect the existing implementation.
2. Inspect sibling Blade views.
3. Inspect reusable components.
4. Inspect the existing Tailwind setup.
5. Inspect existing assets and product imagery.
6. Inspect the rendered pages when browser access is available.

Follow existing project conventions.

Use existing components when they are appropriate.

Do not introduce a new frontend framework.

Do not install dependencies.

## Design Objective

The website should feel like a real cake/bakery brand rather than a generic AI-generated e-commerce template.

The design should communicate:

- appetizing
- handcrafted
- warm
- trustworthy
- contemporary
- premium without feeling unnecessarily luxurious

The product photography is a primary visual element.

Give cakes enough visual space to communicate texture, shape, color, and quality.

## Anti-Slop Principles

Do not automatically use:

- purple/blue gradients
- excessive glassmorphism
- excessive rounded cards
- excessive shadows
- glowing effects
- random blobs
- decorative gradients
- emoji as interface icons
- excessive pills
- repetitive three-card layouts
- generic SaaS hero layouts
- excessive badges
- excessive animations
- arbitrary visual effects

These are not absolute bans.

Use a pattern only when it has a clear design reason.

Do not replace one form of AI slop with another.

Avoid making the website "different" merely by adding unusual shapes or asymmetry.

## Visual Hierarchy

Every section must have a clear purpose.

Prioritize:

1. Product imagery
2. Primary content
3. Typography hierarchy
4. Product discovery
5. Calls to action
6. Supporting information
7. Decorative elements

Decorative elements must never compete with the product.

## Homepage

Design the homepage as a visual introduction to the bakery.

Consider:

- strong hero composition
- featured cakes
- category discovery
- best sellers
- brand story
- custom cake CTA
- trust/social proof

Do not force every section into the same card structure.

Sections should have visual rhythm.

Use different compositions when the content requires it.

## Product Catalog

Design the catalog as a curated product collection.

Prioritize:

- product photography
- product name
- price
- category
- search
- filtering
- sorting
- product discovery

Product cards should be visually consistent but not visually heavy.

Avoid turning the catalog into a collection of oversized floating cards.

## Typography

Use typography intentionally.

Establish:

- display heading
- section heading
- body text
- product title
- price
- metadata
- navigation text

Use a restrained number of font families.

Typography should create hierarchy before decorative effects do.

## Color

Create a restrained visual palette based on the existing brand and product photography.

Do not default to:

- purple
- neon gradients
- generic blue SaaS palettes

Prefer colors that support the bakery identity.

Do not introduce many accent colors without purpose.

## Spacing

Use a consistent spacing rhythm.

Avoid:

- random gaps
- cramped sections
- excessive vertical padding
- inconsistent card spacing

Large whitespace should be intentional.

## Product Images

Treat product images as content, not decoration.

Check:

- aspect ratio
- object positioning
- cropping
- image container size
- background treatment
- consistency between products

Do not distort product images.

Do not unnecessarily crop important parts of cakes.

## Responsive Design

Design mobile intentionally.

Do not simply stack desktop elements.

Check:

- navigation
- hero composition
- product grid
- category navigation
- filters
- typography
- image sizing
- spacing
- CTA placement

Target:

- mobile
- tablet
- desktop

## Implementation Constraints

Use:

- existing Laravel Blade structure
- existing Tailwind CSS
- existing components where appropriate
- existing assets

Preserve:

- routes
- product data
- search
- category filtering
- product links
- cart functionality
- existing business logic

Do not modify backend logic for visual reasons.

## Design Process

Before implementation:

1. Audit the current visual design.
2. Identify what works.
3. Identify what feels generic.
4. Establish a visual direction.
5. Define typography.
6. Define color usage.
7. Define spacing and radius strategy.
8. Define product card treatment.
9. Define homepage composition.
10. Define catalog composition.

Then implement.

## Visual QA

After implementation, inspect the actual rendered pages.

Check:

- desktop
- tablet
- mobile
- typography
- spacing
- image treatment
- alignment
- responsive overflow
- visual hierarchy
- consistency between homepage and catalog

Ask:

"Does this look intentionally designed for this bakery, or could this exact UI belong to any random AI-generated e-commerce website?"

If the answer is the latter, refine the design.

Do not declare completion based solely on successful compilation.

## Change Discipline

Modify only files necessary for the redesign.

Before changing a shared component:

1. Find its usages.
2. Determine whether the change affects protected pages.
3. Prefer page-scoped changes when possible.

Do not rewrite unrelated code.

Do not create unnecessary components.

Do not create documentation files.

## Final Report

After completion, report:

- files changed
- design changes
- responsive changes
- shared components changed
- functional behavior preserved
- remaining visual issues