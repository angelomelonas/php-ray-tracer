Feature: Patterns

  Background:
    Given black is a color(0, 0, 0)
    And white is a color(1, 1, 1)

  Scenario: Creating a stripe pattern
    Given pattern is a stripe_pattern(white, black)
    Then pattern.a = white
    And pattern.b = black

  Scenario: A stripe pattern is constant in y
    Given pattern is a stripe_pattern(white, black)
    Then stripe_at(pattern, point(0, 0, 0)) = white
    And stripe_at(pattern, point(0, 1, 0)) = white
    And stripe_at(pattern, point(0, 2, 0)) = white

  Scenario: A stripe pattern is constant in z
    Given pattern is a stripe_pattern(white, black)
    Then stripe_at(pattern, point(0, 0, 0)) = white
    And stripe_at(pattern, point(0, 0, 1)) = white
    And stripe_at(pattern, point(0, 0, 2)) = white

  Scenario: A stripe pattern alternates in x
    Given pattern is a stripe_pattern(white, black)
    Then stripe_at(pattern, point(0, 0, 0)) = white
    And stripe_at(pattern, point(0.9, 0, 0)) = white
    And stripe_at(pattern, point(1, 0, 0)) = black
    And stripe_at(pattern, point(-0.1, 0, 0)) = black
    And stripe_at(pattern, point(-1, 0, 0)) = black
    And stripe_at(pattern, point(-1.1, 0, 0)) = white

  Scenario: Stripes with an object transformation
    Given object is a sphere()
    And set_transform(object, scaling(2, 2, 2))
    And pattern is a stripe_pattern(white, black)
    When c is a stripe_at_object(pattern, object, point(1.5, 0, 0))
    Then c = white

  Scenario: Stripes with a pattern transformation
    Given object is a sphere()
    And pattern is a stripe_pattern(white, black)
    And set_pattern_transform(pattern, scaling(2, 2, 2))
    When c is a stripe_at_object(pattern, object, point(1.5, 0, 0))
    Then c = white

  Scenario: Stripes with both an object and a pattern transformation
    Given object is a sphere()
    And set_transform(object, scaling(2, 2, 2))
    And pattern is a stripe_pattern(white, black)
    And set_pattern_transform(pattern, translation(0.5, 0, 0))
    When c is a stripe_at_object(pattern, object, point(2.5, 0, 0))
    Then c = white

  Scenario: The default pattern transformation
    Given pattern is a test_pattern()
    Then pattern.transform = identity_matrix

  Scenario: Assigning a transformation
    Given pattern is a test_pattern()
    When set_pattern_transform(pattern, translation(1, 2, 3))
    Then pattern.transform = translation(1, 2, 3)

  Scenario: A pattern with an object transformation
    Given shape is a sphere()
    And set_transform(shape, scaling(2, 2, 2))
    And pattern is a test_pattern()
    When c is a pattern_at_shape(pattern, shape, point(2, 3, 4))
    Then c = color(1, 1.5, 2)

  Scenario: A pattern with a pattern transformation
    Given shape is a sphere()
    And pattern is a test_pattern()
    And set_pattern_transform(pattern, scaling(2, 2, 2))
    When c is a pattern_at_shape(pattern, shape, point(2, 3, 4))
    Then c = color(1, 1.5, 2)

  Scenario: A pattern with both an object and a pattern transformation
    Given shape is a sphere()
    And set_transform(shape, scaling(2, 2, 2))
    And pattern is a test_pattern()
    And set_pattern_transform(pattern, translation(0.5, 1, 1.5))
    When c is a pattern_at_shape(pattern, shape, point(2.5, 3, 3.5))
    Then c = color(0.75, 0.5, 0.25)

  Scenario: A gradient linearly interpolates between colors
    Given pattern is a gradient_pattern(white, black)
    Then pattern_at(pattern, point(0, 0, 0)) = white
    And pattern_at(pattern, point(0.25, 0, 0)) = color(0.75, 0.75, 0.75)
    And pattern_at(pattern, point(0.5, 0, 0)) = color(0.5, 0.5, 0.5)
    And pattern_at(pattern, point(0.75, 0, 0)) = color(0.25, 0.25, 0.25)

  Scenario: A ring should extend in both x and z
    Given pattern is a ring_pattern(white, black)
    Then pattern_at(pattern, point(0, 0, 0)) = white
    And pattern_at(pattern, point(1, 0, 0)) = black
    And pattern_at(pattern, point(0, 0, 1)) = black
    # 0.708 = just slightly more than √2/2
    And pattern_at(pattern, point(0.708, 0, 0.708)) = black

  Scenario: Checkers should repeat in x
    Given pattern is a checkers_pattern(white, black)
    Then pattern_at(pattern, point(0, 0, 0)) = white
    And pattern_at(pattern, point(0.99, 0, 0)) = white
    And pattern_at(pattern, point(1.01, 0, 0)) = black

  Scenario: Checkers should repeat in y
    Given pattern is a checkers_pattern(white, black)
    Then pattern_at(pattern, point(0, 0, 0)) = white
    And pattern_at(pattern, point(0, 0.99, 0)) = white
    And pattern_at(pattern, point(0, 1.01, 0)) = black

  Scenario: Checkers should repeat in z
    Given pattern is a checkers_pattern(white, black)
    Then pattern_at(pattern, point(0, 0, 0)) = white
    And pattern_at(pattern, point(0, 0, 0.99)) = white
    And pattern_at(pattern, point(0, 0, 1.01)) = black
