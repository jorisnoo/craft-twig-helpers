# Changelog

All notable changes to this project will be documented in this file.

## [1.0.2](https://github.com/jorisnoo/craft-twig-helpers/releases/tag/1.0.2) (2026-07-16)

### Bug Fixes

- **trimEmptyParagraphs:** preserve Markup type through filter ([61d30ca](https://github.com/jorisnoo/craft-twig-helpers/commit/61d30ca2ac72282c27493c109d4fe46504c7e8b9))
## [1.0.1](https://github.com/jorisnoo/craft-twig-helpers/releases/tag/1.0.1) (2026-07-10)

### Bug Fixes

- **minify:** fall back to original string on preg errors ([b2384ff](https://github.com/jorisnoo/craft-twig-helpers/commit/b2384ff77b853897af8e7045a55fe9ba94d5e6df))
- **security:** use preserves_safety for trimEmptyParagraphs to prevent XSS ([d2429d6](https://github.com/jorisnoo/craft-twig-helpers/commit/d2429d690d743a54c759fcca30460263a61c9e65))

### Chores

- tidy composer.json metadata and add justfile ([1eedfb0](https://github.com/jorisnoo/craft-twig-helpers/commit/1eedfb016508ecb746ba4bc28635de11dcc6365e))
- **deps:** bump actions/checkout from 6 to 7 ([a2bee90](https://github.com/jorisnoo/craft-twig-helpers/commit/a2bee90c478c9c730601d4cbcefb2e5f657cf183))
## [1.0.0](https://github.com/jorisnoo/craft-twig-helpers/releases/tag/1.0.0) (2026-05-12)

### Features

- add {% minify %} Twig tag for HTML minification ([9e5c8f1](https://github.com/jorisnoo/craft-twig-helpers/commit/9e5c8f187f9c536006800096e1c663d33130adf2))

### Bug Fixes

- controller namespace ([a9e9086](https://github.com/jorisnoo/craft-twig-helpers/commit/a9e90866917d165ee0f163a4bcf16d824a19de63))

### Code Refactoring

- **minify:** use environment variable for dev mode detection ([1f8f865](https://github.com/jorisnoo/craft-twig-helpers/commit/1f8f865e70e0acb9a80345f557f921b85c1582b1))
- extract twig filters and functions into dedicated classes ([59009ef](https://github.com/jorisnoo/craft-twig-helpers/commit/59009efd442003a23a33b5df0eb0e3c9fd27988b))
- remove textSnippet and staticAsset functions ([91b5ae6](https://github.com/jorisnoo/craft-twig-helpers/commit/91b5ae6597fb9998480fcfc5827daead9f8fd963))
- rename vendor namespace from jorisnoo to noo ([7d1d4e8](https://github.com/jorisnoo/craft-twig-helpers/commit/7d1d4e8fa31f2819eac34c89a566a63e0ecf1406))

### Documentation

- add README with project documentation ([7e45079](https://github.com/jorisnoo/craft-twig-helpers/commit/7e45079818597976c14099b54e9cd79df7d7913a))

### Tests

- configure Pest and add TrimEmptyParagraphs test ([4574f32](https://github.com/jorisnoo/craft-twig-helpers/commit/4574f32d397a01622bc7efa866f87cb6e13bbcfc))

### Chores

- add project configuration, workflows, and license files ([1599c7e](https://github.com/jorisnoo/craft-twig-helpers/commit/1599c7ef0f23d319499c371fb647db6937d3cb8f))

### Reverts

- rename vendor namespace from jorisnoo to noo ([80d173e](https://github.com/jorisnoo/craft-twig-helpers/commit/80d173edab41500d8fecaea64e8268bf3c29a91e))
