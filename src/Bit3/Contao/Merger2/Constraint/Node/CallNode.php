<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS
 *
 * @copyright 2013,2014 bit3 UG
 * @author    Tristan Lins <tristan.lins@bit3.de>
 * @link      http://bit3.de
 * @package   bit3/contao-merger2
 * @license   LGPL-3.0+
 */

namespace Bit3\Contao\Merger2\Constraint\Node;

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

	function __construct($name, array $parameters)
	{
		$this->name       = $name;
		$this->parameters = $parameters;
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
		if (!isset($GLOBALS['MERGER2_FUNCTION'][$this->name])) {
			throw new \RuntimeException('Unknown function ' . $this->name);
		}

		return call_user_func_array($GLOBALS['MERGER2_FUNCTION'][$this->name], $this->getEvaluatedParameters());
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
