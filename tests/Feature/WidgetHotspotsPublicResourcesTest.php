<?php

declare(strict_types=1);

use Capell\Core\Enums\ContentStructure;
use Capell\Core\Facades\CapellCore;
use Capell\Core\Models\Page;
use Capell\Core\Models\Site;
use Capell\Core\Models\SiteDomain;
use Capell\LayoutBuilder\Support\LayoutBuilderLayoutWidgetResourceUsageContributor;
use Capell\Tests\Support\Concerns\CreatesAdminUser;
use Dom\Element;
use Dom\HTMLDocument;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Workbench\App\Support\WidgetScreenshotFixtures;

require_once dirname(__DIR__, 4) . '/tests/Packages/Support/PublicOutputSafety.php';

uses(CreatesAdminUser::class);

it('loads resources for every public showcase member and preserves standalone lazy activation', function (string $fixture, bool $signedIn): void {
    foreach (array_keys(WidgetScreenshotFixtures::states()) as $widget) {
        $package = 'widget-' . $widget;
        CapellCore::forcePackageInstalled('capell-app/' . $package);
        $manifest = capell_json_file_array(dirname(__DIR__, 4) . '/packages/' . $package . '/composer.json');
        $providers = data_get($manifest, 'extra.laravel.providers', []);
        $this->assertIsArray($providers);
        foreach ($providers as $provider) {
            $this->assertIsString($provider);
            $this->app->register($provider);
        }
    }

    $site = Site::factory()->createOne();
    SiteDomain::query()->updateOrCreate(
        ['site_id' => $site->id, 'domain' => 'localhost', 'path' => null],
        ['scheme' => 'http', 'language_id' => $site->language_id, 'status' => true, 'default' => true],
    );
    $page = Page::factory()->site($site)
        ->state(['content_structure_override' => ContentStructure::Blocks->value])
        ->withTranslations($site->language, [
            'title' => 'Studio showcase', 'content' => WidgetScreenshotFixtures::blocks($fixture),
        ], slug: '/widget-showcase/showcase', contentStructure: ContentStructure::Blocks)
        ->createOne();
    $blueprint = $page->blueprint;
    $blueprint->forceFill(['meta' => [...($blueprint->meta ?? []), 'content_structure' => ContentStructure::Blocks->value]])->save();

    if ($signedIn) {
        $this->actingAsUser();
    }

    $response = resolve(Kernel::class)->handle(Request::create('/widget-showcase/showcase'));
    expect($response->getStatusCode())->toBe(200);
    $html = $response->getContent();
    expect($html)->toBeString();
    $document = HTMLDocument::createFromString(is_string($html) ? $html : '', LIBXML_NOERROR);

    $hotspots = $document->querySelector('.capell-hotspots');
    expect($hotspots)->toBeInstanceOf(Element::class)
        ->and($hotspots?->querySelectorAll('details > summary'))->toHaveCount(2);
    $publicId = LayoutBuilderLayoutWidgetResourceUsageContributor::resourceGroupPublicId('capell-app.widget-hotspots');
    $runtime = $document->querySelector("[data-capell-widget-resources*='" . $publicId . "']");
    $this->assertInstanceOf(Element::class, $runtime);
    expect($runtime->querySelector('.capell-hotspots'))->toBe($hotspots);
    $settings = json_decode((string) $runtime->getAttribute('data-capell-widget-settings'), true, flags: JSON_THROW_ON_ERROR);
    expect($settings)->toMatchArray(['loading_strategy' => $fixture === 'showcase' ? 'eager' : 'visible']);

    $manifest = $document->querySelector('script[type="application/json"][data-capell-widget-assets]');
    $activations = $manifest instanceof Element ? json_decode((string) $manifest->textContent, true, flags: JSON_THROW_ON_ERROR) : [];
    $this->assertIsArray($activations);
    if ($fixture === 'showcase') {
        expect($activations)->toBe([]);
        foreach ($document->querySelectorAll('[data-capell-widget-runtime]') as $member) {
            $this->assertInstanceOf(Element::class, $member);
            $presentation = json_decode((string) $member->getAttribute('data-capell-widget-settings'), true, flags: JSON_THROW_ON_ERROR);
            expect($presentation)->toMatchArray(['loading_strategy' => 'eager']);
        }

        foreach (array_keys(WidgetScreenshotFixtures::states()) as $widget) {
            $base = url('/vendor/capell-widget-' . $widget . '/widget');
            expect($document->head?->querySelectorAll('link[rel="stylesheet"][href="' . $base . '.css"]'))->toHaveCount(1)
                ->and($document->querySelectorAll('script[src="' . $base . '.js"]'))->toHaveCount(1);
        }
    } else {
        expect($document->head?->querySelectorAll('link[rel="stylesheet"][href="' . url('/vendor/capell-widget-hotspots/widget.css') . '"]'))->toHaveCount(0)
            ->and($activations)->toHaveCount(1);
        $activation = $activations[0] ?? null;
        $this->assertIsArray($activation);
        expect($activation['target'] ?? null)->toBe($publicId)
            ->and($activation['loading'] ?? null)->toBe('visible')
            ->and(data_get($activation, 'layers.0.*.url'))->toBe([
                url('/vendor/capell-widget-hotspots/widget.css'),
                url('/vendor/capell-widget-hotspots/widget.js'),
            ]);
    }

    expect($document->saveHtml($runtime))->not->toContain('__capell', 'capell-app.hotspots', 'instance_id', 'state_version', 'permissions', 'selector', 'field_path');
    assertCapellPublicOutputIsSafe($document->saveHtml(), 'Public widget fixture');
})->with([
    'showcase anonymous' => ['showcase', false],
    'showcase non-admin' => ['showcase', true],
    'standalone anonymous' => ['hotspots', false],
    'standalone non-admin' => ['hotspots', true],
]);
