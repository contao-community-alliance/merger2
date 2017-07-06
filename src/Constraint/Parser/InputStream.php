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

namespace ContaoCommunityAlliance\Merger2\Constraint\Parser;

/**
 * The parser input stream.
 */
class InputStream
{
    /**
     * Raw input.
     *
     * @var string
     */
    protected $input;

    /**
     * Stack.
     *
     * @var array
     */
    protected $stack = array();

    /**
     * Simple char mapping to token type.
     *
     * @var array
     */
    private $charMapping = [
        ',' => InputToken::LIST_SEPARATOR,
        '(' => InputToken::OPEN_BRACKET,
        ')' => InputToken::CLOSE_BRACKET,
        '[' => InputToken::OPEN_SQUARE_BRACKET,
        ']' => InputToken::CLOSE_SQUARE_BRACKET,
    ];

    /**
     * Conjunction char mapping.
     *
     * @var array
     */
    private $conjunctionMapping = [
        '&' => InputToken::AND_CONJUNCTION,
        '|' => InputToken::OR_CONJUNCTION,
    ];

    /**
     * InputStream constructor.
     *
     * @param string $input Raw input.
     */
    public function __construct($input)
    {
        $this->input = $input;
    }

    /**
     * Return the remaining length of the input.
     *
     * @return int
     */
    public function length()
    {
        return mb_strlen($this->input);
    }

    /**
     * Check if stream has more tokens.
     *
     * @return bool
     */
    public function hasMore()
    {
        return $this->length() > 0;
    }

    /**
     * Check if stream is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->length() == 0;
    }

    /**
     * Undo a token removal.
     *
     * @param InputToken $token Input token.
     *
     * @return void
     */
    public function undo(InputToken $token)
    {
        array_unshift($this->stack, $token);
    }

    /**
     * Read the next char from the input.
     *
     * @return InputToken
     */
    public function next()
    {
        if ($this->stack) {
            return array_shift($this->stack);
        }

        if ($this->isEmpty()) {
            return new InputToken(InputToken::END_OF_STREAM);
        }

        $char = $this->head();

        if ($char == ' ' || $char == "\t" || $char == "\n") {
            $this->input = ltrim($this->input);

            return new InputToken(InputToken::TOKEN_SEPARATOR);
        }

        if (isset($this->charMapping[$char])) {
            $this->skip();

            return new InputToken($this->charMapping[$char]);
        }

        $token = $this->createTokenFromCombinedChars($char);
        if ($token) {
            return $token;
        }

        return $this->createInputTokenFromWordSequence($char);
    }

    /**
     * Read a quoted sequence.
     *
     * @return string
     *
     * @throws InputStreamException When unexpected end of sequence is reached.
     */
    protected function readQuotedSequence()
    {
        $buffer = '';
        $escape = false;
        $length = strlen($this->input);

        while ($length) {
            $char = $this->read();

            if ($escape) {
                $buffer .= $char;
                $escape  = false;
            } elseif ($char == '\\') {
                $escape = true;
            } elseif ($char == '"' || $char == "'") {
                return $buffer;
            } else {
                $buffer .= $char;
            }

            $length = strlen($this->input);
        }

        throw new InputStreamException('Unexpected end of quoted sequence');
    }

    /**
     * Read a word sequence.
     *
     * @return string
     */
    protected function readWordSequence()
    {
        $buffer = '';
        $length = strlen($this->input);

        while ($length) {
            $char = $this->head();

            if (!$this->checkWordCharacter($char)) {
                break;
            }

            $buffer .= $char;
            $this->skip();
            $length = strlen($this->input);
        }

        return $buffer;
    }

    /**
     * Validate a word character.
     *
     * @param string $char Given character.
     *
     * @return int
     */
    protected function checkWordCharacter($char)
    {
        return preg_match('~^[\w<>=!]$~', $char);
    }

    /**
     * Expect a word character.
     *
     * @param string $char Expected word character.
     *
     * @return void
     * @throws ParserException When invalid token is given.
     */
    protected function expectWordCharacter($char)
    {
        if (!$this->checkWordCharacter($char)) {
            throw new ParserException('Invalid token, expect a "word" character got '.$char);
        }
    }

    /**
     * Expect a specific character at the current position.
     *
     * @param string $char Expected character.
     *
     * @return void
     *
     * @throws ParserException When token is not as expected.
     */
    protected function expect($char)
    {
        $readChar = $this->read();

        if ($char != $readChar) {
            throw new ParserException('Invalid token, expect '.$char.' got '.$readChar);
        }
    }

    /**
     * Get the head character.
     *
     * @return string
     */
    protected function head()
    {
        return mb_substr($this->input, 0, 1);
    }

    /**
     * Skip a character.
     *
     * @return void
     */
    protected function skip()
    {
        $this->input = mb_substr($this->input, 1);
    }

    /**
     * Read a character and skip to the next.
     *
     * @return string
     */
    protected function read()
    {
        $char = $this->head();
        $this->skip();

        return $char;
    }

    /***
     * Create input token from a word sequence.
     *
     * @param string $key Given input.
     *
     * @return InputToken
     * @throws ParserException When invalid token is given.
     */
    private function createInputTokenFromWordSequence($key)
    {
        $this->expectWordCharacter($key);
        $sequence = $this->readWordSequence();

        $lowerSequence = strtolower($sequence);

        if ($lowerSequence == 'and') {
            return new InputToken(InputToken::AND_CONJUNCTION);
        }

        if ($lowerSequence == 'or') {
            return new InputToken(InputToken::OR_CONJUNCTION);
        }

        if ($lowerSequence == 'not') {
            return new InputToken(InputToken::NOT);
        }

        if ($lowerSequence == 'true') {
            return new InputToken(InputToken::TRUE);
        }

        if ($lowerSequence == 'false') {
            return new InputToken(InputToken::FALSE);
        }

        if ($this->head() == '(') {
            return new InputToken(InputToken::CALL, $sequence);
        }

        return new InputToken(InputToken::STRING, $sequence);
    }

    /**
     * Create input token for chars which have relations to next characters.
     *
     * If no combined char is given null is returned.
     *
     * @param string $char Given char.
     *
     * @return InputToken|null
     */
    private function createTokenFromCombinedChars($char)
    {
        if (isset($this->conjunctionMapping[$char])) {
            $this->skip();
            if ($this->head() === $char) {
                $this->skip();
            }

            return new InputToken($this->conjunctionMapping[$char]);
        }

        if ($char == '!') {
            $this->skip();

            // special sequence behavior
            if ($this->head() == '=') {
                $sequence = $char.$this->readWordSequence();

                return new InputToken(InputToken::STRING, $sequence);
            }

            return new InputToken(InputToken::NOT);
        }

        if ($char == '$') {
            $this->skip();
            $name = $this->readWordSequence();

            return new InputToken(InputToken::VARIABLE, $name);
        }

        if ($char == '"' || $char == "'") {
            $this->skip();
            $sequence = $this->readQuotedSequence();

            return new InputToken(InputToken::STRING, $sequence);
        }

        return null;
    }
}
