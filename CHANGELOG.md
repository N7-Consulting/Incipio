[comment]: <> (Convertir ce fichier en code HTML et mettre à jour la page dédiée pour que les modifications apparaissent sur le CRM directement.)
# Changelog ─ Jeyser CRM

## Preview Jeyser 4.0.0 (Mis à jour le 2021-07-10)

Cette release s'inscrit dans une volontée de N7 Consulting de redynamiser Jeyser CRM après plusieures années d'inactivité.
Logiciel libre [lourd d'histoire](https://jeyser-crm.n7consulting.fr/docs/dev/about/), l'objectif de cette nouvelle version est d'apporter son lot de mises à jour et d'améliorations tout en maintenant un haut niveau de rétrocompatibilité.

### Dashboard

Le dashboard a connu deux nouvelles fonctionnalités majeures, ainsi qu'une update concernant le chiffre d'affaire suite à l'ajout des nouveaux états d'une étude (voir Suivi d'étude pour plus de détails).

#### Calendrier

- Ajout d'un calendrier commun à la Junior ;
- Possibilitité d'ajouter/modifier/supprimer des événements.

#### Commentaires

- Reprise de tous les commentaires associés aux études et affichage des plus récents (< 10 jours) ;
- Accès direct à un fil de discussion.

*NB: La structure (réponse, réponse de réponse, etc) des fils de discussion est perdue au rechargement de la page, ceci sera réglé si le temps le permet.*

#### Chiffres clés

- CA en cours = études acceptées + études en cours ;
- CA clôturé = études finies + études terminées ;
- L'heure d'actualisation affichée est celle de Paris.

### Suivi d'étude

Le suivi d'étude étant le fer de lance d'une junior, un soin particulier a été apporté à cette onglet.

Tout d'abord, de nouveaux documents ont fait leur apparition (Convention Cadre Agile et Bon de Commande) depuis la dernière mise à jour de Jeyser. De plus pour davantage d'organisation deux nouveaux états associés à une étude sont apparus:

#### Nouveaux états associés à une étude

- Etude acceptée -> Le client a signé l'étude mais cette dernière n'a pas encore commencé ;
- Etude finie (≠ clôturée) -> L'étude à proprement parler est terminée, mais elle n'a pas encore reçu d'audit en interne par la qualité. Une fois l'audit réalisé l'étude peut-être "clôturée".

#### Voir les études

- Ajout de deux onglets associés aux nouveaux états (acceptée et finies). Ces onglets sont cachés par défaut sauf si des études dans ces états existent ;
- Uniformisation de l'affichage des onglets, notamment les études des mandats précédents, qui utilisaient encore un ancien affichage ;
- Nombre de contacts client réalisé dans cette étude.

#### Créer une étude

- Possibilité d'utiliser une CCa ajoutée précédemment (le prospect sera donc déduit de ce document). La date de fin de cette CCa ne doit pas être passée ;
- Meilleure déduction du numéro de l'étude. Pour les juniors qui n'ont jamais utilisé de numéro de l'étude, ce champ ne sera laissé vide.


#### Voir les CCa

Ceci est un nouvel onglet permettant de gérer les Conventions Cadre Agile de la junior.

Il se compose:

- D'un bouton permettant d'ajouter une Cca ;
- D'un tableau affichant l'ensemble des CCa, surlignées ou non ;
  - La couleur jaune signifie qu'elle se termine bientôt (< 1 mois) ;
  - La couleur rouge signifie que cette CCa est terminée.
- D'un bouton avec le nombre de BDC associés à cette CCa. Ce bouton mène vers un tableau détaillant les études associées à ces BdC ;
- D'un bouton permettant de modifier les informations de la CCa (sauf prospect) et de la supprimer.

#### Vue d'une étude

##### Récapitulatif

Harmonisation de la couleur d'affichage selon l'état d'une étude (avec l'onglet Vue CA par exemple).

##### Phases

Lors de l'ajout d'une phase, le prix d'un JEH est automatiquement reporté et la date de début de la phase suivante est automatiquement calculée avec le délai.

##### Rédaction et Génération

- Pour une étude avec une CCa, on peut rédiger un BdC puis le générer. On peut aussi générer la CCa ;
- Lors de l'ajout d'un RM, la répartition des JEH doit faire référence à des phases de l'étude.


##### Suivi

- Ajout des nouveaux états pour une étude ;
- Ajout des nouveaux documents d'étude ;
- Documents non générés sont désormais cachés dans le tableau récapitulatif ;
- Les audits se font désormais dans l'onglet Qualité (voir Qualité).

##### Contacts

Cet onglet existait auparavant mais il était moins accessible.

- Harmonisation de l'affichage ;
- Modification/suppression d'un contact client directement en cliquant dessus.

#### Contacts Client

Harmonisation de cet onglet avec l'onglet par étude "Contacts"

#### Vue CA

La vue CA permet d'avoir un condensé d'informations pour chaque étude non terminée.

- Ajout des études acceptées à l'accès rapide ;
- Ajout des nouveaux documents (BdC) ;
- Réorganisation de l'affichage ;
- Utilisation des templates (voir le Coin des développeurs).

#### Boutons interactifs Client/Suiveur

Ces boutons ne marchaient pas tous, ils sont désormais opérationnels.

#### Suivi des problèmes

Déplacé dans l'onglet Qualité (voir Qualité).

### R.F.P. (Anciennement Formation)

Cet onglet, plus complet, permet d'ajouter des documents de passation et les formations liées au R.F.P. afin d'avoir un archivage uniforme entre tous les pôles.

- Ajout de passations liés à un pôle ;
  - Upload de documents liés à une passation.
- Ajout de formations ;
  - Ajout de formations/participants ;
  - Date de début/date de fin ;
  - Upload de documents liés à une formation.
- Monitoring des participants aux formations dispensées.

### Qualité

#### Indicateurs

- Affichage de données brutes récupérées automatiquement, soit mensuellement (12 derniers mois) soit par année (3 dernières années) ;
- Les graphiques, classés par pôles, ont été déplacés ici.

#### Gestion des processus

Nouvel onglet permettant au pôle qualité une gestion plus centrale des processus.

- Ajout d'un processus (Nom du processus / Pilote).
  - Ajout de documents liés à ce processus.

#### Suivi des problèmes

C'est ici que la qualité va pouvoir auditer une étude lorsque cette dernière passe dans l'état "Finie".

- Ajout d'un tableau (affiché par défaut) qui permet d'auditer les études finies (bouton Audit Etudes Finies) ;
- Ajout d'un tableau qui affiche les études finies (bonton Etudes Finies).

### Archivage des documents

C'est sur ce nouvel onglet qu'on peut télécharger tous les documents (si les doctypes existent) de toutes les études.

*NB: La partie trésorerie de cet onglet n'est pas terminé.*

### Divers

#### Esthétique

- Affichage du nom de la junior en entier en haut à gauche. Ceci est remplacé par son logo lorsque le panneau latéral est minimisé.

### Le coin des développeurs

- Travail important pour améliorer l'i18n sur l'ensemble des pages sur lesquelles nous avons travaillé pour une meilleure portabilité dans d'autres langages ;
- Factorisation de code pour les études (extensions Twig/templates).
  - Voir EtudeExtension.php :
    - Ajout d'une fonction unique qui renvoie la couleur du bandeau d'une étude selon son état (en négociation, en pause, etc) ;
    - Ajout d'une fonction unique qui renvoie la couleur d'un document selon son état (s'utilise indifféremment avec un document d'étude ou une facture).
  - Voir Project/Etude/Tools/* (liste non exhaustive) :
    - AffichageAvancement.html.twig, affiche l'avancement d'une étude ;
    - EtatDocument.html.twig, affiche l'état d'un document ;
    - EtatDocuments.html.twig, affiche l'état d'une liste de documents ;
    - Intervenants.html.twig, affiche sous forme de boutons l'ensemble des intervenants d'une étude ;
    - Et bien d'autres.
- Mise à jour des fixtures (demo_data) avec nouvelles features.
