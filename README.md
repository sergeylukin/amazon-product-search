# Amazon product search [![Build Status](https://travis-ci.org/sergeylukin/amazon-product-search.png?branch=master)](https://travis-ci.org/sergeylukin/amazon-product-search)

Library that allows searching for products on amazon.com store.

# Installation

Use composer to require in your `composer.json`:

```
"sergeylukin/amazon-product-search": "dev-master"
```

or just run `composer require sergeylukin/amazon-product-search`

# Usage

```
use Sergeylukin\AmazonProductSearch as Amazon;

$Product = Amazon::factory()->findOneProductByKeyword('latte cup');

print_r($Product);
```

outputs:

```
stdClass Object
(
 [title] => Best latte cup
 [price] => $12.05
 [image_uri] => /latte-cup.jpg
 [description] => The best latte cup you will ever drink latte from. Period.
)
```

# License

MIT
