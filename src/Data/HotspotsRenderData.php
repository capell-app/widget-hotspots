<?php

declare(strict_types=1);

namespace Capell\WidgetHotspots\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

final class HotspotsRenderData extends Data
{
    /** @param DataCollection<int, HotspotData> $hotspots */
    public function __construct(public string $image, public string $alt, #[DataCollectionOf(HotspotData::class)] public DataCollection $hotspots) {}
}
