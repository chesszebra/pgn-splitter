# pgn-splitter

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

A PHP library to split PGN files into chunks per game or per section.

## Installation

Via composer:

```bash
composer require chesszebra/pgn-splitter
```

## Usage

Split a stream per game:

```php
use ChessZebra\Chess\Pgn\Splitter;

$stream = fopen('my-games.pgn', 'r');

$splitter = new Splitter($stream, Splitter::SPLIT_GAMES);
$splitter->split(function(string $buffer) {
    echo $buffer;
});
```

Or split a stream per chunk (tags and moves chunks):

```php
use ChessZebra\Chess\Pgn\Splitter;

$stream = fopen('my-games.pgn', 'r');

$splitter = new Splitter($stream, Splitter::SPLIT_CHUNKS);
$splitter->split(function(string $buffer) {
    echo $buffer;
});
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please report them via [HackerOne][link-hackerone].

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/chesszebra/pgn-splitter.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/chesszebra/pgn-splitter/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/chesszebra/pgn-splitter.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/chesszebra/pgn-splitter.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/chesszebra/pgn-splitter.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/chesszebra/pgn-splitter
[link-travis]: https://travis-ci.org/chesszebra/pgn-splitter
[link-scrutinizer]: https://scrutinizer-ci.com/g/chesszebra/pgn-splitter/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/chesszebra/pgn-splitter
[link-downloads]: https://packagist.org/packages/chesszebra/pgn-splitter
[link-contributors]: ../../contributors
[link-hackerone]: https://hackerone.com/chesszebra
