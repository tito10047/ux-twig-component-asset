# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-08-12

### Changed

- **Breaking Change**: `#[AsSdcComponent]` no longer extends `#[AsTwigComponent]`. It is now a standalone, repeatable attribute used alongside `#[AsTwigComponent]` (or `#[AsLiveComponent]`). It marks a class as an SDC component and enables auto-discovery of sibling `.css`, `.js`, and `.html.twig` files. Optionally accepts an explicit `path`/`type`/`priority`/`attributes` to inject a specific asset.
- **Breaking Change**: The `#[Asset]` attribute has been renamed to `#[SdcAsset]`. Its constructor signature (`path`, `type`, `priority`, `attributes`) is unchanged. Update all `use Tito10047\UX\Sdc\Attribute\Asset` imports to `SdcAsset`.

### Migration Guide

**Before (≤ 0.x):**
```php
use Tito10047\UX\Sdc\Attribute\AsSdcComponent;
use Tito10047\UX\Sdc\Attribute\Asset;

#[AsSdcComponent('Alert', css: 'Alert.css', js: 'Alert.js')]
class Alert {}

#[AsTwigComponent('Search')]
#[Asset(path: 'Search.css')]
class Search {}
```

**After (1.0):**
```php
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Tito10047\UX\Sdc\Attribute\AsSdcComponent;
use Tito10047\UX\Sdc\Attribute\SdcAsset;

// Auto-discovery (sibling CSS/JS/Twig discovered automatically):
#[AsTwigComponent]
#[AsSdcComponent]
class Alert {}

// Explicit assets via SdcAsset:
#[AsTwigComponent('Search')]
#[SdcAsset(path: 'Search.css')]
class Search {}
```

## [0.3.0] - 2026-03-25

### Changed

- **Breaking Change**: Renamed `UxSdcBundle` to `SdcBundle` to follow official Symfony bundle naming conventions and reflect the updated bundle name `Tito10047\UX\Sdc\SdcBundle`.
