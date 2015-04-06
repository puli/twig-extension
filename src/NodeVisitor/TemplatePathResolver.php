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

use Puli\AssetPlugin\Api\UrlGenerator\ResourceUrlGenerator;
use Puli\Extension\Twig\PuliExtension;
use Puli\Repository\Api\ResourceRepository;
use RuntimeException;
use Twig_Node;
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
     * @var ResourceUrlGenerator
     */
    private $urlGenerator;

    public function __construct(ResourceRepository $repo, ResourceUrlGenerator $urlGenerator = null)
    {
        parent::__construct($repo);

        $this->urlGenerator = $urlGenerator;
    }

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
            return $this->processModuleNode($node);
        }

        if ($node instanceof Twig_Node_Include) {
            return $this->processIncludeNode($node);
        }

        if ($node instanceof Twig_Node_Import) {
            return $this->processImportNode($node);
        }

        if ($node instanceof Twig_Node_Expression_Function && 'resource_url' === $node->getAttribute('name')) {
            return $this->processResourceUrlFunction($node);
        }

        return null;
    }

    private function processModuleNode(Twig_Node_Module $node)
    {
        // Resolve relative parent template paths to absolute paths
        $parentNode = $node->getNode('parent');
        $traitsNode = $node->getNode('traits');

        // If the template extends another template, resolve the path
        if ($parentNode instanceof Twig_Node_Expression_Constant) {
            $this->processConstantNode($parentNode);
        }

        // Resolve paths of embedded templates
        foreach ($node->getAttribute('embedded_templates') as $embeddedNode) {
            $this->processEmbeddedTemplateNode($embeddedNode);
        }

        // Resolve paths of used templates
        foreach ($traitsNode as $traitNode) {
            $this->processTraitNode($traitNode);
        }

        return null;
    }

    private function processEmbeddedTemplateNode(Twig_Node_Module $embeddedNode)
    {
        $embedParent = $embeddedNode->getNode('parent');

        // If the template extends another template, resolve the path
        if ($embedParent instanceof Twig_Node_Expression_Constant) {
            $this->processConstantNode($embedParent);
        }
    }

    private function processTraitNode(Twig_Node $traitNode)
    {
        $usedTemplate = $traitNode->getNode('template');

        // If the template extends another template, resolve the path
        if ($usedTemplate instanceof Twig_Node_Expression_Constant) {
            $this->processConstantNode($usedTemplate);
        }
    }

    private function processIncludeNode(Twig_Node_Include $node)
    {
        $exprNode = $node->getNode('expr');

        if ($exprNode instanceof Twig_Node_Expression_Constant) {
            $this->processConstantNode($exprNode);
        }

        return null;
    }

    private function processImportNode(Twig_Node_Import $node)
    {
        $exprNode = $node->getNode('expr');

        if ($exprNode instanceof Twig_Node_Expression_Constant) {
            $this->processConstantNode($exprNode);
        }

        return null;
    }

    protected function processResourceUrlFunction(Twig_Node $node)
    {
        if (!$this->urlGenerator) {
            throw new RuntimeException(
                'The resource_url() function is only available if the Puli '.
                'Web Resource Plugin is installed.'
            );
        }

        $argsNode = $node->getNode('arguments');

        if (!$argsNode->hasNode(0)) {
            return null;
        }

        $exprNode = $argsNode->getNode(0);

        if (!$exprNode instanceof Twig_Node_Expression_Constant) {
            return null;
        }

        $this->processConstantNode($exprNode);

        // Optimize away function call
        $exprNode->setAttribute('value', $this->urlGenerator->generateUrl($exprNode->getAttribute('value')));

        return $exprNode;
    }

    private function processConstantNode(Twig_Node_Expression_Constant $node)
    {
        $node->setAttribute('value', $this->resolvePath($node->getAttribute('value'), $this->currentDir));
    }
}
