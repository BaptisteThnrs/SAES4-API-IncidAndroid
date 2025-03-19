<?php
namespace controllers;

use yasmf\HttpHelper;
use yasmf\View;
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

    public function ajoutReservation($pdo) {
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
            $this->Requete->insertionReservation($nomSalle, $nomActivite, $date, $heureDebut, $heureFin, $objet, $nom, $prenom, $numTel, $precisActivite, $idLogin);
        } catch(\PDOException $ex) {
            //$view->setVar('error', "Un problème est survenu.");
        }
    }
}
?>