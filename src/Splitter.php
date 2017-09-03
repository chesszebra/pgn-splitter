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
    const STATE_TAGS = 0;
    const STATE_MOVES = 1;

    /**
     * The stream to split.
     *
     * @var resource
     */
    private $stream;

    /**
     * Initializes a new instance of this class.
     *
     * @param resource $stream The stream to split.
     * @throws InvalidStreamException
     */
    public function __construct($stream)
    {
        if (!is_resource($stream)) {
            throw new InvalidStreamException('The provided stream is not a valid resource.');
        }

        $this->stream = $stream;
    }

    /**
     * Splits the stream into loose pgn files which stored in the given directory.
     *
     * @param callable $callback The callback that is called for each splitted file.
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
