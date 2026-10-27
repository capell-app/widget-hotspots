<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @if ($imageUrl())
        <div
            x-data="{
                x: $wire.$entangle(@js($coordinateStatePath('x'))),
                y: $wire.$entangle(@js($coordinateStatePath('y'))),
                clamp(value) { return Math.round(Math.min(100, Math.max(0, value)) * 100) / 100 },
                place(event) {
                    const bounds = this.$refs.surface.getBoundingClientRect()
                    if (!bounds.width || !bounds.height) return
                    this.x = this.clamp((event.clientX - bounds.left) / bounds.width * 100)
                    this.y = this.clamp((event.clientY - bounds.top) / bounds.height * 100)
                },
                move(event) {
                    const step = event.shiftKey ? 10 : 1
                    if (!['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'].includes(event.key)) return
                    event.preventDefault()
                    if (event.key === 'ArrowLeft') this.x = this.clamp(Number(this.x ?? 50) - step)
                    if (event.key === 'ArrowRight') this.x = this.clamp(Number(this.x ?? 50) + step)
                    if (event.key === 'ArrowUp') this.y = this.clamp(Number(this.y ?? 50) - step)
                    if (event.key === 'ArrowDown') this.y = this.clamp(Number(this.y ?? 50) + step)
                }
            }"
        >
            <p
                id="{{ $getId() }}-help"
            >{{ __('capell-widget-hotspots::widget.position_help') }}</p>
            <div
                x-ref="surface"
                style="position: relative; width: 100%; touch-action: none;"
                x-on:pointerdown.prevent="place($event); $event.currentTarget.setPointerCapture($event.pointerId)"
                x-on:pointermove="if ($event.buttons === 1) place($event)"
            >
                <img
                    src="{{ $imageUrl() }}"
                    alt="{{ __('capell-widget-hotspots::widget.position_image') }}"
                    style="display: block; width: 100%; height: auto;"
                    draggable="false"
                />
                <button
                    type="button"
                    aria-describedby="{{ $getId() }}-help"
                    aria-label="{{ __('capell-widget-hotspots::widget.position_point') }}"
                    x-on:keydown="move($event)"
                    x-bind:style="{left: (x ?? 50) + '%', top: (y ?? 50) + '%'}"
                    style="position: absolute; transform: translate(-50%, -50%); min-width: 44px; min-height: 44px; border-radius: 50%; border: 2px solid white; background: #1d4ed8; color: white;"
                >
                    +
                </button>
            </div>
        </div>
    @else
        <p>{{ __('capell-widget-hotspots::widget.position_upload') }}</p>
    @endif
</x-dynamic-component>
