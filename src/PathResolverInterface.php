<?php

/*
 * This file is part of the puli/twig-puli-extension package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Puli\Extension\Twig;

/**
 * Returns a Puli path depending on a current directory.
 *
 * @since  1.0
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
interface PathResolverInterface
{
    /**
     * Resolves a path depending on the current directory.
     *
     * If the path is a relative path, the Puli repository is checked for that
     * path in the current directory. If that path exists, it is returned.
     * Otherwise, the path is returned unchanged.
     *
     * If the path is an absolute path, it is returned unchanged.
     *
     * @param string $path       A path.
     * @param string $currentDir The Puli directory path of the current file.
     *
     * @return string The resolved path.
     */
    public function resolvePath($path, $currentDir);
}
