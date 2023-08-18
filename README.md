# Basic PHP Project Setup

This is a basic project skeleton for a simple PHP Project using a few Symfony components. 
The basic setup includes:

- PHPUnit for unit testing
- Behat for BDD/acceptance testing
- PHPStan for static analysis
- PHP CS Fixer for code cleanup
- Makefile for running tests and static analysis

## Setup

### Requirements

- PHP 8.2 (Installation instructions for [Windows](https://www.sitepoint.com/how-to-install-php-on-windows/))
- Composer (Installation instructions for [Windows](https://getcomposer.org/doc/00-intro.md#installation-windows))
- Symfony CLI (Optional, [installation instructions](https://symfony.com/download))) 

### Installation

In the project root directory, run `composer install` to install the dependencies. That's it!

### Makefile

- To run unit tests, run `make unit-test`
- To run Behat (Cucumber) tests, run `make behat-test`
- To clean up code and run static analysis, run `make coding-standard-fix && make static-analysis`