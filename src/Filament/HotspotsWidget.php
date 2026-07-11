<?php

declare(strict_types=1);

namespace Capell\WidgetHotspots\Filament;

use Capell\Admin\Contracts\Widgets\FilamentWidget;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

final class HotspotsWidget implements FilamentWidget
{
    public static function getWidgetName(): string
    {
        return 'capell-app.hotspots';
    }

    public static function make(): Block
    {
        return Block::make(self::getWidgetName())->label(__('capell-widget-hotspots::widget.label'))->schema([FileUpload::make('image')->image()->required(), TextInput::make('alt')->required()->maxLength(180), Repeater::make('hotspots')->reorderable()->minItems(1)->maxItems(30)->schema([TextInput::make('label')->required()->maxLength(80), Textarea::make('detail')->required()->maxLength(1000), TextInput::make('x')->numeric()->minValue(0)->maxValue(100)->required(), TextInput::make('y')->numeric()->minValue(0)->maxValue(100)->required(), TextInput::make('targetInstanceId')->uuid()])]);
    }
}
