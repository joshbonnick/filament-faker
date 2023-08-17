<?php

declare(strict_types=1);

namespace FilamentFaker\Fakers;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use FilamentFaker\Concerns\GeneratesFakes;
use FilamentFaker\Concerns\InteractsWithFactories;
use FilamentFaker\Contracts\FakesForms;
use FilamentFaker\Contracts\FilamentFaker;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class FormFaker extends GeneratesFakes implements FakesForms, FilamentFaker
{
    use InteractsWithFactories;

    protected bool $withHidden = true;

    public function __construct(
        protected Form $form,
    ) {
        parent::__construct();
    }

    /**
     * @return array<string, mixed>
     */
    public function fake(): array
    {
        return $this->fakeComponents(collect($this->form->getComponents($this->withHidden)));
    }

    public function withoutHidden(bool $withoutHidden = true): static
    {
        return tap($this, function () use ($withoutHidden) {
            $this->withHidden = ! $withoutHidden;
        });
    }

    /**
     * @param  Collection<int, Component>  $components
     * @return array<string, mixed>
     */
    protected function fakeComponents(Collection $components): array
    {
        return $components
            ->reject(fn (Component $component) => $component instanceof Placeholder)
            ->mapWithKeys(fn (Component $component) => match (true) {
                $component instanceof Builder => [$component->getName() => $this->getContentForBuilder($component)],

                $component instanceof Grid,
                $component instanceof Fieldset,
                $component instanceof Tabs,
                $component instanceof Wizard,
                $component instanceof Wizard\Step,
                $component instanceof Group,
                $component instanceof Section => $this->fakeComponents(collect($component->getChildComponents())),

                $component instanceof Field => [$component->getName() => $this->getContentForComponent($component)],

                default => throw new InvalidArgumentException(
                    sprintf('%s is not a supported component type.', $component::class)
                ),
            })->toArray();
    }

    /**
     * @return array<string, mixed>
     */
    protected function getContentForBuilder(Builder $builder): array
    {
        return collect($builder->getChildComponents())
            ->filter(fn (Component $block) => $block instanceof Block)
            ->map(fn (Block $block) => $this->getBlockFaker($block)->fake())
            ->toArray();
    }

    protected function getContentForComponent(Field $component): mixed
    {
        if (! ($content = $this->mutate($this->form, $component)) instanceof Field) {
            return $content;
        }

        return $this->getComponentFaker($content)->fake();
    }
}
