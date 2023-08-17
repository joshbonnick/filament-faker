<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TagsInput;

interface FakerProvider
{
    public function withOptions(Field $component): mixed;

    public function defaultCallback(Field $component): string;

    public function withSuggestions(TagsInput $component): array;

    public function date(): string;

    public function file(FileUpload $upload): string;

    public function keyValue(KeyValue $component): array;

    public function color(ColorPicker $color): string;

    public function html(): string;

    public function checkbox(): bool;
}
