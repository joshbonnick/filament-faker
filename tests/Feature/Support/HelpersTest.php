<?php

use function FilamentFaker\callOrReturn;

it('returns result of closure')
    ->expect(callOrReturn(fn () => '::test::'))
    ->toEqual('::test::');

it('returns value if not closure')
    ->expect(callOrReturn('::test::'))
    ->toEqual('::test::');

it('accepts params')
    ->expect(callOrReturn(fn ($a, $b, $c) => [$a, $b, $c], 1, 2, 3))
    ->toBeArray()
    ->toEqual([1, 2, 3]);
