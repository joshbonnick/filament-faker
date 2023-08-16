<?php

declare(strict_types=1);

namespace FilamentFaker;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use FilamentFaker\Contracts\FakesBlocks;
use FilamentFaker\Contracts\FakesForms;

class FormFaker implements FakesForms
{
    protected Form $form;

    public function __construct(
        protected readonly FakesBlocks $blockFaker
    )
    {
    }

    /**
     * @param  class-string<resource>  $resource
     * @return array<string, mixed>
     */
    public function fake(string $resource, bool $withHidden = false): array
    {
        $this->form = $resource::form(Form::make(new EditRecord()));

        return collect($this->form->getComponents($withHidden))
            ->mapWithKeys(function (Component $component) {
                if ($component instanceof Builder) {
                    return [$component->getName() => $this->getContentForBuilder($component)];
                }

                return [$component->getName() => $this->getContentForComponent($component)];
            })
            ->toArray();
    }

    /**
     * @return array<string, mixed>
     */
    protected function getContentForBuilder(Builder $builder) : array{
        return collect($builder->getBlocks())
            ->map(fn (Block $block) => $this->blockFaker->fake($block))
            ->toArray();
    }

    protected function getContentForComponent(Field $component): mixed
    {
        if (method_exists($this->form, 'mutateFake')) {
            $content = $this->form->mutateFake($component);

            if (is_callable($content)) {
                $content = $content($component);
            }
        }

        return $content ?? $component->fake(); // @phpstan-ignore-line
    }
}
