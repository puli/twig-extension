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

use Puli\Repository\Api\ResourceRepository;
use Puli\TwigExtension\NodeVisitor\PuliDirTagger;
use Puli\TwigExtension\NodeVisitor\TemplatePathResolver;
use Puli\TwigExtension\TokenParser\LoadedByPuliTokenParser;
use Puli\UrlGenerator\Api\UrlGenerator;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * @since  1.0
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class PuliExtension extends Twig_Extension
{
    /**
     * Priority for node visitors that want to work with relative path before
     * they are turned into absolute paths.
     */
    const PRE_RESOLVE_PATHS = 4;

    /**
     * Priority for node visitors that turn relative paths into absolute paths.
     */
    const RESOLVE_PATHS = 5;

    /**
     * Priority for node visitors that want to work with absolute paths.
     */
    const POST_RESOLVE_PATHS = 6;

    /**
     * @var ResourceRepository
     */
    private $repo;

    /**
     * @var UrlGenerator
     */
    private $urlGenerator;

    /**
     * @var bool
     */
    private $supportFallbackLoader;

    public function __construct(ResourceRepository $repo, UrlGenerator $urlGenerator = null, $supportFallbackLoader = false)
    {
        $this->repo = $repo;
        $this->urlGenerator = $urlGenerator;
        $this->supportFallbackLoader = $supportFallbackLoader;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'puli';
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeVisitors()
    {
        return array(
            new PuliDirTagger(),
            new TemplatePathResolver($this->repo, $this->urlGenerator, $this->supportFallbackLoader),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return array(new LoadedByPuliTokenParser());
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        if (!$this->urlGenerator) {
            return array();
        }

        return array(
            new Twig_SimpleFunction('resource_url', array($this->urlGenerator, 'generateUrl')),
        );
    }
}
