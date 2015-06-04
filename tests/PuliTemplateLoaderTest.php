<?php

/*
 * This file is part of the puli/twig-puli-extension package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Puli\TwigExtension\Tests;

use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Puli\Repository\Api\ResourceRepository;
use Puli\Repository\Tests\Resource\TestDirectory;
use Puli\TwigExtension\PuliTemplateLoader;

/**
 * @since  1.0
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class PuliTemplateLoaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ResourceRepository|PHPUnit_Framework_MockObject_MockObject
     */
    private $repo;

    /**
     * @var PuliTemplateLoader
     */
    private $loader;

    protected function setUp()
    {
        $this->repo = $this->getMock('Puli\Repository\Api\ResourceRepository');
        $this->loader = new PuliTemplateLoader($this->repo);
    }

    /**
     * @expectedException \Twig_Error_Loader
     */
    public function testGetSourceFailsIfNoFileResource()
    {
        $this->repo->expects($this->once())
            ->method('get')
            ->with('/webmozart/puli/file')
            ->will($this->returnValue(new TestDirectory('/webmozart/puli/file')));

        $this->loader->getSource('/webmozart/puli/file');
    }

    /**
     * @expectedException \Twig_Error_Loader
     */
    public function testIsFreshFailsIfNoFileResource()
    {
        $this->repo->expects($this->once())
            ->method('get')
            ->with('/webmozart/puli/file')
            ->will($this->returnValue(new TestDirectory('/webmozart/puli/file')));

        $this->loader->isFresh('/webmozart/puli/file', 123);
    }

    public function testExistsReturnsFalseIfNoFileResource()
    {
        $this->repo->expects($this->once())
            ->method('contains')
            ->with('/webmozart/puli/file')
            ->willReturn(false);

        $this->assertFalse($this->loader->exists('/webmozart/puli/file'));
    }
}
