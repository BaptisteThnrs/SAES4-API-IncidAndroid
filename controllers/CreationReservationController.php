<?php
namespace controllers;

use PDO;
use PDOException;
use yasmf\HttpHelper;
use yasmf\View;
use yasmf\Controller;
use model\Requete;
require __DIR__ . '/../fonction/connexion.php';

session_start();

class CreationReservationController {

    private Requete $requete;

    /**
     * Create a new default controller
     */
    public function __construct(Requete $requete)
    {
        $this->requete = $requete;
    }

    public function index(PDO $pdo): view {
            $tabSalles = $this->requete->listeSalles($pdo);
            $tabActivites = $this->requete->listeActivites($pdo);
            $tabReservation = $this->requete->affichageReservation($pdo);

            $view = new View("../views/creationReservation");
            $view->setVar('listeSalles', $tabSalles);
            $view->setVar('listeActivites', $tabActivites);
            $view->setVar('affichageReservation', $tabReservation);
            return $view;
    }

    public function ajoutReservation(PDO $pdo): view {
        $nomSalle = HttpHelper::getParam('nomSalle') ?? '';
        $nomActivite = HttpHelper::getParam('nomActivite') ?? '';
        $date = HttpHelper::getParam('date') ?? '';
        $heureDebut = HttpHelper::getParam('heureDebut') ?? '';
        $heureFin = HttpHelper::getParam('heureFin') ?? '';
        $objet = HttpHelper::getParam('objet') ?? '';
        $nom = HttpHelper::getParam('nom') ?? '';
        $prenom = HttpHelper::getParam('prenom') ?? '';
        $numTel = HttpHelper::getParam('numTel') ?? '';
        $precisActivite = HttpHelper::getParam('precisActivite') ?? '';
        
        $this->requete->insertionReservation($pdo, $nomSalle, $nomActivite, $date, $heureDebut, $heureFin, $objet, $nom, $prenom, $numTel, $precisActivite, $_SESSION['id']);
        $tabSalles = $this->requete->listeSalles($pdo);
        $tabActivites = $this->requete->listeActivites($pdo);
        $tabReservation = $this->requete->affichageReservation($pdo);

        $view = new View("../views/creationReservation");
        $view->setVar('listeSalles', $tabSalles);
        $view->setVar('listeActivites', $tabActivites);
        $view->setVar('affichageReservation', $tabReservation);
        return $view;
    }
}
?>