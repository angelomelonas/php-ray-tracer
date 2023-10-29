# The Ray Tracer Challenge (in PHP!)

This project follows [The Ray Tracer Challenge](http://raytracerchallenge.com/) by Jamis Buck.

## Example Renders (Coming Soon)

## Chapters

### Implemented 

- Chapter 1
- Chapter 2
- Chapter 3
- Chapter 4
- Chapter 5
- Chapter 6
- Chapter 7
- Chapter 8
- Chapter 9
- Chapter 10
- Chapter 11
- Chapter 12

### Todo

- Chapter 13
- Chapter 14
- Chapter 15
- Chapter 16
- Chapter 17

## Setup

This project is a simple PHP Project using a few Symfony components. 
The basic project setup includes:

- PHPUnit for unit testing
- Behat for BDD/acceptance testing
- PHPStan for static analysis
- PHP CS Fixer for code cleanup
- Makefile for running tests and static analysis

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
