<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    Tristan Lins <tristan.lins@bit3.de>
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @copyright 2013-2014 bit3 UG
 * @copyright 2015-2018 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0-or-later
 * @link      https://github.com/contao-community-alliance/merger2
 */

declare(strict_types=1);

namespace ContaoCommunityAlliance\Merger2\Constraint\Parser;

/**
 * An input token.
 */
final class InputToken
{
    const OPEN_BRACKET = 'open_bracket';

    const CLOSE_BRACKET = 'close_bracket';

    const OPEN_SQUARE_BRACKET = 'open_square_bracket';

    const CLOSE_SQUARE_BRACKET = 'close_square_bracket';

    const AND_CONJUNCTION = 'and_conjunction';

    const OR_CONJUNCTION = 'or_conjunction';

    const NOT = 'not';

    const QUOTE = 'quote';

    const STRING = 'string';

    const TRUE = 'true';

    const FALSE = 'false';

    const CALL = 'call';

    const TOKEN_SEPARATOR = 'token_separator';

    const LIST_SEPARATOR = 'list_separator';

    const END_OF_STREAM = 'end_of_stream';

    /**
     * Input token type.
     *
     * @var string
     */
    protected $type;

    /**
     * Input token value.
     *
     * @var string
     */
    protected $value;

    /**
     * InputToken constructor.
     *
     * @param string $type  Input token type.
     * @param string $value Input token value.
     */
    public function __construct($type, $value = null)
    {
        $this->type  = $type;
        $this->value = $value;
    }

    /**
     * Check if input token is a specific type.
     *
     * @param string $type Type to compare with.
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function is($type)
    {
        return $this->type === $type;
    }

    /**
     * Get the type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
