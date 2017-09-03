# pgn-splitter

A PHP library to split PGN files into chunks per game.

## Installation

Via composer:

```bash
composer require chesszebra/pgn-splitter
```

## Usage

```php
$handle = fopen('my-games.pgn', 'r');

$splitter = new \ChessZebra\Chess\Pgn\Splitter();
$splitter->split(function(string $buffer) {
    echo $buffer;
});
 
```
