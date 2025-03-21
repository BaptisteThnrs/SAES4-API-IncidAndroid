<?php
require("json.php");
require("donnees.php");
require("authentification.php");

class Api {
    private Donnees $donnees;
    private Authentification $authentification;

    public function __construct() {
        $this->donnees = new Donnees();
        $this->authentification = new Authentification();
        $this->requete();
    }

    private function requete(): void {
        $method_requete = $_SERVER["REQUEST_METHOD"];
        switch ($method_requete) {
            case "GET":
                $this->getRequete();
                break;
            case "POST":
                $this->postRequete();
                break;
            case "PUT":
                $this->putRequete();
                break;
            default:
                $this->sendError("Méthode non supportée", 405);
        }
    }

    private function getRequete(): void {
        if (!empty($_GET['demande'])) {
            $demande = filter_var($_GET['demande'], FILTER_SANITIZE_URL);
            $url = [];
            if ($demande !== false) {
                $url = explode("/", $demande);
            } else {
                $this->sendError("URL non valide ou mal formatée", 404);
            }
            switch ($url[0]) {
                case 'login':
                    if (!empty($url[1]) && !empty($url[2])) {
                        $this->authentification->verifLoginPassword($url[1], $url[2]);
                    } else {
                        $this->sendError("Employé non trouvé", 404);
                    }
                    break;
                case 'toutesReservations':
                    if (!empty($url[1])) {
                        $this->authentification->authentification(); // Test si on est bien authenfifié pour l'API
                        $this->donnees->getReservation($url[1]);
                    } else {
                        $this->sendError("Employé non trouvé", 404);
                    }
                    break;
                case 'tousIncidents':
                    $this->authentification->authentification(); // Test si on est bien authenfifié pour l'API
                    $this->donnees->getIncident();
                    break;
                case 'incidentUneReservation':
                    if (!empty($url[1])) {
                        $this->authentification->authentification(); // Test si on est bien authenfifié pour l'API
                        $this->donnees->getIncidentPourUneReservation($url[1]);
                    } else {
                        $this->sendError("Réservation non trouvée", 404);
                    }
                    break;
                case 'InfoUnIncident':
                    if (!empty($url[1])) {
                        $this->authentification->authentification(); // Test si on est bien authenfifié pour l'API
                        $this->donnees->getInfoUnIncident($url[1]);
                    } else {
                        $this->sendError("Incident non trouvé", 404);
                    }
                    break;
                default:
                    $this->sendError("Requête inexistant", 404);
            }
        } else {
            $this->sendError("URL non valide", 404);
        }
    }

    private function postRequete(): void {
        if (!empty($_GET['demande'])) {
            $demande = filter_var($_GET['demande'], FILTER_SANITIZE_URL);
            $url = [];
            if ($demande !== false) {
                $url = explode("/", $demande);
            } else {
                $this->sendError("URL non valide ou mal formatée", 404);
            }
            
            if ($url[0] === 'ajoutIncident') {
                $inputJSON = file_get_contents("php://input");

                if ($inputJSON === false) {
                    $this->sendError("Impossible de lire les données d'entrée", 400);
                } else {
                    $data = json_decode($inputJSON, true);
                
                    if (!is_array($data)) {
                        $this->sendError("Données JSON invalides ou format incorrect", 400);
                    } else {
                        $this->authentification->authentification(); // Test si on est bien authenfifié pour l'API
                        $this->donnees->postIncident($data);
                    }
                }
            }
        }
        
        $this->sendError("URL non valide POST", 404);
    }

    private function putRequete(): void {
        if (!empty($_GET['demande'])) {
            $demande = filter_var($_GET['demande'], FILTER_SANITIZE_URL);
            $url = [];
            if ($demande !== false) {
                $url = explode("/", $demande);
            } else {
                $this->sendError("URL non valide ou mal formatée", 404);
            }
            
            if ($url[0] === 'modifIncident' && !empty($url[1])) {
                $idIncident = $url[1];
                $inputJSON = file_get_contents("php://input");

                if ($inputJSON === false) {
                    $this->sendError("Impossible de lire les données d'entrée", 400);
                } else {
                    $data = json_decode($inputJSON, true);
                
                    if (!is_array($data)) {
                        $this->sendError("JSON invalide", 400);
                    } else {
                        if (!isset($data['resume'], $data['service_technique'], $data['id_gravite'])) {
                            $this->sendError("Données incomplètes", 400);
                        }
                        $this->authentification->authentification(); // Test si on est bien authenfifié pour l'API
                        $this->donnees->putIncident($data['resume'], $data['description'], $data['service_technique'], $data['id_gravite'], $idIncident);
                    }
                }
            }
        }
        
        $this->sendError("URL non valide PUT", 404);
    }

    private function sendError(String $message, int $code): void {
        $infos = ["Statut" => "KO", "message" => $message];
        sendJSON($infos, $code);
        exit;
    }
}

new Api();
?>
