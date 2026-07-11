<?php

declare(strict_types=1);

namespace Capell\WidgetHotspots\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

final class HotspotsInputData extends Data
{
    /** @param DataCollection<int, HotspotData> $hotspots */
    public function __construct(public string $image, public string $alt, #[DataCollectionOf(HotspotData::class)] public DataCollection $hotspots) {}

    /** @return array<string, list<string>> */
    public static function rules(): array
    {
        return ['image' => ['required', 'string', 'max:2048', 'regex:/^(?![a-z][a-z0-9+.-]*:)(?!\/\/)[A-Za-z0-9_\/.-]+$/i'], 'alt' => ['required', 'string', 'max:180'], 'hotspots' => ['required', 'array', 'min:1', 'max:30']];
    }
}
