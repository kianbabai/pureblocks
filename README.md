# PureBlocks

PureBlocks is a clean Gutenberg block plugin.

## Blocks

- **Carousel (`pureblocks/carousel`)**
  - Uses Slick for sliding.
  - Attributes:
    - `slidesToShow` (number, default `1`)
  - Features:
    - InnerBlocks (images/heading/paragraph) as slides
    - Minimal arrows/dots styling
    - Image-load aware init to prevent layout jumps

## Requirements
- WordPress 6.0+
- Node 18+ (for builds)

## Development

From the plugin directory:

```bash
npm install
npm start   # watches all blocks, outputs to build/
```

Build for release:

```bash
npm run build
```

## Production usage
- Ensure `build/` is present (run `npm run build` before packaging).
- Upload the plugin or zip the folder (excluding `node_modules/`).

## Notes
- Slick assets are bundled via `slick-carousel` and enqueued with the carousel block.
- Block category: `PureBlocks`.


