<?php

declare(strict_types=1);

namespace Capell\WidgetHotspots\Providers;

use Capell\Core\Enums\InteractionBehavior;
use Capell\Core\Enums\InteractionTargetType;
use Capell\Core\Enums\InteractionTriggerEvent;
use Capell\Core\Enums\PresentationLoadingStrategy;
use Capell\LayoutBuilder\Data\WidgetExtensions\WidgetExtensionCapabilitiesData;
use Capell\LayoutBuilder\Data\WidgetExtensions\WidgetExtensionDefinitionData;
use Capell\LayoutBuilder\Enums\WidgetPresentationCapability;
use Capell\LayoutBuilder\Providers\AbstractFoundationWidgetServiceProvider;
use Capell\WidgetHotspots\Data\HotspotsInputData;
use Capell\WidgetHotspots\Data\HotspotsRenderData;
use Capell\WidgetHotspots\Filament\HotspotsWidget;
use Override;

final class WidgetHotspotsServiceProvider extends AbstractFoundationWidgetServiceProvider
{
    public static string $name = 'capell-widget-hotspots';

    public static string $packageName = 'capell-app/widget-hotspots';

    #[Override]
    public function packageBooted(): void {}

    #[Override]
    protected static function resourceGroup(): string
    {
        return 'capell-app.widget-hotspots';
    }

    #[Override]
    protected function bootInstalledRuntime(): void
    {
        $this->registerWidget();
    }

    #[Override]
    protected function definition(): WidgetExtensionDefinitionData
    {
        return new WidgetExtensionDefinitionData(key: 'capell-app.hotspots', packageName: self::$packageName, stateVersion: 1, filamentWidget: HotspotsWidget::class, inputData: HotspotsInputData::class, renderData: HotspotsRenderData::class, fallbackView: 'capell-widget-hotspots::widget', components: ['blade' => 'capell::widgets.capell-app.hotspots'], resourceGroups: [self::resourceGroup()], defaultResourceLoadingStrategy: PresentationLoadingStrategy::Visible, capabilities: new WidgetExtensionCapabilitiesData(supportsInteractions: true, requiresInstanceIdentity: true, presentationCapabilities: [WidgetPresentationCapability::Width, WidgetPresentationCapability::Alignment, WidgetPresentationCapability::LoadingStrategy], supportedInteractionEvents: [InteractionTriggerEvent::Click], supportedInteractionBehaviors: [InteractionBehavior::Modal, InteractionBehavior::SlideOver, InteractionBehavior::InlineReveal, InteractionBehavior::ReplaceRegion], supportedInteractionTargetTypes: [InteractionTargetType::Widget]));
    }
}
