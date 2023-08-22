Feature: Spheres

  Scenario: A ray intersects a sphere at two points
    Given r is a ray(point(0, 0, -5), vector(0, 0, 1))
    And s is a sphere()
    When xs is a intersect(s, r)
    Then xs.count = 2
    And xs[0] = 4.0
    And xs[1] = 6.0

  Scenario: A ray intersects a sphere at a tangent
    Given r is a ray(point(0, 1, -5), vector(0, 0, 1))
    And s is a sphere()
    When xs is a intersect(s, r)
    Then xs.count = 2
    And xs[0] = 5.0
    And xs[1] = 5.0

  Scenario: A ray misses a sphere
    Given r is a ray(point(0, 2, -5), vector(0, 0, 1))
    And s is a sphere()
    When xs is a intersect(s, r)
    Then xs.count = 0

  Scenario: A ray originates inside a sphere
    Given r is a ray(point(0, 0, 0), vector(0, 0, 1))
    And s is a sphere()
    When xs is a intersect(s, r)
    Then xs.count = 2
    And xs[0] = -1.0
    And xs[1] = 1.0

  Scenario: A sphere is behind a ray
    Given r is a ray(point(0, 0, 5), vector(0, 0, 1))
    And s is a sphere()
    When xs is a intersect(s, r)
    Then xs.count = 2
    And xs[0] = -6.0
    And xs[1] = -4.0

  Scenario: Intersect sets the object on the intersection
    Given r is a ray(point(0, 0, -5), vector(0, 0, 1))
    And s is a sphere()
    When xs is a intersect(s, r)
    Then xs.count = 2
    And xs[0].object = s
    And xs[1].object = s

  Scenario: A sphere's default transformation
    Given s is a sphere()
    Then s.transform = identity_matrix

  Scenario: Changing a sphere's transformation
    Given s is a sphere()
    And t is a translation(2, 3, 4)
    When set_transform(s, t)
    Then s.transform = t

  Scenario: Intersecting a scaled sphere with a ray
    Given r is a ray(point(0, 0, -5), vector(0, 0, 1))
    And s is a sphere()
    When set_transform(s, scaling(2, 2, 2))
    And xs is a intersect(s, r)
    Then xs.count = 2
    And xs[0].t = 3
    And xs[1].t = 7

  Scenario: Intersecting a translated sphere with a ray
    Given r is a ray(point(0, 0, -5), vector(0, 0, 1))
    And s is a sphere()
    When set_transform(s, translation(5, 0, 0))
    And xs is a intersect(s, r)
    Then xs.count = 0

  Scenario: The normal on a sphere at a point on the x axis
    Given s is a sphere()
    When n is a normal_at(s, point(1, 0, 0))
    Then n = vector(1, 0, 0)

  Scenario: The normal on a sphere at a point on the y axis
    Given s is a sphere()
    When n is a normal_at(s, point(0, 1, 0))
    Then n = vector(0, 1, 0)

  Scenario: The normal on a sphere at a point on the z axis
    Given s is a sphere()
    When n is a normal_at(s, point(0, 0, 1))
    Then n = vector(0, 0, 1)

  Scenario: The normal on a sphere at a nonaxial point
    Given s is a sphere()
    When n is a normal_at(s, point(√3/3, √3/3, √3/3))
    Then n = vector(√3/3, √3/3, √3/3)

  Scenario: The normal is a normalized vector
    Given s is a sphere()
    When n is a normal_at(s, point(√3/3, √3/3, √3/3))
    Then n = normalize(n)

  Scenario: Computing the normal on a translated sphere
    Given s is a sphere()
    And set_transform(s, translation(0, 1, 0))
    When n is a normal_at(s, point(0, 1.70711, -0.70711))
    Then n = vector(0, 0.70711, -0.70711)

  Scenario: Computing the normal on a transformed sphere
    Given s is a sphere()
    And m is a scaling(1, 0.5, 1) * rotation_z(π/5)
    And set_transform(s, m)
    When n is a normal_at(s, point(0, √2/2, -√2/2))
    Then n = vector(0, 0.97014, -0.24254)

  Scenario: A sphere has a default material
    Given s is a sphere()
    When m is a s.material
    Then m = material()

  Scenario: A sphere may be assigned a material
    Given s is a sphere()
    And m is a material()
    And m.ambient is a 1
    When s.material is a m
    Then s.material = m

#  Scenario: A helper for producing a sphere with a glassy material
#    Given s is a glass_sphere()
#    Then s.transform = identity_matrix
#    And s.material.transparency = 1.0
#    And s.material.refractive_index = 1.5
