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

    const STATE_TAGS = 0;
    const STATE_MOVES = 1;

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

        $buffer = '';
        $state = self::STATE_TAGS;

        while (!feof($this->stream)) {
            $line = fgets($this->stream);
            $line = str_replace("\r\n", "\n", $line); // Replace Windows line endings with unix
            $line = str_replace("\r", "\n", $line); // Replace mac line endings with unix

            if (!$line) {
                continue;
            }

            if ($state === self::STATE_TAGS) {
                $buffer .= $line;

                if ($line[0] !== '[') {
                    if ($this->mode === self::SPLIT_CHUNKS) {
                        $result++;
                        $callback($buffer);
                        $buffer = '';
                    }

                    $state = self::STATE_MOVES;
                }
            } elseif ($state === self::STATE_MOVES && $line === "\n") {
                $result++;
                $callback($buffer);
                $buffer = '';

                $state = self::STATE_TAGS;
            } else {
                $buffer .= $line;
            }
        }

        if ($buffer) {
            $result++;
            $callback($buffer);
        }

        return $result;
    }
}
