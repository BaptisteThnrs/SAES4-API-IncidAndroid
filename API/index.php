<?php
require("json.php");
require("donnees.php");

class Api {
    public function __construct() {
        $this->requete();
    }

    private function requete() {
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

    private function getRequete() {
        if (!empty($_GET['demande'])) {
            $url = explode("/", filter_var($_GET['demande'], FILTER_SANITIZE_URL));
            
            switch ($url[0]) {
                case 'toutesReservations':
                    if (!empty($url[1])) {
                        getReservation($url[1]);
                    } else {
                        $this->sendError("Employé non trouvé", 404);
                    }
                    break;
                case 'tousIncidents':
                    getIncident();
                    break;
                case 'incidentUneReservation':
                    if (!empty($url[1])) {
                        getIncidentPourUneReservation($url[1]);
                    } else {
                        $this->sendError("Réservation non trouvée", 404);
                    }
                    break;
                case 'InfoUnIncident':
                    if (!empty($url[1])) {
                        getInfoUnIncident($url[1]);
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

    private function postRequete() {
        if (!empty($_GET['demande'])) {
            $url = explode("/", filter_var($_GET['demande'], FILTER_SANITIZE_URL));
            
            if ($url[0] === 'ajoutIncident') {
                $inputJSON = file_get_contents("php://input");
                $data = json_decode($inputJSON, true);
                
                if ($data === null) {
                    $this->sendError("Données JSON invalides", 400);
                }
                
                postIncident($data);
                return;
            }
        }
        
        $this->sendError("URL non valide POST", 404);
    }

    private function putRequete() {
        if (!empty($_GET['demande'])) {
            $url = explode("/", filter_var($_GET['demande'], FILTER_SANITIZE_URL));
            
            if ($url[0] === 'modifIncident' && !empty($url[1])) {
                $idIncident = $url[1];
                $input = file_get_contents("php://input");
                $data = json_decode($input, true);
                
                if (!isset($data['resume'], $data['service_technique'], $data['id_gravite'])) {
                    $this->sendError("Données incomplètes", 400);
                }
                
                putIncident($data['resume'], $data['description'], $data['service_technique'], $data['id_gravite'], $idIncident);
                return;
            }
        }
        
        $this->sendError("URL non valide PUT", 404);
    }

    private function sendError($message, $code) {
        $infos = ["Statut" => "KO", "message" => $message];
        sendJSON($infos, $code);
        exit;
    }
}

new Api();
?>
