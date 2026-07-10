<?php

declare(strict_types=1);

namespace Capell\WidgetHotspots;

use Capell\Core\Contracts\Extensions\RegistersExtensionContentWidget;

final class WidgetHotspotsContribution implements RegistersExtensionContentWidget
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^4.1';
    }
}
