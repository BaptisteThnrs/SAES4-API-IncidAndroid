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
                sendError("Méthode non supportée", 405);
        }
    }

    private function getRequete(): void {
        if (!empty($_GET['demande'])) {
            $demande = filter_var($_GET['demande'], FILTER_SANITIZE_URL);
            $url = [];
            if ($demande !== false) {
                $url = explode("/", $demande);
            } else {
                sendError("URL non valide ou mal formatée", 404);
            }
            switch ($url[0]) {
                case 'login':
                    if (!empty($url[1]) && !empty($url[2])) {
                        $this->authentification->verifLoginPassword($url[1], $url[2]);
                    } else {
                        sendError("Employé non trouvé", 404);
                    }
                    break;
                case 'toutesReservations':
                    if (!empty($url[1])) {
                        $this->authentification->authentification(); // Test si on est bien authenfifié pour l'API
                        $this->donnees->getReservation($url[1]);
                    } else {
                        sendError("Employé non trouvé", 404);
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
                        sendError("Réservation non trouvée", 404);
                    }
                    break;
                case 'infoUnIncident':
                    if (!empty($url[1])) {
                        $this->authentification->authentification(); // Test si on est bien authenfifié pour l'API
                        $this->donnees->getInfoUnIncident($url[1]);
                    } else {
                        sendError("Incident non trouvé", 404);
                    }
                    break;
                case 'toutesGravites':
                    $this->authentification->authentification(); // Test si on est bien authenfifié pour l'API
                    $this->donnees->getToutesGravite();
                    break;
                default:
                    sendError("Requête inexistant", 404);
            }
        } else {
            sendError("URL non valide", 404);
        }
    }

    private function postRequete(): void {
        if (!empty($_GET['demande'])) {
            $demande = filter_var($_GET['demande'], FILTER_SANITIZE_URL);
            $url = [];
            if ($demande !== false) {
                $url = explode("/", $demande);
            } else {
                sendError("URL non valide ou mal formatée", 404);
            }
            
            if ($url[0] === 'ajoutIncident') {
                $inputJSON = file_get_contents("php://input");

                if ($inputJSON === false) {
                    sendError("Impossible de lire les données d'entrée", 400);
                } else {
                    $data = json_decode($inputJSON, true);
                
                    if (!is_array($data)) {
                        sendError("Données JSON invalides ou format incorrect", 400);
                    } else {
                        $this->authentification->authentification(); // Test si on est bien authenfifié pour l'API
                        $this->donnees->postIncident($data);
                    }
                }
            }
        }
        
        sendError("URL non valide POST", 404);
    }

    private function putRequete(): void {
        if (!empty($_GET['demande'])) {
            $demande = filter_var($_GET['demande'], FILTER_SANITIZE_URL);
            $url = [];
            if ($demande !== false) {
                $url = explode("/", $demande);
            } else {
                sendError("URL non valide ou mal formatée", 404);
            }
            
            if ($url[0] === 'modifIncident' && !empty($url[1])) {
                $idIncident = $url[1];
                $inputJSON = file_get_contents("php://input");

                if ($inputJSON === false) {
                    sendError("Impossible de lire les données d'entrée", 400);
                } else {
                    $data = json_decode($inputJSON, true);
                
                    if (!is_array($data)) {
                        sendError("JSON invalide", 400);
                    } else {
                        if (!isset($data['resume'], $data['service_technique'], $data['id_gravite'])) {
                            sendError("Données incomplètes", 400);
                        }
                        $this->authentification->authentification(); // Test si on est bien authenfifié pour l'API
                        $this->donnees->putIncident($data['resume'], $data['description'], $data['service_technique'], $data['id_gravite'], $idIncident);
                    }
                }
            }
        }
        
        sendError("URL non valide PUT", 404);
    }
}

new Api();
?>
