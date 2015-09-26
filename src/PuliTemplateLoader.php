<?php

/*
 * This file is part of the puli/twig-puli-extension package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Puli\TwigExtension;

use InvalidArgumentException;
use Puli\Repository\Api\Resource\BodyResource;
use Puli\Repository\Api\ResourceNotFoundException;
use Puli\Repository\Api\ResourceRepository;
use Twig_Error_Loader;
use Twig_ExistsLoaderInterface;
use Twig_LoaderInterface;

/**
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class PuliTemplateLoader implements Twig_LoaderInterface, Twig_ExistsLoaderInterface
{
    private $repo;

    public function __construct(ResourceRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Gets the source code of a template, given its name.
     *
     * @param string $path The name of the template to load
     *
     * @return string The template source code
     *
     * @throws Twig_Error_Loader When $path is not found
     */
    public function getSource($path)
    {
        try {
            $file = $this->repo->get($path);

            if (!$file instanceof BodyResource) {
                throw new Twig_Error_Loader(sprintf(
                    'Can only load file resources. Resource "%s" is of type %s.',
                    $path,
                    is_object($file) ? get_class($file) : gettype($file)
                ));
            }

            // The "loaded_by_puli" tag makes it possible to recognize node
            // trees of templates loaded through this loader. In this way, we
            // can turn relative Puli paths into absolute ones in those
            // templates. The "loaded_by_puli" tag is removed early on by the
            // PuliDirTagger visitor and does not appear in the final
            // output.
            return '{% loaded_by_puli %}'.$file->getBody();
        } catch (ResourceNotFoundException $e) {
            throw new Twig_Error_Loader($e->getMessage(), -1, null, $e);
        } catch (InvalidArgumentException $e) {
            throw new Twig_Error_Loader($e->getMessage(), -1, null, $e);
        }
    }

    /**
     * Gets the cache key to use for the cache for a given template name.
     *
     * @param string $path The name of the template to load
     *
     * @return string The cache key
     *
     * @throws Twig_Error_Loader When $path is not found
     */
    public function getCacheKey($path)
    {
        try {
            // Even though the path and $path are the same, call the locator to
            // make sure that the path actually exists
            // The result of this method MUST NOT be the real path (without
            // prefix), because then the generated file has the same cache
            // key as the same template loaded through a different loader.
            // If loaded through a different loader, relative paths won't be
            // resolved, so we'll have the wrong version of the template in
            // he cache.
            return '__puli__'.$this->repo->get($path)->getPath();
        } catch (ResourceNotFoundException $e) {
            throw new Twig_Error_Loader($e->getMessage(), -1, null, $e);
        } catch (InvalidArgumentException $e) {
            throw new Twig_Error_Loader($e->getMessage(), -1, null, $e);
        }
    }

    /**
     * Returns true if the template is still fresh.
     *
     * @param string $path The template name
     * @param int    $time The last modification time of the cached template
     *
     * @return bool true if the template is fresh, false otherwise
     *
     * @throws Twig_Error_Loader When $path is not found
     */
    public function isFresh($path, $time)
    {
        try {
            $file = $this->repo->get($path);

            if (!$file instanceof BodyResource) {
                throw new Twig_Error_Loader(sprintf(
                    'Can only load file resources. Resource "%s" is of type %s.',
                    $path,
                    is_object($file) ? get_class($file) : gettype($file)
                ));
            }

            return $file->getMetadata()->getModificationTime() <= $time;
        } catch (ResourceNotFoundException $e) {
            throw new Twig_Error_Loader($e->getMessage(), -1, null, $e);
        } catch (InvalidArgumentException $e) {
            throw new Twig_Error_Loader($e->getMessage(), -1, null, $e);
        }
    }

    /**
     * Check if we have the source code of a template, given its name.
     *
     * @param string $name The name of the template to check if we can load
     *
     * @return bool If the template source code is handled by this loader or not
     */
    public function exists($name)
    {
        try {
            return $this->repo->contains($name);
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }
}
