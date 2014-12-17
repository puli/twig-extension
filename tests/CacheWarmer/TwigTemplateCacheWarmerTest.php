<?php

/*
 * This file is part of the puli/twig-puli-extension package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Puli\Extension\Twig\Tests\CacheWarmer;

use Puli\Extension\Twig\CacheWarmer\TwigTemplateCacheWarmer;
use Puli\Repository\InMemoryRepository;

/**
 * @since  1.0
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class TwigTemplateCacheWarmerTest extends \PHPUnit_Framework_TestCase
{
    public function testWarmUp()
    {
        $repo = new InMemoryRepository();
        $repo->add('/webmozart/puli', __DIR__.'/Fixtures');

        $twig = $this->getMock('Twig_Environment');

        $warmer = new TwigTemplateCacheWarmer($repo, $twig);

        $twig->expects($this->at(0))
            ->method('loadTemplate')
            ->with('/webmozart/puli/views/layout.html.twig');
        $twig->expects($this->at(1))
            ->method('loadTemplate')
            ->with('/webmozart/puli/views/show.json.twig');

        $warmer->warmUp(null);
    }
}
