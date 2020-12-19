Feature: Procès-Verbal

@createSchema
  Scenario: I can add a new PVI
    Given I am logged in as "admin"
    Given I am on "/suivi/procesverbal/ajouter/5"
    Then the response status code should be 200
    When I fill "Version du document" with "1"
    When I select "14" from "Signataire Teknik studio"
    When I fill "Date de Signature du document" with "2018-03-03"
    And I press "Valider de Procès-Verbal"
    Then the url should match "/suivi/etude"
    And I should see "PV ajouté"
    
  Scenario: I can write a new PVR
    Given I am logged in as "admin"
    Given I am on "/suivi/procesverbal/rediger/5/pvr"
    Then the response status code should be 200
    When I fill "Version du document" with "1"
    When I select "14" from "Signataire Teknik studio"
    When I fill "Date de Signature du document" with "2018-03-03"
    And I press "Enregistrer"
    Then the url should match "/suivi/etude"
    And I should see "PV rédigé"
    
    
  Scenario: I can edit a PVI
    Given I am logged in as "admin"
    Given I am on "/suivi/procesverbal/modifier/4"
    Then the response status code should be 200
    When I fill "Version du document" with "1"
    When I select "14" from "Signataire Teknik studio"
    When I fill "Date de Signature du document" with "2018-03-03"
    And I press "Enregistrer le Procès-Verbal"
    Then the url should match "/suivi/etude"
    And I should see "PV modifié"
    
    
  Scenario: I can delete a PVI
    Given I am logged in as "admin"
    Given I am on "/suivi/procesverbal/modifier/4"
    Then the response status code should be 200
    And I press "Supprimer de Procès-Verbal"
    Then I should see "Etes-vous sûr de vouloir supprimer définitivement ce PV ?"
    And I press "OK"
    Then the url should match "/suivi/etude"
    And I should see "PV supprimé"
