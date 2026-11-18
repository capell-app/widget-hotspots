# Hotspots Widget

<!-- prettier-ignore-start -->

Numbered dots sit on your product photo, floor plan or diagram, and a visitor taps one to read what that part is. A garden centre could mark the vent and the door on a photo of a greenhouse and write a line about each.

To place a dot, drag it onto the spot in a preview of your image, or nudge it with the arrow keys when it has to be exact. One image takes up to thirty dots, each with a short label and a few lines of plain text. The image has to be one you've uploaded to your site.

A dot's note can also carry an "Open details" link to something else you've put on the same page, such as a size guide or a price table. It can't link to a different page.

Visitors using a keyboard move from dot to dot with the arrow keys. On a phone the dots are big enough to hit with a thumb, and they take your theme's main colour.

- **Price and availability:** Check current availability and price on the [Capell Marketplace listing](https://capell.app/extensions/widget-hotspots).
- **Licence:** Check the licence terms on the [Capell Marketplace listing](https://capell.app/extensions/widget-hotspots).
- **Requires:** `PHP ^8.4`, `capell-app/admin ^1.0`, `capell-app/core ^1.0.66`, `capell-app/frontend ^1.0`, `capell-app/layout-builder ^1.0.44`, `spatie/laravel-package-tools ^1.0`.
- **Install:** `composer require capell-app/widget-hotspots`.

## Quick Start

1. Install the package: `composer require capell-app/widget-hotspots`.
2. Open a page, click **Layout builder**, **Add widgets**, then choose **Image hotspots**.

## For Developers

The typed extension validates coordinate bounds, point limits, local media, and registry-backed targets before public rendering.

## Configure

This extension works without configuration. There is nothing to set after install.

## Limits And Pitfalls

- Install the required Capell packages at the versions in `composer.json`: `capell-app/admin ^1.0`, `capell-app/core ^1.0.66`, `capell-app/frontend ^1.0`, `capell-app/layout-builder ^1.0.44`.
- Keep public Blade and cached HTML free of authoring markers, model IDs, permissions, signed editor URLs, and lazy database queries.
- Custom write integrations must preserve invalidation for `widget-hotspots` cache tags.

## Screens And Workflow

![Hotspots public output](docs/screenshots/hotspots-public-output.png)

![Hotspots fields in Layout Builder](docs/screenshots/hotspots-layout-builder-editor.png)

- Hotspots Widget extension card (marketplace).
- Hotspots fields in Layout Builder (admin).
- Hotspots public output (frontend).
- Hotspots fields in Layout Builder with admin sidebar menu open (admin).

## Install Impact

- Required packages: `capell-app/admin`, `capell-app/core`, `capell-app/frontend`, `capell-app/layout-builder`.
- Admin navigation: no admin page or resource contribution is declared.
- Admin/editor extensions: `content-widget: WidgetHotspotsContribution`.
- Permissions: no package permission declarations or Shield gates detected; host access rules still apply.
- Public routes: none declared.
- Database changes: no package migrations declared.
- Config: no package config files.
- Settings: no package settings declared.
- Queues or schedules: none declared.
- Cache tags: `widget-hotspots`.
- Commands: none declared.

## Troubleshooting

| Symptom | Likely cause | Check | Fix |
| --- | --- | --- | --- |
| Package surface is missing after install | Provider or manifest is not loaded | Confirm `capell.json`, package `composer.json`, and provider registration | Reinstall the package, refresh Composer autoload, and clear host caches |
| Public output leaks unexpected state | Render data, cache variation, or authoring boundary has regressed | Check public Blade, cache tags, and public-output safety tests | Move data loading out of Blade and rerun the package public-output tests |

## Upgrading

No manual steps between minor versions.

## Next Steps

- [Package docs](docs/README.md)
- [Overview](docs/overview.md)
- [Troubleshooting](#troubleshooting)
- [Developer reference](docs/reference.md)
- [Marketplace assets](docs/assets/marketplace/)
- [Capell content language plan](../../docs/CONTENT_LANGUAGE_PLAN.md)
- [Capell documentation design system](../../docs/DESIGN_SYSTEM.md)
- [Capell and package ERD notes](../../docs/erd/capell-and-package-erds.md)
- Related packages: [Layout Builder](../layout-builder/README.md).

<!-- prettier-ignore-end -->
