# Hotspots Widget reference

<!-- prettier-ignore-start -->

Developer reference for `capell-app/widget-hotspots`. Start with the [README](../README.md) for what the extension does and how to install it.

## Package Details

Hotspots Widget is an **Available** Capell package in the **Capell Essentials** product group. It ships as `capell-app/widget-hotspots` and extends these surfaces: admin, frontend.

- Status: Available
- Tier: free
- Bundle: foundation
- Composer package: `capell-app/widget-hotspots`
- Namespace: `Capell\WidgetHotspots`
- Theme key: not applicable

## Technical Shape

### Service providers

- `Capell\WidgetHotspots\Providers\WidgetHotspotsServiceProvider`

### Filament classes

- `HotspotPositionPicker`
- `HotspotsWidget`

### Actions

- `BuildRevealTargetOptionsAction`

### Data objects

- `HotspotData`
- `HotspotsInputData`
- `HotspotsRenderData`

### Manifest contributions

- `asset: Capell\WidgetHotspots\WidgetHotspotsContribution`
- `content-widget: Capell\WidgetHotspots\WidgetHotspotsContribution`

### Health checks

- `Capell\WidgetHotspots\Health\WidgetHotspotsHealthCheck`

### Blade views

- `packages/widget-hotspots/resources/views/forms/position-picker.blade.php`
- `packages/widget-hotspots/resources/views/widget.blade.php`

### Cache tags

- `widget-hotspots`


## Data Model

This package has no schema impact. It extends Capell through `asset` contributions and `content-widget` contributions instead of declaring package-owned tables.

<!-- prettier-ignore-end -->
