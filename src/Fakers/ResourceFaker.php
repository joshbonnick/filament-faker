<?php

declare(strict_types=1);

namespace FilamentFaker\Fakers;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use FilamentFaker\Contracts\FakesResources;
use FilamentFaker\Support\FormsMock;

class ResourceFaker extends GeneratesFakes implements FakesResources
{
    /**
     * @var class-string<resource>
     */
    protected readonly string $resource;

    protected ?Form $form = null;

    /**
     * @param  class-string<resource>  $resource
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
                rescue: fn () => resolve($this->resource)->{$form}($this->baseForm())
            );
        });
    }

    public function fake(): array
    {
        return $this->getFormFaker($this->getForm())->fake();
    }

    public function getForm(): Form
    {
        return is_null($this->form)
            ? $this->withForm()->getForm()
            : $this->form;
    }

    protected function baseForm(): Form
    {
        return Form::make(FormsMock::make());
    }
}
