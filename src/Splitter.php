<?php declare(strict_types=1);
/**
 * pgn-splitter (https://github.com/chesszebra/pgn-splitter)
 *
 * @link https://github.com/chesszebra/pgn-splitter for the canonical source repository
 * @copyright Copyright (c) 2017 Chess Zebra (https://chesszebra.com)
 * @license https://github.com/chesszebra/pgn-splitter/blob/master/LICENSE MIT
 */

namespace ChessZebra\Chess\Pgn;

use ChessZebra\Chess\Pgn\Exception\InvalidStreamException;

final class Splitter
{
    const SPLIT_GAMES = 0;
    const SPLIT_CHUNKS = 1;

    const STATE_LIMBO = 0;
    const STATE_TAGS = 1;
    const STATE_MOVES = 2;

    /**
     * The stream to split.
     *
     * @var resource
     */
    private $stream;

    /**
     * The mode to split the stream on.
     *
     * @var int
     */
    private $mode;

    /**
     * The current state of the splitter.
     *
     * @var int
     */
    private $state;

    /**
     * The parsed buffer.
     *
     * @var string
     */
    private $buffer;

    /**
     * Initializes a new instance of this class.
     *
     * @param resource $stream The stream to split.
     * @param int $mode The mode to split on.
     * @throws InvalidStreamException Thrown when an invalid stream is provided.
     */
    public function __construct($stream, int $mode = self::SPLIT_GAMES)
    {
        if (!is_resource($stream)) {
            throw new InvalidStreamException('The provided stream is not a valid resource.');
        }

        $this->stream = $stream;
        $this->mode = $mode;
    }

    /**
     * Splits the stream into loose chunks and provides them to the given callback.
     *
     * @param callable $callback The callback that is called for each found chunk.
     * @return int Returns the amount of chunks found in the stream.
     */
    public function split(callable $callback): int
    {
        $result = 0;

        $this->buffer = '';
        $this->state = self::STATE_LIMBO;

        while (!feof($this->stream)) {
            $line = fgets($this->stream);
            $line = str_replace("\r\n", "\n", $line); // Replace Windows line endings with unix
            $line = str_replace("\r", "\n", $line); // Replace mac line endings with unix

            if (!$line) {
                continue;
            }

            switch ($this->state) {
                case self::STATE_LIMBO:
                    $this->parseLimbo($line);
                    break;

                case self::STATE_TAGS:
                    $this->parseTags($callback, $result, $line);
                    break;

                case self::STATE_MOVES:
                    $this->parseMoves($callback, $result, $line);
                    break;

                default:
                    throw new \RuntimeException(sprintf('The state "%d" is not implemented.', $this->state));
            }
        }

        if (trim($this->buffer) !== '') {
            $result++;
            $callback($this->buffer);
            $this->buffer = '';
        }

        return $result;
    }

    private function parseLimbo(string $line)
    {
        if (trim($line) === '') {
            return;
        }

        if ($line[0] === '[') {
            $this->buffer .= $line;
            $this->state = self::STATE_TAGS;
        } else {
            $this->buffer .= $line;
            $this->state = self::STATE_MOVES;
        }
    }

    private function parseTags(callable $callback, int &$result, string $line)
    {
        if ($line[0] === '[') {
            $this->buffer .= $line;
            return;
        }

        if ($this->mode === self::SPLIT_CHUNKS) {
            $result++;

            $callback($this->buffer);

            $this->buffer = '';
            return;
        }

        $this->buffer .= $line;

        $this->state = self::STATE_MOVES;
    }

    private function parseMoves(callable $callback, int &$result, string $line)
    {
        if ($line === "\n") {
            $this->state = self::STATE_LIMBO;

            $result++;

            $callback($this->buffer);

            $this->buffer = '';
        }

        $this->buffer .= $line;
    }
}
