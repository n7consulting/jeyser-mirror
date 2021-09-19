Feature: Etude
  I am able to CRUD an Etude.

  Scenario: I can see Etude pipeline
    Given I am logged in as "admin"
    When I go to "/suivi"
    Then the response status code should be 200
    Then I should see "Etudes en Négociation"

  Scenario: I can see an etude
    Given I am logged in as "admin"
    When I go to "/suivi/etude/315GLA"
    Then the response status code should be 200

  Scenario: I can edit AP & CC
    Given I am logged in as "admin"
    When I go to "/suivi/ap/rediger/1"
    Then the response status code should be 200
    When I go to "/suivi/cc/rediger/1"
    Then the response status code should be 200
    Then I fill in "Date de Signature du document" with "24/01/2018"
    Then I check "Le signataire client existe-t-il déjà dans la base de donnée ?"
    And I press "Enregistrer la CC"
    And the response status code should be 200

  Scenario: I can export Gantt Chart
	Given I am logged in as "admin"
    When I go to "/Documents/GetGantt/1"
    Then the response status code should be 200
