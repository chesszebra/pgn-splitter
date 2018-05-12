<?php declare(strict_types=1);
/**
 * pgn-splitter (https://github.com/chesszebra/pgn-splitter)
 *
 * @link https://github.com/chesszebra/pgn-splitter for the canonical source repository
 * @copyright Copyright (c) 2017 Chess Zebra (https://chesszebra.com)
 * @license https://github.com/chesszebra/pgn-splitter/blob/master/LICENSE MIT
 */

namespace ChessZebra\Chess\Pgn;

use PHPUnit\Framework\TestCase;

final class SplitterTest extends TestCase
{
    private function createStream(string $content)
    {
        $stream = fopen('php://memory','r+');

        fwrite($stream, $content);

        rewind($stream);

        return $stream;
    }

    /**
     * @expectedException \ChessZebra\Chess\Pgn\Exception\InvalidStreamException
     * @expectedExceptionCode 0
     * @expectedExceptionMessage The provided stream is not a valid resource.
     */
    public function testSplitterWithInvalidStream()
    {
        // Arrange
        $stream = null;

        // Act
        new Splitter($stream);

        // Assert
        // Handled in the docblock.
    }

    public function testSplitWithEmptyStream()
    {
        // Arrange
        $stream = $this->createStream('');
        $splitter = new Splitter($stream);
        $callback = function(string $buffer) { };

        // Act
        $result = $splitter->split($callback);

        // Assert
        static::assertEquals(0, $result);
    }

    public function testSplitWith1WildcardGame()
    {
        // Arrange
        $stream = $this->createStream('*');
        $splitter = new Splitter($stream);
        $callback = function(string $buffer) { };

        // Act
        $result = $splitter->split($callback);

        // Assert
        static::assertEquals(1, $result);
    }

    public function testSplitWith2WildcardGames()
    {
        // Arrange
        $stream = $this->createStream("*\n\n*");
        $splitter = new Splitter($stream);
        $callback = function(string $buffer) { };

        // Act
        $result = $splitter->split($callback);

        // Assert
        static::assertEquals(2, $result);
    }

    public function testSplitWithTagsAndGame()
    {
        // Arrange
        $stream = $this->createStream("[A]\n[B]\n\n*");
        $splitter = new Splitter($stream);
        $callback = function(string $buffer) { };

        // Act
        $result = $splitter->split($callback);

        // Assert
        static::assertEquals(1, $result);
    }

    public function testSplitPerChunk()
    {
        // Arrange
        $stream = $this->createStream("[A]\n[B]\n\n*");
        $splitter = new Splitter($stream, Splitter::SPLIT_CHUNKS);
        $callback = function(string $buffer) { };

        // Act
        $result = $splitter->split($callback);

        // Assert
        static::assertEquals(2, $result);
    }

    public function testSplitLichessGames()
    {
        // Arrange
        $stream = $this->createStream("[A]

1. e4 e5


[B]

1. e4 e5


[C]

1. d4 d5


[D]

1. e4 e5


");
        $splitter = new Splitter($stream, Splitter::SPLIT_GAMES);
        $callback = function(string $buffer) { };

        // Act
        $result = $splitter->split($callback);

        // Assert
        static::assertEquals(4, $result);
    }
}
