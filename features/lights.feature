Feature: Lights

  Scenario: A point light has a position and intensity
    Given intensity is a color(1, 1, 1)
    And position is a point(0, 0, 0)
    When light is a point_light(position, intensity)
    Then light.position = position
    And light.intensity = intensity
