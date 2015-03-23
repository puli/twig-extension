<?php

/*
 * This file is part of the puli/twig-puli-extension package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Puli\Extension\Twig\Tests;

use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Puli\Extension\Twig\PuliExtension;
use Puli\Extension\Twig\PuliTemplateLoader;
use Puli\Repository\InMemoryRepository;
use Puli\Repository\Resource\DirectoryResource;
use Puli\Repository\Resource\GenericResource;
use Puli\WebResourcePlugin\Api\UrlGenerator\ResourceUrlGenerator;
use Twig_Loader_Chain;
use Twig_Loader_Filesystem;

/**
 * @since  1.0
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class PuliExtensionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RandomizedTwigEnvironment
     */
    private $twig;

    /**
     * @var InMemoryRepository
     */
    private $repo;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|ResourceUrlGenerator
     */
    private $urlGenerator;

    protected function setUp()
    {
        $this->repo = new InMemoryRepository();
        $this->repo->add('/acme/blog/views', new DirectoryResource(__DIR__.'/Fixtures/puli'));
        $this->repo->add('/acme/blog/css/style.css', new GenericResource());

        $this->urlGenerator = $this->getMock('Puli\WebResourcePlugin\Api\UrlGenerator\ResourceUrlGenerator');

        $this->twig = new RandomizedTwigEnvironment(new Twig_Loader_Chain(array(
            new PuliTemplateLoader($this->repo),
            new Twig_Loader_Filesystem(__DIR__.'/Fixtures'),
        )));
        $this->twig->addExtension(new PuliExtension($this->repo, $this->urlGenerator));
    }

    public function testRender()
    {
        $this->assertSame(
            "TEMPLATE\n",
            $this->twig->render('/acme/blog/views/template.txt.twig')
        );
    }

    public function testRenderEmpty()
    {
        $this->assertSame(
            "",
            $this->twig->render('/acme/blog/views/empty.txt.twig')
        );
    }

    public function testExtendAbsolutePath()
    {
        $this->assertSame(
            "PARENT\n\nCHILD\n",
            $this->twig->render('/acme/blog/views/extend-absolute.txt.twig')
        );
    }

    public function testExtendRelativePath()
    {
        $this->assertSame(
            "PARENT\n\nCHILD\n",
            $this->twig->render('/acme/blog/views/extend-relative.txt.twig')
        );
    }

    public function testExtendRelativeDotDotPath()
    {
        $this->assertSame(
            "PARENT\n\nCHILD\n",
            $this->twig->render('/acme/blog/views/nested/extend-relative-dot-dot.txt.twig')
        );
    }

    public function testIncludeAbsolutePath()
    {
        $this->assertSame(
            "TEMPLATE\n\nREFERENCE\n",
            $this->twig->render('/acme/blog/views/include-absolute.txt.twig')
        );
    }

    public function testIncludeRelativePath()
    {
        $this->assertSame(
            "TEMPLATE\n\nREFERENCE\n",
            $this->twig->render('/acme/blog/views/include-relative.txt.twig')
        );
    }

    public function testIncludeNonPuliAndRelativePath()
    {
        // Resolution of relative paths should work after including a template
        // with a different loader than PuliTemplateLoader
        $this->assertSame(
            "TEMPLATE\n\nNON PULI REFERENCE\n\nREFERENCE\n",
            $this->twig->render('/acme/blog/views/include-non-puli-and-relative.txt.twig')
        );
    }

    public function testImportAbsolutePath()
    {
        $this->assertSame(
            "TEMPLATE\n\nPULI MACRO\n",
            $this->twig->render('/acme/blog/views/import-absolute.txt.twig')
        );
    }

    public function testImportRelativePath()
    {
        $this->assertSame(
            "TEMPLATE\n\nPULI MACRO\n",
            $this->twig->render('/acme/blog/views/import-relative.txt.twig')
        );
    }

    public function testEmbedAbsolutePath()
    {
        $this->assertSame(
            "TEMPLATE\n\nREFERENCE\n",
            $this->twig->render('/acme/blog/views/embed-absolute.txt.twig')
        );
    }

    public function testEmbedRelativePath()
    {
        $this->assertSame(
            "TEMPLATE\n\nREFERENCE\n",
            $this->twig->render('/acme/blog/views/embed-relative.txt.twig')
        );
    }

    public function testExtendAbsolutePathNonPuli()
    {
        $this->assertSame(
            "PARENT\n\nCHILD\n",
            $this->twig->render('/non-puli/extend-absolute.txt.twig')
        );
    }

    /**
     * @expectedException \Twig_Error_Loader
     */
    public function testExtendRelativePathNonPuli()
    {
        $this->twig->render('/non-puli/extend-relative.txt.twig');
    }

    public function testIncludeWhichExtendsAbsolutePathNonPuli()
    {
        $this->assertSame(
            "TEMPLATE\n\nPARENT\n\nCHILD\n",
            $this->twig->render('/non-puli/include-extend-absolute.txt.twig')
        );
    }

    /**
     * @expectedException \Twig_Error_Loader
     */
    public function testIncludeWhichExtendsRelativePathNonPuli()
    {
        $this->twig->render('/non-puli/include-extend-relative.txt.twig');
    }

    public function testRenderUrlForAbsolutePath()
    {
        $this->urlGenerator->expects($this->once())
            ->method('generateUrl')
            ->with('/acme/blog/css/style.css')
            ->willReturn('/blog/css/style.css');

        $this->assertSame(
            "/blog/css/style.css\n",
            $this->twig->render('/acme/blog/views/resource-url-absolute.txt.twig')
        );
    }

    public function testRenderUrlForRelativePath()
    {
        $this->urlGenerator->expects($this->once())
            ->method('generateUrl')
            ->with('/acme/blog/css/style.css')
            ->willReturn('/blog/css/style.css');

        $this->assertSame(
            "/blog/css/style.css\n",
            $this->twig->render('/acme/blog/views/resource-url-relative.txt.twig')
        );
    }
}
