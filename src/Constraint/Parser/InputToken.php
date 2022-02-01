<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    Tristan Lins <tristan.lins@bit3.de>
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @copyright 2013-2014 bit3 UG
 * @copyright 2015-2022 Contao Community Alliance
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
    public const OPEN_BRACKET = 'open_bracket';

    public const CLOSE_BRACKET = 'close_bracket';

    public const OPEN_SQUARE_BRACKET = 'open_square_bracket';

    public const CLOSE_SQUARE_BRACKET = 'close_square_bracket';

    public const AND_CONJUNCTION = 'and_conjunction';

    public const OR_CONJUNCTION = 'or_conjunction';

    public const NOT = 'not';

    public const QUOTE = 'quote';

    public const STRING = 'string';

    public const TRUE = 'true';

    public const FALSE = 'false';

    public const CALL = 'call';

    public const TOKEN_SEPARATOR = 'token_separator';

    public const LIST_SEPARATOR = 'list_separator';

    public const END_OF_STREAM = 'end_of_stream';

    /**
     * Input token type.
     *
     * @var string
     */
    protected $type;

    /**
     * Input token value.
     *
     * @var string|null
     */
    protected $value;

    /**
     * InputToken constructor.
     *
     * @param string $type  Input token type.
     * @param string $value Input token value.
     */
    public function __construct(string $type, ?string $value = null)
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
    public function is(string $type): bool
    {
        return $this->type === $type;
    }

    /**
     * Get the type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the value.
     *
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }
}
