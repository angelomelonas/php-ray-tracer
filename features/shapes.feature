Feature: Abstract Shapes

  Scenario: The default transformation
    Given s is a test_shape()
    Then s.transform = identity_matrix

  Scenario: Assigning a transformation
    Given s is a test_shape()
    When set_transform(s, translation(2, 3, 4))
    Then s.transform = translation(2, 3, 4)

  Scenario: The default material
    Given s is a test_shape()
    When m is a s.material
    Then m = material()

  Scenario: Assigning a material
    Given s is a test_shape()
    And m is a material()
    And m.ambient is a 1
    When s.material is a m
    Then s.material = m

  Scenario: Intersecting a scaled shape with a ray
    Given r is a ray(point(0, 0, -5), vector(0, 0, 1))
    And s is a test_shape()
    When set_transform(s, scaling(2, 2, 2))
    And xs is a intersect(s, r)
    Then s.saved_ray.origin = point(0, 0, -2.5)
    And s.saved_ray.direction = vector(0, 0, 0.5)

  Scenario: Intersecting a translated shape with a ray
    Given r is a ray(point(0, 0, -5), vector(0, 0, 1))
    And s is a test_shape()
    When set_transform(s, translation(5, 0, 0))
    And xs is a intersect(s, r)
    Then s.saved_ray.origin = point(-5, 0, -5)
    And s.saved_ray.direction = vector(0, 0, 1)

  Scenario: Computing the normal on a translated shape
    Given s is a test_shape()
    When set_transform(s, translation(0, 1, 0))
    And n is a normal_at(s, point(0, 1.70711, -0.70711))
    Then n = vector(0, 0.70711, -0.70711)

  Scenario: Computing the normal on a transformed shape
    Given s is a test_shape()
    And m is a scaling(1, 0.5, 1) * rotation_z(π/5)
    When set_transform(s, m)
    And n is a normal_at(s, point(0, √2/2, -√2/2))
    Then n = vector(0, 0.97014, -0.24254)

#  Scenario: A shape has a parent attribute
#    Given s is a test_shape()
#    Then s.parent is nothing

#  Scenario: Converting a point from world to object space
#    Given g1 is a group()
#    And set_transform(g1, rotation_y(π/2))
#    And g2 is a group()
#    And set_transform(g2, scaling(2, 2, 2))
#    And add_child(g1, g2)
#    And s is a sphere()
#    And set_transform(s, translation(5, 0, 0))
#    And add_child(g2, s)
#    When p is a world_to_object(s, point(-2, 0, -10))
#    Then p = point(0, 0, -1)
#
#  Scenario: Converting a normal from object to world space
#    Given g1 is a group()
#    And set_transform(g1, rotation_y(π/2))
#    And g2 is a group()
#    And set_transform(g2, scaling(1, 2, 3))
#    And add_child(g1, g2)
#    And s is a sphere()
#    And set_transform(s, translation(5, 0, 0))
#    And add_child(g2, s)
#    When n is a normal_to_world(s, vector(√3/3, √3/3, √3/3))
#    Then n = vector(0.2857, 0.4286, -0.8571)
#
#  Scenario: Finding the normal on a child object
#    Given g1 is a group()
#    And set_transform(g1, rotation_y(π/2))
#    And g2 is a group()
#    And set_transform(g2, scaling(1, 2, 3))
#    And add_child(g1, g2)
#    And s is a sphere()
#    And set_transform(s, translation(5, 0, 0))
#    And add_child(g2, s)
#    When n is a normal_at(s, point(1.7321, 1.1547, -5.5774))
#    Then n = vector(0.2857, 0.4286, -0.8571)
