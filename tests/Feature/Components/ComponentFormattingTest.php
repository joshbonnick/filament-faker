<?php

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Filament\Forms\Components\DatePicker;
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
        DatePicker::make('published_at')
            ->label('Published Date')
            ->nullable()
            ->default('31-12-2000')
            ->format('Y-m-d'),
    ]);

    $date = $datepicker->fake()['published_at'];

    expect($carbon = Carbon::parse($date))
        ->not
        ->toThrow(InvalidFormatException::class)
        ->and($carbon->isValid())
        ->toBeTrue()
        ->and($date)
        ->toEqual('2000-12-31');
});
