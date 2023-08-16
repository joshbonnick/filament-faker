<?php

declare(strict_types=1);

namespace FilamentFaker;

use Filament\Forms\Components\Field;
use FilamentFaker\Concerns\GeneratesFakesFromComponentName;
use FilamentFaker\Concerns\InteractsWithFakeConfig;
use FilamentFaker\Contracts\FakesComponents;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class ComponentFaker implements FakesComponents
{
    use GeneratesFakesFromComponentName;
    use InteractsWithFakeConfig;

    protected Field $component;

    public function __construct()
    {
        $this->setUpConfig();
    }

    public function fake(Field $component): mixed
    {
        $this->component = $component;

        return $this->fakeComponentContent($this->component);
    }

    protected function fakeComponentContent(Field $component): mixed
    {
        if (method_exists($component, 'mutateFake')) {
            return $component->mutateFake($component);
        }

        if ($this->shouldFakeUsingComponentName($component) && ! method_exists($component, 'getOptions')) {
            try {
                $content = $this->fakeUsingComponentName($component);

                if (! is_null($content)) {
                    return $content;
                }
            } catch (InvalidArgumentException $e) {
            }
        }

        if (Arr::has($this->fakesConfig, $component::class)) {
            return $this->fakesConfig[$component::class]($component);
        }

        return $this->fakesConfig['default']($component);
    }
}
