<?php

declare(strict_types=1);

namespace FilamentFaker\Fakers;

use Closure;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Resource as FilamentResource;
use FilamentFaker\Contracts\Fakers\FakesResources;
use FilamentFaker\Support\Livewire;

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

    public function fake(): array
    {
        if (! ($resource = $this->resolveResource()) instanceof FilamentResource) {
            return $this->getFormFaker($this->getForm())->fake();
        }

        if (method_exists($resource, 'mutateFake')) {
            $mutationCallback = Closure::fromCallable([$resource, 'mutateFake']);
        } else {
            $mutationCallback = $this->mutateCallback;
        }

        return $this->getFormFaker($this->getForm())->mutateFake($mutationCallback)->fake();
    }

    public function getForm(): Form
    {
        return is_null($this->form)
            ? $this->withForm()->getForm()
            : $this->form;
    }

    protected function resolveResource(): ?FilamentResource
    {
        return rescue(fn (): FilamentResource => resolve($this->resource));
    }

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
     */
    protected function injectionParameters(): array
    {
        if (! ($resource = $this->resolveResource()) instanceof Resource) {
            return [];
        }

        return [FilamentResource::class => $resource, $resource::class => $resource];
    }
}
