<?php

declare(strict_types=1);

namespace FilamentFaker;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use FilamentFaker\Contracts\FakesBlocks;
use FilamentFaker\Contracts\FakesForms;
use InvalidArgumentException;

class FormFaker implements FakesForms
{
    protected Form $form;

    public function __construct(
        protected readonly FakesBlocks $blockFaker
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function fake(Form $form, bool $withHidden = false): array
    {
        $this->form = $form;

        return collect($this->form->getComponents($withHidden))
            ->mapWithKeys(function (Component $component) {
                if ($component instanceof Builder) {
                    return [$component->getName() => $this->getContentForBuilder($component)];
                }

                if (! $component instanceof Field) {
                    throw new InvalidArgumentException(
                        sprintf('%s is not a supported component type.', $component::class));
                }

                return [$component->getName() => $this->getContentForComponent($component)];
            })
            ->toArray();
    }

    /**
     * @return array<string, mixed>
     */
    protected function getContentForBuilder(Builder $builder): array
    {
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
