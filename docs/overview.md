# Hotspots Widget

<!-- prettier-ignore-start -->

Numbered dots sit on your product photo, floor plan or diagram, and a visitor taps one to read what that part is. A garden centre could mark the vent and the door on a photo of a greenhouse and write a line about each.

To place a dot, drag it onto the spot in a preview of your image, or nudge it with the arrow keys when it has to be exact. One image takes up to thirty dots, each with a short label and a few lines of plain text. The image has to be one you've uploaded to your site.

A dot's note can also carry an "Open details" link to something else you've put on the same page, such as a size guide or a price table. It can't link to a different page.

Visitors using a keyboard move from dot to dot with the arrow keys. On a phone the dots are big enough to hit with a thumb, and they take your theme's main colour.

## What it does

Hotspots lets editors place up to 30 ordered points over a local image for product features, venue plans, diagrams, and annotated photography. Each point opens its own accessible details panel and can optionally link to another widget on the same rendered page.

## Do I need to do anything?

Add **Image hotspots** in Layout Builder, upload a local image, and enter meaningful alternative text. Add one to 30 ordered hotspots; each needs a label and detail text. Click or drag on its image preview to position it. Focus the point and use arrow keys for 1% steps, or Shift and arrow keys for 10% steps. Numeric `x`/`y` inputs remain available from `0` to `100`: X starts at the left edge and Y at the top. Reordering keeps each point's saved percentages. Optionally choose a named target in **Content to open** when the hotspot should offer an **Open details** interaction. Preview the public page before publishing.

## Where it shows up

Editors configure it as a Layout Builder widget with an image position picker, numeric percentages, labels, descriptions, and optional named same-content targets. On the public page, every point is a keyboard-accessible details disclosure. A configured target becomes a resolved same-page interaction link when that target is present.

## Good to know

- This free extension belongs to Capell Essentials and requires Layout Builder.
- Hotspots use local images; remote image URLs are rejected.
- The point controls are at least 44 pixels and keyboard reachable.
- Target UUIDs are authoring data. Public output resolves a target to its interaction locator and never exposes the raw target UUID in the link.
- The current widget form does not expose an interaction-behaviour selector; resolved target interactions use the widget's default modal behaviour.

**Content to open** lists registered widgets from the current page content field and language, excluding the widget being edited. It keeps stable identities internally, so reordering does not change the target. Targets in other pages, languages, or separate content fields are not offered. If a saved target is removed or unavailable, the authoring form asks you to select a replacement before publishing. The public page keeps the existing safe fallback without exposing the missing identity.

---

For developers: see the [README](../README.md).

<!-- prettier-ignore-end -->
