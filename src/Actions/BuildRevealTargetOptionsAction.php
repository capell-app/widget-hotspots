<?php

declare(strict_types=1);

namespace Capell\WidgetHotspots\Actions;

use Capell\LayoutBuilder\Support\WidgetExtensions\WidgetExtensionStateWalker;
use Lorisleiva\Actions\Concerns\AsFake;
use Lorisleiva\Actions\Concerns\AsObject;

final class BuildRevealTargetOptionsAction
{
    use AsFake;
    use AsObject;

    public function __construct(private readonly WidgetExtensionStateWalker $walker) {}

    /**
     * @param  array<array-key, mixed>  $widgets
     * @return array<string, string>
     */
    public function handle(array $widgets, ?string $sourceId = null): array
    {
        $options = [];
        foreach ($this->walker->walk(array_values($widgets)) as $index => $target) {
            if ($target->instanceId === $sourceId) {
                continue;
            }

            $data = is_array($target->widget['data'] ?? null) ? $target->widget['data'] : [];
            $title = $data['heading'] ?? $data['title'] ?? $data['alt'] ?? null;
            $label = $target->definition->filamentWidget::make()->getLabel();
            $label = is_string($label) ? $label : strip_tags($label->toHtml());
            $options[$target->instanceId] = ($index + 1) . '. ' . $label
                . (is_string($title) && trim($title) !== '' ? ': ' . trim(strip_tags($title)) : '');
        }

        return $options;
    }
}
