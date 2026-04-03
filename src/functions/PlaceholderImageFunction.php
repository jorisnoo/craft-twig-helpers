<?php

namespace Noo\CraftTwigHelpers\functions;

class PlaceholderImageFunction
{
    public function __invoke(array $config): string
    {
        $width = $config['width'];
        $height = $config['height'];
        $color = $config['color'] ?? 'transparent';

        return 'data:image/svg+xml;charset=utf-8,'.rawurlencode(
            sprintf(
                '<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'%s\' height=\'%s\' style=\'background:%s\'/>',
                $width,
                $height,
                $color,
            )
        );
    }
}
