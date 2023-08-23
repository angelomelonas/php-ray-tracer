Feature: Camera

  Scenario: Constructing a camera
    Given hsize is 160
    And vsize is 120
    And field_of_view is π/2
    When c is a camera(hsize, vsize, field_of_view)
    Then c.hsize = 160
    And c.vsize = 120
    And c.field_of_view = π/2
    And c.transform = identity_matrix

  Scenario: The pixel size for a horizontal canvas
    Given c is a camera(200, 125, π/2)
    Then c.pixel_size = 0.01

  Scenario: The pixel size for a vertical canvas
    Given c is a camera(125, 200, π/2)
    Then c.pixel_size = 0.01

  Scenario: Constructing a ray through the center of the canvas
    Given c is a camera(201, 101, π/2)
    When r is a ray_for_pixel(c, 100, 50)
    Then r.origin = point(0, 0, 0)
    And r.direction = vector(0, 0, -1)

  Scenario: Constructing a ray through a corner of the canvas
    Given c is a camera(201, 101, π/2)
    When r is a ray_for_pixel(c, 0, 0)
    Then r.origin = point(0, 0, 0)
    And r.direction = vector(0.66519, 0.33259, -0.66851)

  Scenario: Constructing a ray when the camera is transformed
    Given c is a camera(201, 101, π/2)
    When c.transform is a rotation_y(π/4) * translation(0, -2, 5)
    And r is a ray_for_pixel(c, 100, 50)
    Then r.origin = point(0, 2, -5)
    And r.direction = vector(√2/2, 0, -√2/2)

  Scenario: Rendering a world with a camera
    Given w is a default_world()
    And c is a camera(11, 11, π/2)
    And from is a point(0, 0, -5)
    And to is a point(0, 0, 0)
    And up is a vector(0, 1, 0)
    And c.transform is a view_transform(from, to, up)
    When image is a render(c, w)
    Then pixel_at(image, 5, 5) = color(0.38066, 0.47583, 0.2855)
