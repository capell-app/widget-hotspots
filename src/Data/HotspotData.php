<?php

declare(strict_types=1);

namespace Capell\WidgetHotspots\Data;

use Spatie\LaravelData\Data;

final class HotspotData extends Data
{
    public function __construct(public string $label, public string $detail, public float $x, public float $y, public ?string $targetInstanceId = null, public string $behavior = 'modal') {}

    /** @return array<string, list<string>> */
    public static function rules(): array
    {
        return ['label' => ['required', 'string', 'max:80'], 'detail' => ['required', 'string', 'max:1000'], 'x' => ['required', 'numeric', 'between:0,100'], 'y' => ['required', 'numeric', 'between:0,100'], 'targetInstanceId' => ['nullable', 'uuid'], 'behavior' => ['required', 'in:modal,slide_over,inline_reveal,replace_region']];
    }
}
