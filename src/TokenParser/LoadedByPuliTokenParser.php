<?php

/*
 * This file is part of the puli/twig-puli-extension package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Puli\TwigExtension\TokenParser;

use Puli\TwigExtension\Node\LoadedByPuliNode;
use Twig_Error_Syntax;
use Twig_NodeInterface;
use Twig_Token;
use Twig_TokenParser;

/**
 * Turns the "{% loaded_by_puli %}" token into an instance of
 * {@link LoadedByPuliNode}.
 *
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class LoadedByPuliTokenParser extends Twig_TokenParser
{
    /**
     * Parses a token and returns a node.
     *
     * @param Twig_Token $token A Twig_Token instance
     *
     * @return Twig_NodeInterface A Twig_NodeInterface instance
     *
     * @throws Twig_Error_Syntax
     */
    public function parse(Twig_Token $token)
    {
        $this->parser->getStream()->next();

        return new LoadedByPuliNode();
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'loaded_by_puli';
    }
}
