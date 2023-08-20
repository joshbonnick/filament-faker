<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

/**
 * @internal
 */
trait CanSpecifyFields
{
    /** @var string[] */
    protected array $onlyFields = [];

    /**
     * {@inheritDoc}
     */
    public function onlyFields(string ...$fields): static
    {
        return tap($this, function () use ($fields) {
            $this->onlyFields = $fields;
        });
    }

    /**
     * @return string[]
     */
    protected function getOnlyFields(): array
    {
        return $this->onlyFields;
    }
}
