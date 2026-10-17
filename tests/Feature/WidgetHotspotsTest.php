<?php

declare(strict_types=1);

use Capell\Core\Facades\CapellCore;
use Capell\LayoutBuilder\Support\WidgetExtensions\WidgetExtensionRegistry;
use Capell\WidgetHotspots\Data\HotspotsInputData;
use Capell\WidgetHotspots\Health\WidgetHotspotsHealthCheck;
use Capell\WidgetHotspots\Providers\WidgetHotspotsServiceProvider;
use Illuminate\Validation\ValidationException;

beforeEach(function (): void {
    CapellCore::forcePackageInstalled('capell-app/widget-hotspots');
    $this->app->register(WidgetHotspotsServiceProvider::class);
});
it('registers its interactive definition and health surfaces', function (): void {
    expect(resolve(WidgetExtensionRegistry::class)->definition('capell-app.hotspots')?->capabilities->supportsInteractions)->toBeTrue()->and(WidgetHotspotsHealthCheck::passed())->toBeTrue();
});
it('bounds coordinates and hotspot count', function (): void {
    expect(fn () => HotspotsInputData::validateAndCreate(['image' => 'image.jpg', 'alt' => 'Plan', 'hotspots' => [['label' => 'Bad', 'detail' => 'Detail', 'x' => -1, 'y' => 101]]]))->toThrow(ValidationException::class);
});
