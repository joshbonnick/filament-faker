<?php

declare(strict_types=1);

namespace FilamentFaker;

use Closure;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use FilamentFaker\Contracts\FakesBlocks;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use FilamentFaker\Concerns\GeneratesFakeFromComponentName;

class BlockFaker implements FakesBlocks
{
    use GeneratesFakeFromComponentName;

    protected Block $block;

    /**
     * @var array<string|class-string<Field>, Closure>
     */
    protected readonly array $fakesConfig;

    public function __construct()
    {
        $this->fakesConfig = config('filament-faker.fakes', []);
    }

    /**
     * {@inheritDoc}
     */
    public function fake(string $block, string $name): array
    {
        $this->block = $block::make($name);

        return [
            'type' => $this->block::class,
            'data' => collect($this->block->getChildComponents())
                ->filter(fn (Component $component) => $component instanceof Field)
                ->mapWithKeys(fn (Field $component) => [$component->getName() => $this->getComponentContent($component)])
                ->toArray(),
        ];
    }

    protected function getComponentContent(Field $component): mixed
    {
        if (is_callable($callback = $this->mutateFake($component))) {
            return $callback($component);
        }

        if ($this->shouldFakeUsingComponentName($component) && ! method_exists($component, 'getOptions')) {
            try {
                return $this->fakeUsingComponentName($component);
            } catch (InvalidArgumentException $e) {
            }
        }

        if (Arr::has($this->fakesConfig, $component::class)) {
            return $this->fakesConfig[$component::class]($component);
        }

        return $this->fakesConfig['default']($component);
    }

    /**
     * @return null|Closure(Field $component): mixed
     */
    protected function mutateFake(Field $component): ?Closure
    {
        return method_exists($this->block, 'mutateFake')
            ? $this->block->mutateFake($component)
            : null;
    }
}
