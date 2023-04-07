# Shopping Register

[Laravel](https://laravel.com/) application, experiment for [livewire](https://laravel-livewire.com/) based rendering and character recognition with [Tesseract OCR](https://github.com/tesseract-ocr/tesseract).

This application could be used to store the data  - extracted from an image of a receipt - in a mysql database. On the `/receipt-scan` page, you can choose and upload an image, crop it if necessary, then you select a parser application and then setup the form prefilled with the text extracted from the image.

## Features

- Create basket, shop, company, items on the `/receipt-scan` page.
- Create / edit / delete addresses on the `/address` page.
- Create / edit / delete items on the `/item` page.
- Create / edit / delete companies on the `/company` page.
- Create / edit / delete shops on the `/shop` page.
- Create / edit / delete baskets on the `/basket` page.
- Dashboard about various statistics on the `/home` page.
- Laravel based registration / login.
- Visibility restrictions, only own baskets could be changed.

## Database

### Address

Stores the addresses
- Id
- Timestamps (create, update)
- Raw - the fixed value extracted from the image. It is used for calculating levenshtein distances

### Company

Stores a company.
- Id
- Timestamps (create, update)
- Name
- Address
- Tax number (uniq)

### Shop

Stores a shop,
- Id
- Timestamps (create, update)
- Name
- Company
- Address

### Basket

Stores the receipt main informations.
- Id
- Timestamps (create, update)
- Time of the shopping
- Shop
- Total item price
- Receipt identifier
- User identifier
- Receipt URL - fragmant for the image of the receipt.

### Items

Stores an item that is bought somewhere.
- Id
- Timestamps (create, update)
- Name

### Basket items

Stores the items that were in a given basket
- Id
- Basket
- Item
- Price

## Basket Extraction

To be able to understand what is in the extracted texts, we need to provide parser applications.
These applications are responsible for providing a Scanned basket that represents the receipt.

The `config/basketextractor.php` contains the list of the parser applications. Feel free to implement further parsers. When you want to use a new parser, you only have to register it in the config.
Simply add a new entry to the array. The pattern and word files has to be located under the `tesseract-user-patterns` directory.

```php
[
    'myParserKey' => [
        'label' => 'My Ultimate Parser',
        'parser' => \App\Services\Parser\UltimateParserService::class,
        'config' => [
            'lang' => 'eng',
            'user-pattern-file' => 'my-patterns.txt',
            'user-words-file' => 'my-words.txt',
            'psm' => 4,
            'oem' => 3,
        ],
    ],
]
```

## Start the application.

- Get the codebase. Clone the repository.
- Get dependencies. Execute `composer install`. This step is necessary to have the `sail` tool. If you are not using it, you can execute it after the container start.
- Create .env file. There is an example provided, you can start with `cp .env.example .env`.
- Start application containers. Execute `./vendor/bin/sail up -d`.
- Generate key for the application. Execute `./vendor/bin/sail artisan key:generate`.
- Setup the database. Execute `./vendor/bin/sail artisan migrate`.
- Add data to database. Execute `./vendor/bin/sail artisan migrate --seed`.
- Setup frontend dependencies. Execute `./vendor/bin/sail npm install`.
- Build and watch frontend resources. Execute `./vendor/bin/sail npm run dev`.

## Setup OCR languages

Currently it supports `eng` and `hun`.

If you need further languages for the OCR application, you have to add it to the Dockerfile (docker/tesseract-ocr/Dockerfile). The tesseract and language installation is in line 47.
