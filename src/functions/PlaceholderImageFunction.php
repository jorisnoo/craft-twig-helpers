<?php

namespace Noo\CraftTwigHelpers\functions;

use InvalidArgumentException;

class PlaceholderImageFunction
{
    public function __invoke(array $config): string
    {
        foreach (['width', 'height'] as $dimension) {
            $value = $config[$dimension] ?? null;
            if (! is_numeric($value) || ! is_finite((float) $value) || (float) $value <= 0) {
                throw new InvalidArgumentException("placeholderImage requires a finite, positive $dimension.");
            }
        }
        $color = $config['color'] ?? 'transparent';
        if (! is_string($color)) {
            throw new InvalidArgumentException('placeholderImage color must be a CSS color string.');
        }

        return 'data:image/svg+xml;charset=utf-8,'.rawurlencode(
            sprintf(
                '<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'%s\' height=\'%s\' style=\'background:%s\'/>',
                trim((string) $config['width']),
                trim((string) $config['height']),
                htmlspecialchars($color, ENT_QUOTES | ENT_XML1 | ENT_SUBSTITUTE | ENT_DISALLOWED, 'UTF-8'),
            )
        );
    }
}
