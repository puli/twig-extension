<?php

/*
 * This file is part of the puli/twig-puli-extension package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Puli\TwigExtension\NodeVisitor;

use Puli\Repository\Api\ResourceRepository;
use Twig_BaseNodeVisitor;
use Twig_Environment;
use Twig_Node;
use Twig_Node_Module;
use Webmozart\PathUtil\Path;

/**
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
abstract class AbstractPathResolver extends Twig_BaseNodeVisitor
{
    /**
     * @var ResourceRepository
     */
    protected $repo;

    /**
     * @var string
     */
    protected $currentDir;

    public function __construct(ResourceRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * {@inheritdoc}
     */
    protected function doEnterNode(Twig_Node $node, Twig_Environment $env)
    {
        // Remember the directory of the current file
        if ($node instanceof Twig_Node_Module && $node->hasAttribute('puli-dir')) {
            // Currently, it doesn't seem like Twig does recursive traversals
            // (i.e. starting the traversal of another module while a previous
            // one is still in progress). Thus we don't need to track existing
            // values here.
            $this->currentDir = $node->getAttribute('puli-dir');
        }

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    protected function doLeaveNode(Twig_Node $node, Twig_Environment $env)
    {
        // Only process if the current directory was set
        if (null !== $this->currentDir) {
            if ($result = $this->processNode($node)) {
                $node = $result;
            }
        }

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    protected function resolvePath($path, $checkPath = false)
    {
        // Empty path? WTF I don't want to deal with this.
        if ('' === $path) {
            return $path;
        }

        // Absolute paths are fine
        if ('/' === $path[0]) {
            return $path;
        }

        // Resolve relative paths
        $absolutePath = Path::makeAbsolute($path, $this->currentDir);

        // With other loaders enabled, it may happen that a path looks like
        // a relative path, but is none, for example
        // "AcmeBlogBundle::index.html.twig", which doesn't start with a forward
        // slash. For this reason, if $checkPath is true, we should only resolve
        // paths if they actually exist in the repository.
        if (!$checkPath || $this->repo->contains($absolutePath)) {
            return $absolutePath;
        }

        // Return the path unchanged if $checkPath and the path does not exist
        return $path;
    }

    /**
     * @param Twig_Node $node
     *
     * @return Twig_Node|null
     */
    abstract protected function processNode(Twig_Node $node);
}
