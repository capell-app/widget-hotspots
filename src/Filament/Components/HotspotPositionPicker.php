<?php

declare(strict_types=1);

namespace Capell\WidgetHotspots\Filament\Components;

use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Override;

final class HotspotPositionPicker extends Field
{
    protected string $view = 'capell-widget-hotspots::forms.position-picker';

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->dehydrated(false);
    }

    public function imageUrl(): ?string
    {
        $image = (new Get($this))('../../image');
        if (is_array($image)) {
            $image = reset($image);
        }

        if ($image instanceof TemporaryUploadedFile) {
            return $image->temporaryUrl();
        }

        if (! is_string($image) || preg_match('/^(?![a-z][a-z0-9+.-]*:)(?!\/\/)[A-Za-z0-9_\/.-]+$/i', $image) !== 1) {
            return null;
        }

        return Storage::disk(config()->string('filament.default_filesystem_disk', 'public'))->url($image);
    }

    public function coordinateStatePath(string $coordinate): string
    {
        return $this->resolveRelativeStatePath($coordinate);
    }
}
