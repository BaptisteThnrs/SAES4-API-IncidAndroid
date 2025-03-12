<?php
require("json.php");
require("donnees.php");

class Api {
    private Donnees $donnees;

    public function __construct() {
        $this->donnees = new Donnees();
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
                case 'toutesReservations':
                    if (!empty($url[1])) {
                        $this->donnees->getReservation($url[1]);
                    } else {
                        $this->sendError("Employé non trouvé", 404);
                    }
                    break;
                case 'tousIncidents':
                    $this->donnees->getIncident();
                    break;
                case 'incidentUneReservation':
                    if (!empty($url[1])) {
                        $this->donnees->getIncidentPourUneReservation($url[1]);
                    } else {
                        $this->sendError("Réservation non trouvée", 404);
                    }
                    break;
                case 'InfoUnIncident':
                    if (!empty($url[1])) {
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
