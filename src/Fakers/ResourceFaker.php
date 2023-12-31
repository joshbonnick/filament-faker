<?php

declare(strict_types=1);

namespace FilamentFaker\Fakers;

use Closure;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Resource as FilamentResource;
use FilamentFaker\Concerns\HasChildComponents;
use FilamentFaker\Concerns\InteractsWithFactories;
use FilamentFaker\Concerns\InteractsWithFilamentContainer;
use FilamentFaker\Concerns\ResolvesClosures;
use FilamentFaker\Concerns\TransformsFakes;
use FilamentFaker\Contracts\Fakers\FakesResources;
use FilamentFaker\Contracts\Fakers\FilamentFaker;
use FilamentFaker\Support\Livewire;
use Stringable;

class ResourceFaker implements FakesResources, FilamentFaker
{
    use InteractsWithFilamentContainer;
    use InteractsWithFactories;
    use TransformsFakes;
    use HasChildComponents;
    use ResolvesClosures;

    /**
     * @var class-string<FilamentResource>
     */
    protected readonly string $resource;

    protected ?Form $form = null;

    /**
     * @param  class-string<FilamentResource>  $resource
     */
    public function __construct(string $resource)
    {
        $this->resource = $resource;
    }

    /**
     * {@inheritDoc}
     */
    public function withForm(string|Stringable|Form $form = 'form'): static
    {
        return tap($this, function () use ($form) {
            if ($form instanceof Form) {
                $this->form = $form;

                return;
            }

            $this->form = rescue(
                callback: fn () => $this->resource::{ (string) $form}($this->baseForm()),
                rescue: fn () => $this->resolveResource()?->{ (string) $form}($this->baseForm())
            );
        });
    }

    /**
     * {@inheritDoc}
     */
    public function fake(): array
    {
        $form = $this->faker($this->getForm());

        if (! ($resource = $this->resolveResource()) instanceof FilamentResource) {
            return $form->fake();
        }

        return $form
            ->mutateFake(method_exists($resource, 'mutateFake')
                ? Closure::fromCallable([$resource, 'mutateFake'])
                : $this->mutateCallback
            )->fake();
    }

    public function getForm(): Form
    {
        return $this->form ?? $this->withForm()->getForm();
    }

    protected function resolveResource(): ?FilamentResource
    {
        return rescue(fn (): FilamentResource => app($this->resource));
    }

    /**
     * {@inheritDoc}
     */
    public function resolveModel(): string
    {
        return $this->resource::getModel();
    }

    protected function baseForm(): Form
    {
        return Form::make(Livewire::make());
    }

    /**
     * @return array<class-string|string, object>
     *
     * @codeCoverageIgnore
     */
    protected function injectionParameters(): array
    {
        if (! ($resource = $this->resolveResource()) instanceof Resource) {
            return [];
        }

        return [FilamentResource::class => $resource, $resource::class => $resource];
    }
}
