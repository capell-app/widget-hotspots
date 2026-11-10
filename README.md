# Hotspots Widget

<!-- prettier-ignore-start -->

## What It Does

Hotspots adds a Layout Builder widget for placing bounded, accessible points over a local image. Each point can expose supporting content or an approved lazy widget target.

- **Price and availability:** Check current availability and price on the [Capell Marketplace listing](https://capell.app/extensions/widget-hotspots).
- **Licence:** Check the licence terms on the [Capell Marketplace listing](https://capell.app/extensions/widget-hotspots).
- **Requires:** `PHP ^8.4`, `capell-app/admin ^1.0`, `capell-app/core ^1.0.66`, `capell-app/frontend ^1.0`, `capell-app/layout-builder ^1.0.44`, `spatie/laravel-package-tools ^1.0`.
- **Install:** `composer require capell-app/widget-hotspots`.

Editors position and label hotspots in Layout Builder, and visitors can inspect those points from the public image using the provided interaction controls.

## Who It Is For

**For developers:** The typed extension validates coordinate bounds, point limits, local media, and registry-backed targets before public rendering.

**For teams:** Teams can annotate products, diagrams, or places directly on an image without building a custom interactive map.

## Quick Start

1. Install the package: `composer require capell-app/widget-hotspots`.
2. Open a page, click **Layout builder**, **Add widgets**, then choose **Image hotspots**.

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
| The image is refused | The path is a web address, contains unsupported characters, or the alt text is empty | Check the image and alt text fields | Pick the image from the media library and describe it in the alt text |
| A hotspot is refused | It sits outside the image (x or y outside 0 to 100), has no label or detail, or more than 30 hotspots exist | Check each hotspot's position, label and detail | Place hotspots within 0 to 100 on both axes and keep between 1 and 30 |

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
