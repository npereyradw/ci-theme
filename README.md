# CI Theme

A WordPress block theme (Full Site Editing) for professional services firms. Every section of
the design is a native block with a dynamic PHP render, a live preview in the editor and its own
assets, styled only with the values of `theme.json`.

- WordPress 6.6+ and PHP 8.0+
- No page builder and no jQuery: blocks built with `@wordpress/scripts`, motion with GSAP

## Getting started

```bash
npm install
npm run build      # compiles src/ into build/
npm run start      # rebuilds on save
npm run lint:js
npm run lint:css
```

Activate the theme, set the logo in **Appearance → Editor → Site Identity** (a version for dark
backgrounds can be chosen in the header and footer blocks) and create pages from the
**CI · Pages** patterns.

## Structure

```
ci-theme/
├── functions.php        ← loader: only requires the modules in inc/
├── theme.json           ← palette, typography (Geist), font sizes and spacing
├── templates/, parts/   ← templates and parts (header and footer are blocks)
├── patterns/            ← ready-made sections, full pages and the template contents
├── assets/              ← base CSS, font and the map worker
├── inc/<module>/        ← one module per folder (entry file, classes and assets)
└── src/
    ├── blocks/<block>/  ← block.json, edit.js, render.php, style.css, view.js
    ├── editor/          ← shared editor library (@ci-theme/editor)
    ├── motion/          ← animations (GSAP + ScrollTrigger + SplitText + DrawSVG)
    ├── gsap/            ← shared GSAP build (handle ci-theme-gsap)
    └── forms/           ← Contact Form 7 submissions through its REST API
```

## Blocks

| Category | Blocks |
|---|---|
| CI · Sections | Home hero, Stats, Features, Process, Text and media, Reach, Audiences, Page header, Contact, Map, Call to action |
| CI · Dynamic content | Areas, Services, Projects, News, Testimonials, Team, Institutions |
| CI · Templates | Site header, Site footer, Service hero, Project header and body, Post header and body, Not found |

Every section shares the same style options: light, gray or dark scheme, title, text and accent
colors from the palette, top and bottom spacing, and animations on or off.

## Content contract

The theme does not register content types. A companion plugin provides them with these names,
and every dynamic section simply stays hidden while its content type is missing.

| Type | Name | Fields |
|---|---|---|
| Services | `ci_service` | meta `status` (`active` / `coming_soon`), `includes`, `cover`; taxonomy `ci_area` |
| Projects | `ci_project` | meta `client`, `location`, `year`, `service`, `cover`; taxonomy `ci_area` |
| Team | `ci_team_member` | meta `role`, `initials`, `education`, `linkedin` |
| Testimonials | `ci_testimonial` | meta `quote`, `role` |
| Institutions | `ci_institution` | meta `detail`, `full_name`, `relation`, `website` |
| Areas | `ci_area` (taxonomy) | term meta `icon`, `order` |

Contact details, social profiles, the call-to-action texts, the form IDs and the analytics
settings come from the `ci_theme_site_settings` filter:

```php
add_filter( 'ci_theme_site_settings', function ( $settings ) {
	$settings['phone']           = '+1 555 0100';
	$settings['email']           = 'hello@example.com';
	$settings['contact_form_id'] = 12;
	return $settings;
} );
```

## Performance and accessibility

- Block CSS is loaded only on the pages that use it and inlined into the HTML.
- Above-the-fold content enters with CSS (fast LCP); the rest is animated with GSAP.
- `prefers-reduced-motion` is respected, and everything is shown if the script does not arrive.
- The map is a static image until the visitor asks for the interactive version.
- Forms with accessible validation (`aria-invalid`, `aria-describedby`, focus on the first
  error) and a honeypot field instead of reCAPTCHA.

## Credits

Built by Nicolás Pereyra. Claude (Anthropic) was used as an assistant to optimize the work.

## License

GPLv2 or later.
