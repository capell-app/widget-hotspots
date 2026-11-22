<?php

declare(strict_types=1);

use Capell\Core\Data\Rendering\FrontendRuntimeManifestData;
use Capell\Core\Enums\Rendering\RenderingStrategyEnum;
use Capell\Core\Facades\CapellCore;
use Capell\Frontend\Data\Assets\FrontendResourcePlanData;
use Capell\Frontend\Data\PublicPageRenderData;
use Capell\WidgetHotspots\Data\HotspotsRenderData;
use Capell\WidgetHotspots\Providers\WidgetHotspotsServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Walk a render payload and name every Eloquent model held anywhere inside it.
 *
 * @param  array<int, true>  $seen
 * @return list<string>
 */
function hotspotsModelsIn(mixed $value, string $path, array &$seen = []): array
{
    if ($value instanceof Model) {
        return [$path . ' (' . $value::class . ')'];
    }

    if (is_array($value)) {
        $found = [];
        foreach ($value as $key => $item) {
            array_push($found, ...hotspotsModelsIn($item, $path . '[' . $key . ']', $seen));
        }

        return $found;
    }

    if (! is_object($value) || isset($seen[spl_object_id($value)])) {
        return [];
    }

    $seen[spl_object_id($value)] = true;
    $found = [];
    foreach ((array) $value as $property => $item) {
        array_push($found, ...hotspotsModelsIn($item, $path . '->' . preg_replace('/^\0.*\0/', '', (string) $property), $seen));
    }

    return $found;
}

it('renders the public hotspots widget from hydrated data with lazy loading disabled and no queries', function (): void {
    CapellCore::forcePackageInstalled('capell-app/widget-hotspots');
    $this->app->register(WidgetHotspotsServiceProvider::class);
    $previous = Model::preventsLazyLoading();
    Model::preventLazyLoading();
    $violations = [];
    Model::handleLazyLoadingViolationUsing(static function (Model $model, string $relation) use (&$violations): void {
        $violations[] = $model::class . '::' . $relation;
    });
    $renderData = HotspotsRenderData::from(['image' => 'plan.jpg', 'alt' => 'Plan', 'hotspots' => [['label' => 'Studio', 'detail' => 'Visit us', 'x' => 20, 'y' => 30, 'targetInstanceId' => 'target-1']]]);
    $pageData = new PublicPageRenderData(
        layoutGraph: null,
        runtimeManifest: FrontendRuntimeManifestData::forRenderingStrategy(RenderingStrategyEnum::BladeOnly),
        resourcePlan: new FrontendResourcePlanData([], [], [], [], [], [], [], ''),
        surrogateKeys: [],
        widgetInteractionLocators: ['target-1' => '/widgets/signed-locator'],
    );
    $queries = [];
    DB::listen(static function ($query) use (&$queries): void {
        $queries[] = $query->sql;
    });

    try {
        $html = view('capell-widget-hotspots::widget', ['renderData' => $renderData, 'publicPageRenderData' => $pageData])->render();
    } finally {
        Model::preventLazyLoading($previous);
        Model::handleLazyLoadingViolationUsing(null);
    }

    expect($html)->toContain('Visit us', '/widgets/signed-locator')
        ->and($queries)->toBe([])
        ->and($violations)->toBe([])
        ->and(hotspotsModelsIn($renderData, 'renderData'))->toBe([])
        ->and(hotspotsModelsIn($pageData, 'publicPageRenderData'))->toBe([]);
});
