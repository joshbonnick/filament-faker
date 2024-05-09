<?php

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use FilamentFaker\Contracts\Decorators\ComponentDecorator;
use FilamentFaker\Support\Livewire;
use FilamentFaker\Tests\Feature\Fixtures\InjectableService;

beforeEach(function () {
    $this->componentDecorator = tap(resolve(ComponentDecorator::class))->uses(TextInput::make('test')->container(ComponentContainer::make(Livewire::make())));
});

it('returns an instance of component', function () {
    expect($this->componentDecorator->getField())->toBeInstanceOf(Field::class);
});

test('__get function returns a property', function () {
    $mock = mock(TextInput::class)->makePartial();
    $mock->shouldReceive('make')->andReturnSelf()->set('foobar', 'foo');

    $decorator = tap(resolve(ComponentDecorator::class))->uses($mock::make('test'));

    expect($decorator->foobar)->toBeString()->toEqual('foo');
});

test('is_a method with array and string', function () {
    expect($this->componentDecorator->is_a(Field::class))
        ->toBeTrue()
        ->and($this->componentDecorator->is_a(TextInput::class, RichEditor::class))
        ->toBeTrue();
});

it('returns a searchable option', function () {
    $searchCallback = function (InjectableService $service) {
        return $service->search();
    };

    $select = Select::make('some_option')
        ->searchable()
        ->getSearchResultsUsing($searchCallback)
        ->container(ComponentContainer::make(Livewire::make()));
    /** @var ComponentDecorator $decorator */
    $decorator = tap(resolve(ComponentDecorator::class))->uses($select);

    expect($decorator->getSearch())->toEqual(app()->call($searchCallback));
});
