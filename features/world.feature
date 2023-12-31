Feature: World

  Scenario: Creating a world
    Given w is a world()
    Then w contains no objects
    And w has no light source

  Scenario: The default world
    Given light is a point_light(point(-10, 10, -10), color(1, 1, 1))
    And s1 is a sphere() with:
      | material.color     | (0.8, 1.0, 0.6)        |
      | material.diffuse   | 0.7                    |
      | material.specular  | 0.2                    |
    And s2 is a sphere() with:
      | transform | scaling(0.5, 0.5, 0.5) |
    When w is a default_world()
    Then w.light = light
    And w contains s1
    And w contains s2

  Scenario: Intersect a world with a ray
    Given w is a default_world()
    And r is a ray(point(0, 0, -5), vector(0, 0, 1))
    When xs is the intersections of intersect_world(w, r)
    Then xs.count = 4
    And xs[0].t = 4
    And xs[1].t = 4.5
    And xs[2].t = 5.5
    And xs[3].t = 6

  Scenario: Shading an intersection
    Given w is a default_world()
    And r is a ray(point(0, 0, -5), vector(0, 0, 1))
    And shape is the first object in w
    And i is a intersection(4, shapeA)
    When comps is a prepare_computations(i, r)
    And world_c is a shade_hit(w, comps)
    Then world_c = color(0.38066, 0.47583, 0.2855)

  Scenario: Shading an intersection from the inside
    Given w is a default_world()
    And w.light is a point_light(point(0, 0.25, 0), color(1, 1, 1))
    And r is a ray(point(0, 0, 0), vector(0, 0, 1))
    And shape is the second object in w
    And i is a intersection(0.5, shapeB)
    When comps is a prepare_computations(i, r)
    And world_c is a shade_hit(w, comps)
    Then world_c = color(0.90498, 0.90498, 0.90498)

  Scenario: The color when a ray misses
    Given w is a default_world()
    And r is a ray(point(0, 0, -5), vector(0, 1, 0))
    When world_c is a color_at(w, r)
    Then world_c = color(0, 0, 0)

  Scenario: The color when a ray hits
    Given w is a default_world()
    And r is a ray(point(0, 0, -5), vector(0, 0, 1))
    When world_c is a color_at(w, r)
    Then world_c = color(0.38066, 0.47583, 0.2855)

  Scenario: The color with an intersection behind the ray
    Given w is a default_world()
    And outer is the first object in w
    And outer.material.ambient is 1
    And inner is the second object in w
    And inner.material.ambient is a 1
    And r is a ray(point(0, 0, 0.75), vector(0, 0, -1))
    When world_c is a color_at(w, r)
    Then world_c = inner.material.color

  Scenario: There is no shadow when nothing is collinear with point and light
    Given w is a default_world()
    And p is a point(0, 10, 0)
    Then is_shadowed(w, p) is false

  Scenario: The shadow when an object is between the point and the light
    Given w is a default_world()
    And p is a point(10, -10, 10)
    Then is_shadowed(w, p) is true

  Scenario: There is no shadow when an object is behind the light
    Given w is a default_world()
    And p is a point(-20, 20, -20)
    Then is_shadowed(w, p) is false

  Scenario: There is no shadow when an object is behind the point
    Given w is a default_world()
    And p is a point(-2, 2, -2)
    Then is_shadowed(w, p) is false

  Scenario: shade_hit() is given an intersection in shadow
    Given w is a world()
    And w.light is a point_light(point(0, 0, -10), color(1, 1, 1))
    And s1 is a sphere()
    And s1 is added to w
    And s2 is a sphere() with:
      | transform | translation(0, 0, 10) |
    And s2 is added to w
    And r is a ray(point(0, 0, 5), vector(0, 0, 1))
    And i is a intersection(4, s2)
    When comps is a prepare_computations(i, r)
    And world_c is a shade_hit(w, comps)
    Then world_c = color(0.1, 0.1, 0.1)

  Scenario: The reflected color for a nonreflective material
    Given w is a default_world()
    And r is a ray(point(0, 0, 0), vector(0, 0, 1))
    And shape is the second object in w
    And shape.material.ambient is 1
    And i is a intersection(1, shape)
    When comps is a prepare_computations(i, r)
    And color is a reflected_color(w, comps)
    Then color = color(0, 0, 0)

  Scenario: The reflected color for a reflective material
    Given w is a default_world()
    And plane is a plane() with:
      | material.reflective | 0.5                   |
      | transform           | translation(0, -1, 0) |
    And plane is added to w
    And r is a ray(point(0, 0, -3), vector(0, -√2/2, √2/2))
    And i is a intersection(√2, plane)
    When comps is a prepare_computations(i, r)
    And color is a reflected_color(w, comps)
    Then color = color(0.190331, 0.23791, 0.142748)

  Scenario: shade_hit() with a reflective material
    Given w is a default_world()
    And plane is a plane() with:
      | material.reflective | 0.5                   |
      | transform           | translation(0, -1, 0) |
    And plane is added to w
    And r is a ray(point(0, 0, -3), vector(0, -√2/2, √2/2))
    And i is a intersection(√2, plane)
    When comps is a prepare_computations(i, r)
    And color is a shade_hit(w, comps)
    Then color = color(0.876756, 0.92434, 0.829173)

  Scenario: color_at() with mutually reflective surfaces
    Given w is a world()
    And w.light is a point_light(point(0, 0, 0), color(1, 1, 1))
    And lower is a plane() with:
      | material.reflective | 1                     |
      | transform           | translation(0, -1, 0) |
    And lower is added to w
    And upper is a plane() with:
      | material.reflective | 1                    |
      | transform           | translation(0, 1, 0) |
    And upper is added to w
    And r is a ray(point(0, 0, 0), vector(0, 1, 0))
    Then color_at(w, r) should terminate successfully

  Scenario: The reflected color at the maximum recursive depth
    Given w is a default_world()
    And plane is a plane() with:
      | material.reflective | 0.5                   |
      | transform           | translation(0, -1, 0) |
    And plane is added to w
    And r is a ray(point(0, 0, -3), vector(0, -√2/2, √2/2))
    And i is a intersection(√2, shape)
    When comps is a prepare_computations(i, r)
    And color is a reflected_color(w, comps, 0)
    Then color = color(0, 0, 0)

  Scenario: The refracted color with an opaque surface
    Given w is a default_world()
    And shape is the first object in w
    And r is a ray(point(0, 0, -5), vector(0, 0, 1))
    And xs is a intersections(4:shape, 6:shape)
    When comps is a prepare_computations(xs[0], r, xs)
    And color is a refracted_color(w, comps, 5)
    Then color = color(0, 0, 0)

  Scenario: The refracted color at the maximum recursive depth
    Given w is a default_world()
    And shape is the first object in w
    And shape has:
      | material.transparency     | 1.0 |
      | material.refractive_index | 1.5 |
    And r is a ray(point(0, 0, -5), vector(0, 0, 1))
    And xs is a intersections(4:shape, 6:shape)
    When comps is a prepare_computations(xs[0], r, xs)
    And color is a refracted_color(w, comps, 0)
    Then color = color(0, 0, 0)

  Scenario: The refracted color under total internal reflection
    Given w is a default_world()
    And shape is the first object in w
    And shape has:
      | material.transparency     | 1.0 |
      | material.refractive_index | 1.5 |
    And r is a ray(point(0, 0, √2/2), vector(0, 1, 0))
    And xs is a intersections(-√2/2:shape, √2/2:shape)
  # NOTE: this time you're inside the sphere, so you need
  # to look at the second intersection, xs[1], not xs[0]
    When comps is a prepare_computations(xs[1], r, xs)
    And color is a refracted_color(w, comps, 5)
    Then color = color(0, 0, 0)

  Scenario: The refracted color with a refracted ray
    Given w is a default_world()
    And A is the first object in w
    And A has:
      | material.ambient | 1.0            |
      | material.pattern | test_pattern() |
    And B is the second object in w
    And B has:
      | material.transparency     | 1.0 |
      | material.refractive_index | 1.5 |
    And r is a ray(point(0, 0, 0.1), vector(0, 1, 0))
    And xs is a intersections(-0.9899:A, -0.4899:B, 0.4899:B, 0.9899:A)
    When comps is a prepare_computations(xs[2], r, xs)
    And color is a refracted_color(w, comps, 5)
    Then color = color(0, 0.998883, 0.047216)

  Scenario: shade_hit() with a transparent material
    Given w is a default_world()
    And floor is a plane() with:
      | transform                 | translation(0, -1, 0) |
      | material.transparency     | 0.5                   |
      | material.refractive_index | 1.5                   |
    And floor is added to w
    And ball is a sphere() with:
      | material.color     | (1, 0, 0)                  |
      | material.ambient   | 0.5                        |
      | transform          | translation(0, -3.5, -0.5) |
    And ball is added to w
    And r is a ray(point(0, 0, -3), vector(0, -√2/2, √2/2))
    And xs is a intersections(√2:floor)
    When comps is a prepare_computations(xs[0], r, xs)
    And color is a shade_hit(w, comps, 5)
    Then color = color(0.9364253, 0.6864253, 0.6864253)

  Scenario: shade_hit() with a reflective, transparent material
    Given w is a default_world()
    And r is a ray(point(0, 0, -3), vector(0, -√2/2, √2/2))
    And floor is a plane() with:
      | transform                 | translation(0, -1, 0) |
      | material.reflective       | 0.5                   |
      | material.transparency     | 0.5                   |
      | material.refractive_index | 1.5                   |
    And floor is added to w
    And ball is a sphere() with:
      | material.color     | (1, 0, 0)                  |
      | material.ambient   | 0.5                        |
      | transform          | translation(0, -3.5, -0.5) |
    And ball is added to w
    And xs is a intersections(√2:floor)
    When comps is a prepare_computations(xs[0], r, xs)
    And color is a shade_hit(w, comps, 5)
    Then color = color(0.933915, 0.696434, 0.692431)
