# Changelog

All notable changes to `filament-faker` will be documented in this file.

## Pre-release - 2023-08-16

### What's Changed

- [1.x] Add support for multiple forms in a resource
- [1.x] Add global disabling of faking using method names
- [1.x] Ignore slow faker methods
- [1.x] Fix dependency injection callbacks evaluated by Filament
- [1.x] Add support for faking forms from the resource reference with `formFake` macro
- [1.x] Add support for faking `Forms`
- [1.x] Bind `fake` method as a macro to `Blocks` and `Forms`
- [1.x] Value from suggestions array is always returned
- [1.x] An option value should always be returned
- [1.x] Test method priority
- [1.x] RichEditor default fake returns HTML
- [1.x] Improved readability of config file
- [1.x] Attempt to use faker method based on component names, e.g. `email` returns `fake()->email()`
- [1.x] `mutateFake` method returns a `Closure`
- [1.x] `mutateFake` is no longer static
- [1.x] Added `make` & `getChildComponents` as abstract functions of `GeneratesFakes`
- [1.x] Improve DocBlocks for static analysis
- [1.x] Bump PHPStan Level
- [1.x] Run tests on pushes to release branch
- [1.x] Fake Specific Components
