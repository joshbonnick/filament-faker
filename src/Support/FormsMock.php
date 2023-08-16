<?php

declare(strict_types=1);

namespace FilamentFaker\Support;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

/**
 * @codeCoverageIgnore
 */
final class FormsMock extends Component implements HasForms
{
    use InteractsWithForms;

    protected Form $form;

    /**
     * @var array<string, mixed>
     */
    public array $data;

    public static function make(): self
    {
        return new self();
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function data(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }
}
