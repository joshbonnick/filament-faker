# Changelog

All notable changes to `filament-faker` will be documented in this file.

## v0.0.2 - 2023-08-19

### What's Changed

- [1.x] Add `dev` and `testing` keywords to composer.json
- [1.x] Require `illuminate/support:^10.0` as dependency
- [1.x] Refactor tests directory structure
- [1.x] Abstraction of model resolves in `WithFactory` trait
- [1.x] Mark traits as internal
- [1.x] Add abstract functions to traits
- [1.x] Fix factory resolving from `Block`
- [1.x] Extract `FakeFromComponentName` to `RealTimeFactory` class
- [1.x] Add `ComponentDecorator`
- [1.x] Support dependency injection on `Closures`
- [1.x] Refactoring
- [1.x] Refactor default faker callbacks
- [1.x] Improve README documentation
- [1.x] Add doc blocks with descriptions of methods
- [1.x] Improve test coverage
- [1.x] Return an array if options component is multiselect
- [1.x] Add `Eloquent Factory` support
- [1.x] Fix `DatePicker` component fake
- [1.x] Support date formatting on `DateTimePicker` and `DatePicker`
- [1.x] Add chained mutations
- [1.x] Support `mutateFake` method added using macro

**Full Changelog**: https://github.com/joshbonnick/filament-faker/compare/v0.0.1...v0.0.2

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
