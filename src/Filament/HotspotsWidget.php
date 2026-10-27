<?php

declare(strict_types=1);

namespace Capell\WidgetHotspots\Filament;

use Capell\Admin\Actions\Widgets\NormalizeContentWidgetStateAction;
use Capell\Admin\Contracts\Widgets\FilamentWidget;
use Capell\Admin\Filament\Components\Forms\Editor\ContentBuilder;
use Capell\LayoutBuilder\Filament\Components\Forms\WidgetFileUpload;
use Capell\WidgetHotspots\Actions\BuildRevealTargetOptionsAction;
use Capell\WidgetHotspots\Filament\Components\HotspotPositionPicker;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Override;

final class HotspotsWidget implements FilamentWidget
{
    #[Override]
    public static function getWidgetName(): string
    {
        return 'capell-app.hotspots';
    }

    #[Override]
    public static function make(): Block
    {
        return Block::make(self::getWidgetName())->label(__('capell-widget-hotspots::widget.label'))->schema([
            WidgetFileUpload::make('image')->image()->live()->required(),
            TextInput::make('alt')->required()->maxLength(180),
            Repeater::make('hotspots')->reorderable()->minItems(1)->maxItems(30)->schema([
                TextInput::make('label')->required()->maxLength(80),
                Textarea::make('detail')->required()->maxLength(1000),
                HotspotPositionPicker::make('position')->label(__('capell-widget-hotspots::widget.position')),
                TextInput::make('x')->label(__('capell-widget-hotspots::widget.x'))->live()->default(50)->numeric()->minValue(0)->maxValue(100)->required(),
                TextInput::make('y')->label(__('capell-widget-hotspots::widget.y'))->live()->default(50)->numeric()->minValue(0)->maxValue(100)->required(),
                self::targetSelect(),
            ]),
        ]);
    }

    private static function targetSelect(): Select
    {
        return Select::make('targetInstanceId')
            ->label(__('capell-widget-hotspots::widget.target_label'))
            ->searchable()
            ->live()
            ->options(fn (Select $component, Get $get): array => self::targetOptions($component, $get))
            ->helperText(fn (Select $component, Get $get, mixed $state): string => is_string($state) && $state !== '' && ! array_key_exists($state, self::targetOptions($component, $get))
                    ? __('capell-widget-hotspots::widget.target_missing')
                    : __('capell-widget-hotspots::widget.target_help'));
    }

    /** @return array<string, string> */
    private static function targetOptions(Select $component, Get $get): array
    {
        $schema = $component->getContainer();
        while (($parent = $schema->getParentComponent()) instanceof Component) {
            if ($parent instanceof ContentBuilder) {
                $state = $parent->getRawState();
                // Filament adds blocks after hydration. Persist canonical identities
                // in live state before offering targets or validating the first save.
                $state = NormalizeContentWidgetStateAction::run(is_array($state) ? $state : []);
                $parent->rawState($state);
                $sourceId = $get('../../__capell.instance_id');

                return BuildRevealTargetOptionsAction::run(
                    $state,
                    is_string($sourceId) ? $sourceId : null,
                );
            }

            $schema = $parent->getContainer();
        }

        return [];
    }
}
