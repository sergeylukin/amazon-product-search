# Amazon product search [![Build Status](https://travis-ci.org/sergeylukin/amazon-product-search.png?branch=master)](https://travis-ci.org/sergeylukin/amazon-product-search)

Library that allows searching for products on amazon.com store.

# Installation

Use composer to require in your `composer.json`:

```
"sergeylukin/amazon-product-search": "dev-master"
```

or just run `composer require sergeylukin/amazon-product-search`

# Usage

```php
<?php require __DIR__ . '/vendor/autoload.php';

use Sergeylukin\AmazonProductSearch as Amazon;

$Product = Amazon::factory()->findOneProductByKeyword('latte cup');

print_r($Product);
```

sample output:

```php
stdClass Object
(
 [title] => Best latte cup
 [price] => $12.05
 [image_uri] => /latte-cup.jpg
 [description] => The best latte cup you will ever drink latte from. Period.
)
```

# Development

Install [git](http://git-scm.com) and [composer](http://getcomposer.org)

```
git clone https://github.com/sergeylukin/amazon-product-search.git
composer install
```

Run tests:

```
./vendor/bin/codecept run
```

# License

MIT
