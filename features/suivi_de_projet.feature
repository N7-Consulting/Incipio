Feature: Suivi de projet
  As a suiver I can CRUD a Projet
  
  @createSchema 
  Scenario: I can see Projet Homepage
    Given I am logged in as "suiveur"
    Given I am on "/suivi"
    Then the response status code should be 200
    Then I should see "Suivi d'Etude"
    And I should see "315GLA"
    
  Scenario: I can see ajax
    Given I am logged in as "suiveur"
    Given I am on "/suivi/get"
    Then the response status code should be 200
    
  Scenario: I can see a Projet
    Given I am logged in as "suiveur"
    Given I am on "/suivi/etudes/suivi"
    Then the response status code should be 200
    Then I should see "Suivi d'Etude"
    
  Scenario: I can see a Projet with quality comment
    Given I am logged in as "suiveur"
    Given I am on "/suivi/etudes/suiviQualite"
    Then the response status code should be 200
    Then I should see "321DUV"
    And I should see "PVR - Date de signature :"
    
  Scenario: I can create a new Projet
    Given I am logged in as "suiveur"
    Given I am on "/suivi/etude/ajouter"
    Then the response status code should be 200
    When I select "1" from "Prospect existant"
    When I fill in "Nom interne de l'étude" with "974TAM"
    When I fill in "Numéro de l'étude" with "974"
    When I fill in "Présentation interne de l'étude" with "Etude test"
    When I select "7" from "Suiveur de projet"
    When I select "1" from "Source de prospection"
    And I press "Enregistrer l'étude"
    Then the url should match "/suivi/etude/974TAM"
    And I should see "Etude ajoutée"
    And I should see "Etat : En négociation"
    And I should see "Gladiator Consulting"
    And I should see "Alice Dubois"
    
  Scenario: I can see a Projet
    Given I am logged in as "suiveur"
    Given I am on "/suivi/etude/316BLA"
    Then the response status code should be 200
    Then I should see "Description de l'étude:"
    And I should see "Etude 316BLA"
    And I should see "Etat : En cours"
    
  Scenario: I can edit a Projet
    Given I am logged in as "suiveur"
    Given I am on "/suivi/etude/modifier/316BLA"
    Then the response status code should be 200
    When I fill in "Nom interne de l'étude" with "975TET"
    And I press "Enregistrer l'étude"
    Then the url should match "/suivi/etude/975TET"
    And I should see "Etude modifiée"
    And I should see "Etude 975TET"
    And I should not see "Etude 316BLA"
    
  #Scenario: I can delete a projet # a faire
  
  Scenario: I can write an AP
    Given I am logged in as "suiveur"
    Given I am on "/suivi/ap/rediger/2"
    Then the response status code should be 200
    When I select "1" from "Suiveur de projet"
    When I fill in "Version du document" with "1"
    When I select "5" from "Signataire Blackwater"
    When I fill in "Date de Signature du document" with "06/07/2015"
    When I fill in "Nombre d'intervenants estimé" with "1"
    When I fill in "Frais de dossier" with "90"
    When I fill in "Présentation du projet" with "Etude test"
    When I fill in "Description de la prestation proposée" with "Etude test"
    And I press "Enregistrer l'AP"
    Then the url should match "/suivi/etude/316BLA"
    And I should see "Avant-Projet modifié"
    And I should see "Etude test"
    
  Scenario: I can write a CC
    Given I am logged in as "suiveur"
    Given I am on "/suivi/cc/rediger/2"
    Then the response status code should be 200
    When I fill in "Version du document" with "1"
    When I select "5" from "Signataire Blackwater"
    When I fill in "Date de Signature du document" with "2015-07-06"
    When I fill in "Pourcentage acompte" with "40"
    And I press "Enregistrer la CC"
    Then the url should match "/suivi/etude/316BLA"
    
  Scenario: I can edit mission
    Given I am logged in as "suiveur"
    Given I am on "/suivi/missions/modifier/2"
    Then the response status code should be 200
    When I select "1" from "Référent Technique"
    And I press "Enregistrer les missions"
    Then the url should match "/suivi/missions/modifier/2"
    And I should see "Mission enregistrée"
    
  Scenario: I can add a customer contact
    Given I am logged in as "suiveur"
    Given I am on "/suivi/clientcontact/ajouter/1"
    Then the response status code should be 200
    When I select "1" from "Fait par"
    When I fill in "Objet" with "Test"
    When I fill in "Résumé du contact" with "Contact test"
    When I fill in "Date du contact" with "2018-04-04"
    And I press "Enregistrer le nouveau contact client"
    Then the url should match "/suivi/clientcontact/voir/1"
    And I should see "Test"
    And I should see "Contact test"
    
  Scenario: I can edit a contact
    Given I am logged in as "suiveur"
    Given I am on "/suivi/clientcontact/modifier/1"
    Then the response status code should be 200
    When I fill in "Objet" with "test"
    When I fill in "Résumé du contact" with "contact test replace"
    And I press "Enregistrer le contact client"
    Then the url should match "/suivi/clientcontact/voir/1"
    And I should see "Contact client modifié"
    And I should see "test"
    And I should see "contact test replace"
    
  Scenario: I can see a contact
    Given I am logged in as "suiveur"
    Given I am on "/suivi/clientcontact/voir/1"
    Then the response status code should be 200
    And I should see "Affichage d'un contact client"
  
  @dropSchema
  Scenario: I can see a contact
    Given I am logged in as "suiveur"
    Given I am on "/suivi/clientcontact/"
    Then the response status code should be 200
    And I should see "Affichage des contacts client"
  
