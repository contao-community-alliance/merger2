<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    Tristan Lins <tristan.lins@bit3.de>
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @copyright 2013-2014 bit3 UG. 2015-2017 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0+
 * @link      https://github.com/contao-community-alliance/merger2
 */

namespace ContaoCommunityAlliance\Merger2\Constraint\Node;

/**
 * Class GroupNode.
 */
class GroupNode implements NodeInterface
{
    /**
     * Child node.
     *
     * @var NodeInterface
     */
    protected $child;

    /**
     * GroupNode constructor.
     *
     * @param NodeInterface $child Node child.
     */
    public function __construct(NodeInterface $child)
    {
        $this->child = $child;
    }

    /**
     * Get the child node.
     *
     * @return NodeInterface
     */
    public function getChild()
    {
        return $this->child;
    }

    /**
     * {@inheritdoc}
     */
    public function evaluate()
    {
        return $this->child && $this->child->evaluate();
    }
}
