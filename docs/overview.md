# Hotspots Widget

<!-- prettier-ignore-start -->

## What it does

Hotspots lets editors place up to 30 ordered points over a local image for product features, venue plans, diagrams, and annotated photography. Each point opens its own accessible details panel and can optionally link to another widget on the same rendered page.

## Do I need to do anything?

Add **Image hotspots** in Layout Builder, upload a local image, and enter meaningful alternative text. Add one to 30 ordered hotspots; each needs a label, detail text, and `x`/`y` positions from `0` to `100`. Optionally enter the UUID of a target widget instance when the hotspot should offer an **Open details** interaction. Preview the public page before publishing.

## Where it shows up

Editors configure it as a Layout Builder widget with numeric positions, labels, descriptions, and optional target-widget UUIDs. On the public page, every point is a keyboard-accessible details disclosure. A configured target becomes a resolved same-page interaction link when that target is present.

## Good to know

- This free beta extension belongs to Capell Foundation and requires Layout Builder.
- Hotspots use local images; remote image URLs are rejected.
- The point controls are at least 44 pixels and keyboard reachable.
- Target UUIDs are authoring data. Public output resolves a target to its interaction locator and never exposes the raw target UUID in the link.
- The current widget form does not expose an interaction-behaviour selector; resolved target interactions use the widget's default modal behaviour.

## Screenshot status

Capture the ordered hotspot editor and a public image with one focused hotspot and its detail visible. The committed Marketplace card is interim beta artwork until these two fixture captures are recorded.

---

For developers: see the [README](../README.md).

<!-- prettier-ignore-end -->
