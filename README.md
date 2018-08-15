# Facebook Albums Uploads at Google Drive 

[![Build Status](https://scrutinizer-ci.com/g/mehta-harshit/photo-test/badges/build.png?b=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mehta-harshit/photo-test/badges/quality-score.png?b=master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/mehta-harshit/photo-test/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
This repository contains the open source Facebook API integration and Google Drive Sdk that allows you to access the Facebook Albums and uploads the albums to Google Drive  from your PHP app.

## Installation

## Facebook JAVASCRIPT SDK

It can be installed from [Facebook Guide]https://developers.facebook.com/docs/javascript/). 
It doesn't have any standalone files that need to be downloaded or installed, instead you simply need to include a short piece of regular JavaScript in your HTML that will asynchronously load the SDK into your pages.

## Google Drive SDK 

You can use **Composer** or simply **Download the Release**

### Composer

The preferred method is via [composer](https://getcomposer.org). Follow the
[installation instructions](https://getcomposer.org/doc/00-intro.md) if you do not already have
composer installed.

Once composer is installed, execute the following command in your project root to install this library:

```sh
composer require google/apiclient:"^2.0"
```

Finally, be sure to include the autoloader:

```php
require_once '/path/to/your-project/vendor/autoload.php';
```

### Download the Release

If you abhor using composer, you can download the package in its entirety. The [Releases](https://github.com/google/google-api-php-client/releases) page lists all stable versions. Download any file
with the name `google-api-php-client-[RELEASE_NAME].zip` for a package including this library and its dependencies.

Uncompress the zip file you download, and include the autoloader in your project:

```php
require_once '/path/to/google-api-php-client/vendor/autoload.php';
```

For additional installation and setup instructions, see [the documentation](https://developers.google.com/api-client-library/php/start/installation).
## Usage

> **Note:** This version of the Facebook Javascript SDK And Google Drive SDK for PHP requires PHP 5.4 or greater.

Complete documentation, installation instructions, and examples are available [here](docs/).

## Demo

[You can view a live, interactive version of this Gist here](https://www.staging.nystrading.com/photo/).


## Security Vulnerabilities

If you have found a security issue, please contact the maintainers directly at [mehta.harshit007@gmail.com](mailto:mehta.harshit007@gmail.com).
