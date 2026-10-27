<?php

declare(strict_types=1);

use Capell\Admin\Filament\Components\Forms\Editor\ContentBuilder;
use Capell\Admin\Support\Media\AdminUploadOwnership;
use Capell\Core\Facades\CapellCore;
use Capell\Frontend\Data\Assets\FrontendResourcePlanData;
use Capell\Frontend\Data\FrontendRuntimeManifestData;
use Capell\Frontend\Data\PublicPageRenderData;
use Capell\Frontend\Enums\RenderingStrategyEnum;
use Capell\WidgetHotspots\Data\HotspotsRenderData;
use Capell\WidgetHotspots\Filament\HotspotsWidget;
use Capell\WidgetHotspots\Providers\WidgetHotspotsServiceProvider;
use Dom\HTMLDocument;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;
use Livewire\Component;

it('selects named targets only from the current content builder and retains identities after reordering', function (): void {
    CapellCore::forcePackageInstalled('capell-app/widget-hotspots');
    $this->app->register(WidgetHotspotsServiceProvider::class);
    $targetId = (string) Str::uuid();
    $sourceId = (string) Str::uuid();
    $foreignId = (string) Str::uuid();
    $host = new class extends Component implements HasSchemas
    {
        use InteractsWithSchemas;

        /** @var array<string, mixed> */
        public array $data = [];
    };
    $builder = ContentBuilder::make('translations.en.content');
    Schema::make($host)->statePath('data')->components([$builder])->fill();
    $source = ['type' => HotspotsWidget::getWidgetName(), 'data' => ['image' => 'plan.jpg', 'alt' => 'Plan', 'hotspots' => [['label' => 'Door', 'detail' => 'Entrance', 'x' => 20, 'y' => 30, 'targetInstanceId' => $targetId]]] + ['__capell' => ['instance_id' => $sourceId]]];
    $target = ['type' => HotspotsWidget::getWidgetName(), 'data' => ['image' => 'plan.jpg', 'alt' => 'Plan', 'hotspots' => [['label' => 'Door', 'detail' => 'Entrance', 'x' => 20, 'y' => 30, 'targetInstanceId' => $targetId]]] + ['__capell' => ['instance_id' => $targetId]]];
    $target['data']['alt'] = 'Studio details';
    $builder->rawState(['source' => $source, 'target' => $target]);
    $foreignWidgets = [['type' => HotspotsWidget::getWidgetName(), 'data' => ['alt' => 'Private French target', '__capell' => ['instance_id' => $foreignId]]]];
    $host->data = array_replace_recursive($host->data, ['translations' => ['fr' => ['content' => $foreignWidgets]], 'otherPage' => ['content' => $foreignWidgets]]);

    $block = $builder->getChildSchema('source');
    $this->assertInstanceOf(Schema::class, $block);
    $repeater = $block->getComponent('hotspots');
    $this->assertInstanceOf(Repeater::class, $repeater);
    $repeater->rawState(['card' => ['image' => 'plan.jpg', 'alt' => 'Plan', 'hotspots' => [['label' => 'Door', 'detail' => 'Entrance', 'x' => 20, 'y' => 30, 'targetInstanceId' => $targetId]]]['hotspots'][0]]);
    $item = $repeater->getChildSchema('card');
    $this->assertInstanceOf(Schema::class, $item);
    $select = $item->getComponent('targetInstanceId');
    $this->assertInstanceOf(Select::class, $select);

    expect($select->getOptions())->toHaveKey($targetId)
        ->and($select->getOptions()[$targetId])->toContain('Studio details')
        ->and($select->getOptions())->not->toHaveKeys([$sourceId, $foreignId])
        ->and($select->getState())->toBe($targetId);
    expect(validator(['targetInstanceId' => $targetId], ['targetInstanceId' => $select->getValidationRules()])->passes())->toBeTrue();
    $state = $builder->getRawState();
    $this->assertIsArray($state);
    $source = $state['source'];
    $builder->rawState(['target' => $target, 'source' => $source]);
    expect($select->getOptions())->toHaveKey($targetId)
        ->and($select->getState())->toBe($targetId);
    $builder->rawState(['source' => $source]);
    view()->share('errors', new ViewErrorBag);
    $help = $select->getChildSchema(Select::BELOW_CONTENT_SCHEMA_KEY);
    $this->assertInstanceOf(Schema::class, $help);
    expect($select->getOptions())->toBe([])
        ->and($help->toHtml())->toContain(__('capell-widget-hotspots::widget.target_missing'))
        ->and($select->getState())->toBe($targetId);
    $rules = $select->getValidationRules();
    expect(validator(['targetInstanceId' => $foreignId], ['targetInstanceId' => $rules])->fails())->toBeTrue();
});

it('renders only prepared public target links and safely omits missing target identities', function (): void {
    CapellCore::forcePackageInstalled('capell-app/widget-hotspots');
    $this->app->register(WidgetHotspotsServiceProvider::class);
    $targetId = (string) Str::uuid();
    $renderData = HotspotsRenderData::from(['image' => 'plan.jpg', 'alt' => 'Plan', 'hotspots' => [['label' => 'Studio', 'detail' => 'Visit', 'x' => 20, 'y' => 30, 'targetInstanceId' => $targetId]]]);
    $pageData = new PublicPageRenderData(
        page: null,
        site: null,
        language: null,
        layout: null,
        theme: null,
        layoutGraph: null,
        runtimeManifest: FrontendRuntimeManifestData::forRenderingStrategy(RenderingStrategyEnum::BladeOnly),
        resourcePlan: new FrontendResourcePlanData([], [], [], [], [], [], [], ''),
        surrogateKeys: [],
        widgetInteractionLocators: [$targetId => '/widgets/signed-locator'],
    );
    $html = view('capell-widget-hotspots::widget', ['renderData' => $renderData, 'publicPageRenderData' => $pageData])->render();
    $document = HTMLDocument::createFromString($html, LIBXML_NOERROR);
    expect($document->querySelector('a[data-capell-interaction]')?->getAttribute('href'))->toBe('/widgets/signed-locator')
        ->and($html)->not->toContain($targetId, '__capell', 'targetInstanceId');
    $pageData->widgetInteractionLocators = [];
    $html = view('capell-widget-hotspots::widget', ['renderData' => $renderData, 'publicPageRenderData' => $pageData])->render();
    $document = HTMLDocument::createFromString($html, LIBXML_NOERROR);
    expect($document->querySelector('a[data-capell-interaction]'))->toBeNull()
        ->and($document->body?->textContent)->toContain('Studio', 'Visit')
        ->and($html)->not->toContain($targetId, '__capell', 'targetInstanceId');
});

it('selects newly added targets before the first save and persists their live identities', function (): void {
    Storage::fake('public');
    config()->set('filament.default_filesystem_disk', 'public');
    Storage::disk('public')->put('plan.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='));
    CapellCore::forcePackageInstalled('capell-app/widget-hotspots');
    $this->app->register(WidgetHotspotsServiceProvider::class);
    $host = new class extends Component implements HasSchemas
    {
        use InteractsWithSchemas;

        /** @var array<string, mixed> */
        public array $data = [];
    };
    $builder = ContentBuilder::make('content')->default([]);
    $form = Schema::make($host)->statePath('data')->components([$builder]);
    $form->fill();
    for ($i = 0; $i < 2; $i++) {
        $builder->getAddAction()->call([
            'component' => $builder,
            'arguments' => ['block' => HotspotsWidget::getWidgetName()],
            'data' => ['image' => 'plan.png', 'alt' => 'Studio details', 'hotspots' => [['label' => 'Door', 'detail' => 'Entrance', 'x' => 20, 'y' => 30]]],
        ]);
    }

    $state = $builder->getRawState();
    $this->assertIsArray($state);
    $keys = array_keys($state);
    $block = $builder->getChildSchema($keys[0]);
    $this->assertInstanceOf(Schema::class, $block);
    $repeater = $block->getComponent('hotspots');
    $this->assertInstanceOf(Repeater::class, $repeater);
    $rows = $repeater->getRawState();
    $this->assertIsArray($rows);
    $item = $repeater->getChildSchema(array_key_first($rows));
    $this->assertInstanceOf(Schema::class, $item);
    $select = $item->getComponent('targetInstanceId');
    $this->assertInstanceOf(Select::class, $select);

    $options = $select->getOptions();
    expect($options)->toHaveCount(1);
    $targetId = array_key_first($options);
    $this->assertIsString($targetId);
    expect($options[$targetId])->toContain('Studio details');
    $select->state($targetId);
    expect(validator(['targetInstanceId' => $select->getState()], ['targetInstanceId' => $select->getValidationRules()])->passes())->toBeTrue();
    $live = $builder->getRawState();
    expect(data_get($live, $keys[1] . '.data.__capell.instance_id'))->toBe($targetId)
        ->and($select->getOptions())->toBe($options);
    // A real add action stores a fresh upload; record that this session owns it.
    foreach ($keys as $key) {
        $image = $builder->getChildSchema($key)?->getComponent('image');
        $this->assertInstanceOf(BaseFileUpload::class, $image);
        resolve(AdminUploadOwnership::class)->remember($image, ['plan.png']);
    }
    $saved = $form->getState()['content'];
    expect(data_get($saved, '1.data.__capell.instance_id'))->toBe($targetId)
        ->and(data_get($saved, '0.data.hotspots.0.targetInstanceId'))->toBe($targetId);
    $form->fill(['content' => $saved]);
    $reopened = $builder->getRawState();
    $this->assertIsArray($reopened);
    $reopenedKeys = array_keys($reopened);
    $block = $builder->getChildSchema($reopenedKeys[0]);
    $this->assertInstanceOf(Schema::class, $block);
    $repeater = $block->getComponent('hotspots');
    $this->assertInstanceOf(Repeater::class, $repeater);
    $rows = $repeater->getRawState();
    $this->assertIsArray($rows);
    $item = $repeater->getChildSchema(array_key_first($rows));
    $this->assertInstanceOf(Schema::class, $item);
    $select = $item->getComponent('targetInstanceId');
    $this->assertInstanceOf(Select::class, $select);
    expect($select->getOptions())->toBe($options)
        ->and($select->getState())->toBe($targetId);
});
