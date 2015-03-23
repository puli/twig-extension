<?php

/*
 * This file is part of the puli/twig-puli-extension package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Puli\Extension\Twig\NodeVisitor;

use Puli\Extension\Twig\PuliExtension;
use Twig_Node_Expression_Constant;
use Twig_Node_Expression_Function;
use Twig_Node_Import;
use Twig_Node_Include;
use Twig_Node_Module;
use Twig_NodeInterface;

/**
 * @since  1.0
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class TemplatePathResolver extends AbstractPathResolver
{
    /**
     * Returns the priority for this visitor.
     *
     * Priority should be between -10 and 10 (0 is the default).
     *
     * @return integer The priority level
     */
    public function getPriority()
    {
        return PuliExtension::RESOLVE_PATHS;
    }

    /**
     * {@inheritdoc}
     */
    protected function processNode(Twig_NodeInterface $node)
    {
        if ($node instanceof Twig_Node_Module) {
            // Resolve relative parent template paths to absolute paths
            $parentNode = $node->getNode('parent');

            // If the template extends another template, resolve the path
            if ($parentNode instanceof Twig_Node_Expression_Constant) {
                $this->processConstantNode($parentNode);
            }

            // Resolve paths of embedded templates
            foreach ($node->getAttribute('embedded_templates') as $embeddedNode) {
                /** @var Twig_Node_Module $embeddedNode */
                $embedParent = $embeddedNode->getNode('parent');

                // If the template extends another template, resolve the path
                if ($embedParent instanceof Twig_Node_Expression_Constant) {
                    $this->processConstantNode($embedParent);
                }
            }
        } elseif ($node instanceof Twig_Node_Include || $node instanceof Twig_Node_Import) {
            $exprNode = $node->getNode('expr');

            if ($exprNode instanceof Twig_Node_Expression_Constant) {
                $this->processConstantNode($exprNode);
            }
        } elseif ($node instanceof Twig_Node_Expression_Function && 'resource_url' === $node->getAttribute('name')) {
            $argsNode = $node->getNode('arguments');

            if ($argsNode->hasNode(0)) {
                $exprNode = $argsNode->getNode(0);

                if ($exprNode instanceof Twig_Node_Expression_Constant) {
                    $this->processConstantNode($exprNode);
                }
            }
        }
    }

    private function processConstantNode(Twig_Node_Expression_Constant $node)
    {
        $node->setAttribute('value', $this->resolvePath($node->getAttribute('value'), $this->currentDir));
    }
}
