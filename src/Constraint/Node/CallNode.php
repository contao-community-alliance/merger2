<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @copyright 2013,2014 bit3 UG. 2015-2017 Contao Community Alliance
 * @author    Tristan Lins <tristan.lins@bit3.de>
 * @author    David Molineus <david.molineus@netzmacht.de>
 *
 * @link      http://bit3.de
 *
 * @license   LGPL-3.0+
 */

namespace ContaoCommunityAlliance\Merger2\Constraint\Node;

use ContaoCommunityAlliance\Merger2\Functions\FunctionCollectionInterface;

class CallNode implements NodeInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var NodeInterface[]
     */
    protected $parameters;

    /**
     * Function collection.
     *
     * @var FunctionCollectionInterface
     */
    private $functionCollection;

    public function __construct($name, array $parameters, FunctionCollectionInterface $functionCollection)
    {
        $this->name = $name;
        $this->parameters = $parameters;
        $this->functionCollection = $functionCollection;
    }

    /**
     * @return NodeInterface
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function evaluate()
    {
        if ($this->functionCollection->supports($this->name)) {
            return $this->functionCollection->execute($this->name, $this->getEvaluatedParameters());
        }

        throw new \RuntimeException('Unknown function '.$this->name);
    }

    protected function getEvaluatedParameters()
    {
        $evaluatedParameters = array();

        foreach ($this->parameters as $parameter) {
            $evaluatedParameters[] = $parameter->evaluate();
        }

        return $evaluatedParameters;
    }
}
