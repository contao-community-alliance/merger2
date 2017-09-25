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

use ContaoCommunityAlliance\Merger2\Functions\FunctionCollectionInterface;

/**
 * Class CallNode.
 */
class CallNode implements NodeInterface
{
    /**
     * Function invoke name.
     *
     * @var string
     */
    protected $name;

    /**
     * Parameters.
     *
     * @var NodeInterface[]|array
     */
    protected $parameters;

    /**
     * Function collection.
     *
     * @var FunctionCollectionInterface
     */
    private $functionCollection;

    /**
     * CallNode constructor.
     *
     * @param string                      $name               Function invoke name.
     * @param NodeInterface[]|array       $parameters         Parameters.
     * @param FunctionCollectionInterface $functionCollection Function collection.
     */
    public function __construct($name, array $parameters, FunctionCollectionInterface $functionCollection)
    {
        $this->name               = $name;
        $this->parameters         = $parameters;
        $this->functionCollection = $functionCollection;
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the parameters.
     *
     * @return NodeInterface[]|array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException When unknown function is called.
     */
    public function evaluate()
    {
        return $this->functionCollection->execute($this->name, $this->getEvaluatedParameters());
    }

    /**
     * Evaluate all parameters.
     *
     * @return array
     */
    protected function getEvaluatedParameters()
    {
        $evaluatedParameters = array();

        foreach ($this->parameters as $parameter) {
            $evaluatedParameters[] = $parameter->evaluate();
        }

        return $evaluatedParameters;
    }
}
