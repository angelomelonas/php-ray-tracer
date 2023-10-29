# The Ray Tracer Challenge (in PHP!)

This project follows [The Ray Tracer Challenge](http://raytracerchallenge.com/) by Jamis Buck.

PHP is definitely not the ideal language for implementing a ray tracer. 
However, it is still a great way to learn the basic concepts, which I will use as a stepping stone to learn other languages in the future.

## Example Renders

- Coming soon...

## Chapters

### Implemented 

- Chapter 1: Tuples, Points and Vectors
- Chapter 2: Drawing on a Canvas
- Chapter 3: Matrices
- Chapter 4: Matrix Transformations
- Chapter 5: Ray-Sphere Intersections
- Chapter 6: Light and Shading
- Chapter 7: Making a Scene
- Chapter 8: Shadows
- Chapter 9: Planes
- Chapter 10: Patterns
- Chapter 11: Reflections and Refraction
- Chapter 12: Cubes

### Todo

- Chapter 13: Cylinders
- Chapter 14: Groups
- Chapter 15: Triangles
- Chapter 16: Constructive Solid Geometry (CSG)
- Chapter 17: Nex Steps

#### Bonus (Optional)
- [Rendering Soft Shadows](http://raytracerchallenge.com/bonus/area-light.html)
- [Bounding Boxes and Hierarchies](http://raytracerchallenge.com/bonus/bounding-boxes.html)
- [Texture Mapping](http://raytracerchallenge.com/bonus/texture-mapping.html)

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
