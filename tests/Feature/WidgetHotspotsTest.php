<?php

declare(strict_types=1);

use Capell\Core\Facades\CapellCore;
use Capell\LayoutBuilder\Support\WidgetExtensions\WidgetExtensionRegistry;
use Capell\WidgetHotspots\Data\HotspotsInputData;
use Capell\WidgetHotspots\Filament\Components\HotspotPositionPicker;
use Capell\WidgetHotspots\Filament\HotspotsWidget;
use Capell\WidgetHotspots\Health\WidgetHotspotsHealthCheck;
use Capell\WidgetHotspots\Providers\WidgetHotspotsServiceProvider;
use Dom\HTMLDocument;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Symfony\Component\Process\Process;

beforeEach(function (): void {
    CapellCore::forcePackageInstalled('capell-app/widget-hotspots');
    $this->app->register(WidgetHotspotsServiceProvider::class);
});
it('registers its interactive definition and health surfaces', function (): void {
    expect(resolve(WidgetExtensionRegistry::class)->definition('capell-app.hotspots')?->capabilities->supportsInteractions)->toBeTrue()->and(WidgetHotspotsHealthCheck::passed())->toBeTrue();
});
it('bounds coordinates and hotspot count', function (): void {
    expect(fn (): HotspotsInputData => HotspotsInputData::validateAndCreate(['image' => 'image.jpg', 'alt' => 'Plan', 'hotspots' => [['label' => 'Bad', 'detail' => 'Detail', 'x' => -1, 'y' => 101]]]))->toThrow(ValidationException::class);
});

it('offers an image backed position picker beside the stored numeric coordinates', function (): void {
    $host = new class extends Component implements HasSchemas
    {
        use InteractsWithSchemas;

        /** @var array<string, mixed> */
        public array $data = [];
    };
    $block = HotspotsWidget::make();
    $schema = Schema::make($host)->statePath('data')->components([$block]);
    $schema->fill();

    $blockSchema = $block->getChildSchema();
    $this->assertInstanceOf(Schema::class, $blockSchema);
    $components = $blockSchema->getComponents();
    $repeater = $components[2];
    $this->assertInstanceOf(Repeater::class, $repeater);
    $repeater->state(['stable-point' => ['label' => 'Studio', 'detail' => 'Room', 'x' => 25.5, 'y' => 70]]);
    $host->data['image'] = 'plans/studio.jpg';
    $item = $repeater->getChildSchema('stable-point');
    $this->assertInstanceOf(Schema::class, $item);
    $picker = $item->getComponent('position');
    $this->assertInstanceOf(HotspotPositionPicker::class, $picker);
    $x = $item->getComponent('x');
    $y = $item->getComponent('y');
    $this->assertInstanceOf(TextInput::class, $x);
    $this->assertInstanceOf(TextInput::class, $y);
    view()->share('errors', new ViewErrorBag);
    $html = $picker->toHtml();
    $document = HTMLDocument::createFromString($html, LIBXML_NOERROR);
    expect($document->querySelector('img')?->getAttribute('src'))->toContain('plans/studio.jpg')
        ->and($document->querySelector('button[aria-describedby]'))->not->toBeNull()
        ->and($x->getState())->toEqual(25.5)
        ->and($y->getState())->toEqual(70);
    $binding = $document->querySelector('[x-data]')?->getAttribute('x-data');
    $process = new Process(['node', __DIR__ . '/../JavaScript/position-picker.mjs']);
    $process->setInput(json_encode(['expression' => $binding, 'state' => [$picker->coordinateStatePath('x') => 25.5, $picker->coordinateStatePath('y') => 70]], JSON_THROW_ON_ERROR));
    $process->run();
    expect($process->getErrorOutput())->toBe('')
        ->and($process->getExitCode())->toBe(0);
    $repeater->state(['other-point' => ['label' => 'Door', 'detail' => 'Entry', 'x' => 10, 'y' => 20], 'stable-point' => ['label' => 'Studio', 'detail' => 'Room', 'x' => 25.5, 'y' => 70]]);
    expect($picker->coordinateStatePath('x'))->toEndWith('hotspots.stable-point.x')
        ->and($picker->isDehydrated())->toBeFalse();

});
