<?php

namespace FilamentFaker\Concerns;

use Closure;

trait MutatesFakes
{
    protected ?Closure $mutateCallback = null;

    public function mutateFake(Closure $callback): static
    {
        return tap($this, function () use ($callback) {
            $this->mutateCallback = $callback;
        });
    }

    protected function hasMutations(): bool
    {
        return ! is_null($this->mutateCallback);
    }
}
