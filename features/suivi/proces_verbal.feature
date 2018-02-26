Feature: Procès-Verbal

@createSchema
  Scenario: I can add a new PVI
    Given I am logged in as "admin" or "suiveur" or "treso"
    Given I am on "/suivi/procesverbal/ajouter/{id}"
    Then the response status code should be 200
    When I fill "Version du document" with "1"
    When I fill "Signataire Junior" with the name of the signatory of the JE
    When I fill "Signataire {name of the company)" with the name of the signatory of the company
    When I fill "Date de Signature du document" with a date
    And I press "Valider de Procès-Verbal"
    Then the url should match "/suivi/etude"
    And I should see "PV ajouté"
    
  Scenario: I can write a new PVI
    Given I am logged in as "admin" or "suiveur" or "treso"
    Given I am on "/suivi/procesverbal/rediger/{id}/{type}"
    Then the response status code should be 200
    When I fill "Version du document" with "1"
    When I fill "Signataire Junior" with the name of the signatory of the JE
    When I fill "Signataire {name of the company)" with the name of the signatory of the company
    When I fill "Date de Signature du document" with a date
    And I press "Valider le Procès-Verbal"
    Then the url should match "/suivi/etude"
    And I should see "PV ajouté"
    
    
  Scenario: I can edit a PVI
    Given I am logged in as "admin" or "suiveur" or "treso"
    Given I am on "/suivi/procesverbal/modifier/{id}"
    Then the response status code should be 200
    When I fill "Version du document" with "1"
    When I fill "Signataire Junior" with the name of the signatory of the JE
    When I fill "Signataire {name of the company)" with the name of the signatory of the company
    When I fill "Date de Signature du document" with a date
    And I press "Enregistrer le Procès-Verbal"
    Then the url should match "/suivi/etude"
    And I should see "PV modifié"
    
    
  Scenario: I can delete a PVI
    Given I am logged in as "admin" or "suiveur" or "treso"
    Given I am on "/suivi/procesverbal/modifier/{id}"
    Then the response status code should be 200
    And I press "Supprimer de Procès-Verbal"
    Then I should see "Etes-vous sûr de vouloir supprimer définitivement ce PV ?"
    And I press "OK"
    Then the url should match "/suivi/etude"
    And I should see "PV supprimé"
