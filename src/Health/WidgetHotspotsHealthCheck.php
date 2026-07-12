<?php

declare(strict_types=1);

namespace Capell\WidgetHotspots\Health;

use Capell\LayoutBuilder\Health\AbstractWidgetExtensionHealthCheck;
use Capell\WidgetHotspots\Filament\HotspotsWidget;

final class WidgetHotspotsHealthCheck extends AbstractWidgetExtensionHealthCheck
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^0.0';
    }

    protected static function definitionKey(): string
    {
        return 'capell-app.hotspots';
    }

    protected static function filamentWidget(): string
    {
        return HotspotsWidget::class;
    }

    protected static function fallbackView(): string
    {
        return 'capell-widget-hotspots::widget';
    }

    protected static function resourceGroups(): array
    {
        return ['capell-app.widget-hotspots'];
    }
}
