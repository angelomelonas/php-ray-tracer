Feature: Cylinders

Scenario Outline: A ray misses a cylinder
  Given cyl is a cylinder()
    And direction is a normalize(<direction>)
    And r is a ray(<origin>, direction)
  When lxs is a local_intersect(cyl, r)
  Then lxs.count = 0

  Examples:
    | origin          | direction       |
    | point(1, 0, 0)  | vector(0, 1, 0) |
    | point(0, 0, 0)  | vector(0, 1, 0) |
    | point(0, 0, -5) | vector(1, 1, 1) |

Scenario Outline: A ray strikes a cylinder
  Given cyl is a cylinder()
    And direction is a normalize(<direction>)
    And r is a ray(<origin>, direction)
  When lxs is a local_intersect(cyl, r)
  Then lxs.count = 2
    And lxs[0].t = <t0>
    And lxs[1].t = <t1>

  Examples:
    | origin            | direction         | t0      | t1      |
    | point(1, 0, -5)   | vector(0, 0, 1)   | 5       | 5       |
    | point(0, 0, -5)   | vector(0, 0, 1)   | 4       | 6       |
    | point(0.5, 0, -5) | vector(0.1, 1, 1) | 6.807982 | 7.088723 |

Scenario Outline: Normal vector on a cylinder
  Given cyl is a cylinder()
  When normal is a local_normal_at(cyl, <point>)
  Then normal = <normal>

  Examples:
    | point           | normal           |
    | point(1, 0, 0)  | vector(1, 0, 0)  |
    | point(0, 5, -1) | vector(0, 0, -1) |
    | point(0, -2, 1) | vector(0, 0, 1)  |
    | point(-1, 1, 0) | vector(-1, 0, 0) |

Scenario: The default minimum and maximum for a cylinder
  Given cyl is a cylinder()
  Then cyl.minimum = -infinity
    And cyl.maximum = infinity

Scenario Outline: Intersecting a constrained cylinder
  Given cyl is a cylinder()
    And cyl.minimum is 1
    And cyl.maximum is 2
    And direction is a normalize(<direction>)
    And r is a ray(<point>, direction)
  When lxs is a local_intersect(cyl, r)
  Then lxs.count = <count>

  Examples:
    |   | point             | direction         | count |
    | 1 | point(0, 1.5, 0)  | vector(0.1, 1, 0) | 0     |
    | 2 | point(0, 3, -5)   | vector(0, 0, 1)   | 0     |
    | 3 | point(0, 0, -5)   | vector(0, 0, 1)   | 0     |
    | 4 | point(0, 2, -5)   | vector(0, 0, 1)   | 0     |
    | 5 | point(0, 1, -5)   | vector(0, 0, 1)   | 0     |
    | 6 | point(0, 1.5, -2) | vector(0, 0, 1)   | 2     |

Scenario: The default closed value for a cylinder
  Given cyl is a cylinder()
  Then cyl.closed = false

Scenario Outline: Intersecting the caps of a closed cylinder
  Given cyl is a cylinder()
    And cyl.minimum is 1
    And cyl.maximum is 2
    And cyl.closed is true
    And direction is a normalize(<direction>)
    And r is a ray(<point>, direction)
  When lxs is a local_intersect(cyl, r)
  Then lxs.count = <count>

  Examples:
    |   | point            | direction        | count |
    | 1 | point(0, 3, 0)   | vector(0, -1, 0) | 2     |
    | 2 | point(0, 3, -2)  | vector(0, -1, 2) | 2     |
    # corner case
    | 3 | point(0, 4, -2)  | vector(0, -1, 1) | 2     |
    | 4 | point(0, 0, -2)  | vector(0, 1, 2)  | 2     |
    # corner case
    | 5 | point(0, -1, -2) | vector(0, 1, 1)  | 2     |

Scenario Outline: The normal vector on a cylinder's end caps
  Given cyl is a cylinder()
    And cyl.minimum is 1
    And cyl.maximum is 2
    And cyl.closed is true
  When normal is a local_normal_at(cyl, <point>)
  Then normal = <normal>

  Examples:
    | point            | normal           |
    | point(0, 1, 0)   | vector(0, -1, 0) |
    | point(0.5, 1, 0) | vector(0, -1, 0) |
    | point(0, 1, 0.5) | vector(0, -1, 0) |
    | point(0, 2, 0)   | vector(0, 1, 0)  |
    | point(0.5, 2, 0) | vector(0, 1, 0)  |
    | point(0, 2, 0.5) | vector(0, 1, 0)  |
