<?php

declare(strict_types=1);

namespace FilamentFaker\Fakers;

use Closure;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Resource as FilamentResource;
use FilamentFaker\Contracts\Fakers\FakesResources;
use FilamentFaker\Support\Livewire;
use Illuminate\Database\Eloquent\Model;

class ResourceFaker extends FilamentFaker implements FakesResources
{
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
    public function withForm(string|Form $form = 'form'): static
    {
        return tap($this, function () use ($form) {
            if ($form instanceof Form) {
                $this->form = $form;

                return;
            }

            $this->form = rescue(
                callback: fn () => $this->resource::$form($this->baseForm()),
                rescue: fn () => $this->resolveResource()?->{$form}($this->baseForm())
            );
        });
    }

    /**
     * {@inheritDoc}
     */
    public function fake(): array
    {
        $form = $this->getFormFaker($this->getForm());

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
     * @return class-string<Model>|null|string
     */
    protected function resolveModel(): ?string
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
