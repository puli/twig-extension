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

use Puli\TwigExtension\Node\LoadedByPuliNode;
use Twig_BaseNodeVisitor;
use Twig_Environment;
use Twig_Node;
use Twig_Node_Body;
use Twig_Node_Module;
use Webmozart\PathUtil\Path;

/**
 * Adds the "puli-dir" attribute to all {@link Twig_Module} nodes that were
 * loaded through the Puli loader.
 *
 * This attribute can be used to convert relative paths in the template to
 * absolute paths.
 *
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class PuliDirTagger extends Twig_BaseNodeVisitor
{
    /**
     * @var Twig_Node_Module|null
     */
    private $moduleNode;

    /**
     * Called before child nodes are visited.
     *
     * @param Twig_Node        $node The node to visit
     * @param Twig_Environment $env  The Twig environment instance
     *
     * @return Twig_Node The modified node
     */
    protected function doEnterNode(Twig_Node $node, Twig_Environment $env)
    {
        if ($node instanceof Twig_Node_Module) {
            $this->moduleNode = $node;
        }

        return $node;
    }

    /**
     * Called after child nodes are visited.
     *
     * @param Twig_Node        $node The node to visit
     * @param Twig_Environment $env  The Twig environment instance
     *
     * @return Twig_Node|false The modified node or false if the node must be removed
     */
    protected function doLeaveNode(Twig_Node $node, Twig_Environment $env)
    {
        // Tag the node if it contains a LoadedByPuliNode
        // This cannot be done in enterNode(), because only leaveNode() may
        // return false in order to remove a node
        if ($node instanceof LoadedByPuliNode) {
            if (null !== $this->moduleNode) {
                $this->moduleNode->setAttribute(
                    'puli-dir',
                    Path::getDirectory($this->moduleNode->getAttribute('filename'))
                );
            }

            // Remove that node from the final tree
            return false;
        }

        // Special case: Empty files that contained only the LoadedByPuliNode
        // now contain no nodes anymore. Twig, however, expects Twig_Node_Body
        // instances to have at least one (even if empty) node with name 0.
        if ($node instanceof Twig_Node_Body) {
            if (0 === $node->count()) {
                $node->setNode(0, new Twig_Node(array(), array(), 1));
            }
        }

        return $node;
    }

    /**
     * Returns the priority for this visitor.
     *
     * Priority should be between -10 and 10 (0 is the default).
     *
     * @return int The priority level
     */
    public function getPriority()
    {
        // Should be launched very early on so that other visitors don't have
        // to deal with the LoadedByPuliNode
        return -10;
    }
}
