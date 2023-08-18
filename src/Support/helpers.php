<?php

namespace FilamentFaker;

use Closure;

if (! function_exists('callOrReturn')) {
    /**
     * Execute a Closure or return provided data.
     *
     * @param mixed[] $args
     */
    function callOrReturn(mixed $data, ...$args): mixed
    {
        return $data instanceof Closure ? $data(...$args) : $data;
    }
}
