<?php
namespace controllers;

use PDO;
use PDOException;
use yasmf\HttpHelper;
use yasmf\View;
use yasmf\Controller;
use model\Requete;

class CreationReservationController {

    private Requete $requete;

    /**
     * Create a new default controller
     */
    public function __construct(Requete $requete)
    {
        $this->requete = $requete;
    }

    // public function index($pdo): view {
    //     $nomSalle = HttpHelper::getParam('nomSalle');
    //     $nomActivite = HttpHelper::getParam('nomActivite');
    //     $date = HttpHelper::getParam('date');
    //     $heureDebut = HttpHelper::getParam('heureDebut');
    //     $heureFin = HttpHelper::getParam('heureFin');
    //     $objet = HttpHelper::getParam('objet');
    //     $nom = HttpHelper::getParam('nom');
    //     $prenom = HttpHelper::getParam('prenom');
    //     $numTel = HttpHelper::getParam('numTel');
    //     $precisActivite = HttpHelper::getParam('precisActivite');
    //     try {
    //         $this->requete->insertionReservation($nomSalle, $nomActivite, $date, $heureDebut, $heureFin, $objet, $nom, $prenom, $numTel, $precisActivite, $idLogin);
    //     } catch(\PDOException $ex) {
    //         //$view->setVar('error', "Un problème est survenu.");
    //     }
    // }

    public function ajoutReservation(PDO $pdo) {
        $nomSalle = HttpHelper::getParam('nomSalle');
        $nomActivite = HttpHelper::getParam('nomActivite');
        $date = HttpHelper::getParam('date');
        $heureDebut = HttpHelper::getParam('heureDebut');
        $heureFin = HttpHelper::getParam('heureFin');
        $objet = HttpHelper::getParam('objet');
        $nom = HttpHelper::getParam('nom');
        $prenom = HttpHelper::getParam('prenom');
        $numTel = HttpHelper::getParam('numTel');
        $precisActivite = HttpHelper::getParam('precisActivite');
        try {
            $this->requete->insertionReservation($pdo, $nomSalle, $nomActivite, $date, $heureDebut, $heureFin, $objet, $nom, $prenom, $numTel, $precisActivite, 1);
        } catch(\PDOException $ex) {
            //$view->setVar('error', "Un problème est survenu.");
        }
    }
}
?>