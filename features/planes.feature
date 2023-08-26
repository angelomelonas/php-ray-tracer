Feature: Planes

  Scenario: The normal of a plane is constant everywhere
    Given p is a plane()
    When n1 is a local_normal_at(p, point(0, 0, 0))
    And n2 is a local_normal_at(p, point(10, 0, -10))
    And n3 is a local_normal_at(p, point(-5, 0, 150))
    Then n1 = vector(0, 1, 0)
    And n2 = vector(0, 1, 0)
    And n3 = vector(0, 1, 0)

  Scenario: Intersect with a ray parallel to the plane
    Given p is a plane()
    And r is a ray(point(0, 10, 0), vector(0, 0, 1))
    When lxs is a local_intersect(p, r)
    Then lxs is empty

  Scenario: Intersect with a coplanar ray
    Given p is a plane()
    And r is a ray(point(0, 0, 0), vector(0, 0, 1))
    When lxs is a local_intersect(p, r)
    Then lxs is empty

  Scenario: A ray intersecting a plane from above
    Given p is a plane()
    And r is a ray(point(0, 1, 0), vector(0, -1, 0))
    When lxs is a local_intersect(p, r)
    Then lxs.count = 1
    And lxs[0].t = 1
    And lxs[0].object = p

  Scenario: A ray intersecting a plane from below
    Given p is a plane()
    And r is a ray(point(0, -1, 0), vector(0, 1, 0))
    When lxs is a local_intersect(p, r)
    Then lxs.count = 1
    And lxs[0].t = 1
    And lxs[0].object = p
