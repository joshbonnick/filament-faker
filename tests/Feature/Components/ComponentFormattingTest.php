<?php

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use FilamentFaker\Tests\TestSupport\Resources\PostResource;

it('respects formatStateUsing', function () {
    $component = TextInput::make('email')->formatStateUsing(fn (string $state) => str($state)->wrap('<b>')->toString());

    expect($component->faker()->fake())
        ->toBeString()
        ->toContain('<b>');
});

it('returns a formatted date', function () {
    $datepicker = PostResource::faker()->getForm()->schema([
        DateTimePicker::make('created_at')
            ->format('jS F Y'),

        DatePicker::make('published_at')
            ->format('jS F Y'),
    ]);

    [$published_at, $created_at] = array_values($datepicker->fake());

    expect($carbon = Carbon::parse($published_at))
        ->not
        ->toThrow(InvalidFormatException::class)
        ->and($carbon->isValid())
        ->toBeTrue()
        ->and($published_at)
        ->toEqual(now()->format('jS F Y'))
        ->and($carbon = Carbon::parse($created_at))
        ->not
        ->toThrow(InvalidFormatException::class)
        ->and($carbon->isValid())
        ->toBeTrue()
        ->and($created_at)
        ->toEqual(now()->format('jS F Y'));
});
