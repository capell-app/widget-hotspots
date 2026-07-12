@php /** @var \Capell\WidgetHotspots\Data\HotspotsRenderData $renderData */ $pageData=$publicPageRenderData??null; @endphp
<figure class="capell-hotspots">
    <img
        src="{{ asset('storage/' . ltrim($renderData->image, '/')) }}"
        alt="{{ $renderData->alt }}"
    />
    <div class="capell-hotspots__points">
        @foreach ($renderData->hotspots as $hotspot)
            @php $locator=$pageData?->widgetInteractionLocator($hotspot->targetInstanceId??''); @endphp
            <details
                style="--hotspot-x:{{ $hotspot->x }}%;--hotspot-y:{{ $hotspot->y }}%"
            >
                <summary aria-label="{{ $hotspot->label }}">
                    <span>{{ $loop->iteration }}</span>
                </summary>
                <div>
                    <strong>{{ $hotspot->label }}</strong>
                    <p>{{ $hotspot->detail }}</p>
                    @if ($locator)
                        <a
                            href="{{ $locator }}"
                            data-capell-interaction
                            data-capell-interaction-event="click"
                            data-capell-interaction-behavior="{{ $hotspot->behavior }}"
                            data-capell-interaction-target-url="{{ $locator }}"
                            data-capell-interaction-target-type="widget"
                            >{{ __('capell-widget-hotspots::widget.open_details') }}</a
                        >
                    @endif
                </div>
            </details>
        @endforeach
    </div>
</figure>
