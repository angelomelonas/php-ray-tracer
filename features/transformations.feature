Feature: Matrix Transformations

  Scenario: Multiplying by a translation matrix
    Given transform is a translation(5, -3, 2)
    And p is a point(-3, 4, 5)
    Then transform * p = point(2, 1, 7)

  Scenario: Multiplying by the inverse of a translation matrix
    Given transform is a translation(5, -3, 2)
    And inv is a inverse(transform)
    And p is a point(-3, 4, 5)
    Then inv * p = point(-8, 7, 3)

  Scenario: Translation does not affect vectors
    Given transform is a translation(5, -3, 2)
    And v is a vector(-3, 4, 5)
    Then transform * v = v

  Scenario: A scaling matrix applied to a point
    Given transform is a scaling(2, 3, 4)
    And p is a point(-4, 6, 8)
    Then transform * p = point(-8, 18, 32)

  Scenario: A scaling matrix applied to a vector
    Given transform is a scaling(2, 3, 4)
    And v is a vector(-4, 6, 8)
    Then transform * v = vector(-8, 18, 32)

  Scenario: Multiplying by the inverse of a scaling matrix
    Given transform is a scaling(2, 3, 4)
    And inv is a inverse(transform)
    And v is a vector(-4, 6, 8)
    Then inv * v = vector(-2, 2, 2)

  Scenario: Reflection is scaling by a negative value
    Given transform is a scaling(-1, 1, 1)
    And p is a point(2, 3, 4)
    Then transform * p = point(-2, 3, 4)

  Scenario: Rotating a point around the x axis
    Given p is a point(0, 1, 0)
    And half_quarter is a rotation_x(π / 4)
    And full_quarter is a rotation_x(π / 2)
    Then half_quarter * p = point(0, √2/2, √2/2)
    And full_quarter * p = point(0, 0, 1)

  Scenario: The inverse of an x-rotation rotates in the opposite direction
    Given p is a point(0, 1, 0)
    And half_quarter is a rotation_x(π / 4)
    And inv is a inverse(half_quarter)
    Then inv * p = point(0, √2/2, -√2/2)

  Scenario: Rotating a point around the y axis
    Given p is a point(0, 0, 1)
    And half_quarter is a rotation_y(π / 4)
    And full_quarter is a rotation_y(π / 2)
    Then half_quarter * p = point(√2/2, 0, √2/2)
    And full_quarter * p = point(1, 0, 0)

  Scenario: Rotating a point around the z axis
    Given p is a point(0, 1, 0)
    And half_quarter is a rotation_z(π / 4)
    And full_quarter is a rotation_z(π / 2)
    Then half_quarter * p = point(-√2/2, √2/2, 0)
    And full_quarter * p = point(-1, 0, 0)

  Scenario: A shearing transformation moves x in proportion to y
    Given transform is a shearing(1, 0, 0, 0, 0, 0)
    And p is a point(2, 3, 4)
    Then transform * p = point(5, 3, 4)

  Scenario: A shearing transformation moves x in proportion to z
    Given transform is a shearing(0, 1, 0, 0, 0, 0)
    And p is a point(2, 3, 4)
    Then transform * p = point(6, 3, 4)

  Scenario: A shearing transformation moves y in proportion to x
    Given transform is a shearing(0, 0, 1, 0, 0, 0)
    And p is a point(2, 3, 4)
    Then transform * p = point(2, 5, 4)

  Scenario: A shearing transformation moves y in proportion to z
    Given transform is a shearing(0, 0, 0, 1, 0, 0)
    And p is a point(2, 3, 4)
    Then transform * p = point(2, 7, 4)

  Scenario: A shearing transformation moves z in proportion to x
    Given transform is a shearing(0, 0, 0, 0, 1, 0)
    And p is a point(2, 3, 4)
    Then transform * p = point(2, 3, 6)

  Scenario: A shearing transformation moves z in proportion to y
    Given transform is a shearing(0, 0, 0, 0, 0, 1)
    And p is a point(2, 3, 4)
    Then transform * p = point(2, 3, 7)

  Scenario: Individual transformations are applied in sequence
    Given p is a point(1, 0, 1)
    And A is a rotation_x(π / 2)
    And B is a scaling(5, 5, 5)
    And C is a translation(10, 5, 7)
  # apply rotation first
    When p2 is a A * p
    Then p2 = point(1, -1, 0)
  # then apply scaling
    When p3 is a B * p2
    Then p3 = point(5, -5, 0)
  # then apply translation
    When p4 is a C * p3
    Then p4 = point(15, 0, 7)

  Scenario: Chained transformations must be applied in reverse order
    Given p is a point(1, 0, 1)
    And A is a rotation_x(π / 2)
    And B is a scaling(5, 5, 5)
    And C is a translation(10, 5, 7)
    When T is a C * B * A
    Then T * p = point(15, 0, 7)

  Scenario: The transformation matrix for the default orientation
    Given from is a point(0, 0, 0)
    And to is a point(0, 0, -1)
    And up is a vector(0, 1, 0)
    When t is a view_transform(from, to, up)
    Then t = identity_matrix

  Scenario: A view transformation matrix looking in positive z direction
    Given from is a point(0, 0, 0)
    And to is a point(0, 0, 1)
    And up is a vector(0, 1, 0)
    When t is a view_transform(from, to, up)
    Then t = scaling(-1, 1, -1)

  Scenario: The view transformation moves the world
    Given from is a point(0, 0, 8)
    And to is a point(0, 0, 0)
    And up is a vector(0, 1, 0)
    When t is a view_transform(from, to, up)
    Then t = translation(0, 0, -8)

  Scenario: An arbitrary view transformation
    Given from is a point(1, 3, 2)
    And to is a point(4, -2, 8)
    And up is a vector(1, 1, 0)
    When t is a view_transform(from, to, up)
    Then t is the following 4x4 matrix:
      | -0.50709 | 0.50709 |  0.67612 | -2.36643 |
      |  0.76772 | 0.60609 |  0.12122 | -2.82843 |
      | -0.35857 | 0.59761 | -0.71714 |  0.00000 |
      |  0.00000 | 0.00000 |  0.00000 |  1.00000 |
