<?php
require_once '../fonction/liaisonBD.php';

class Donnees {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = connecteBD();
    }

    public function getReservation(String $idEmploye): void {
        try {
            $maRequete = '
            SELECT reservation.id_reservation AS id_reservation,
                   salle.nom AS nom_salle,
                   employe.nom AS nom_employe,
                   employe.prenom AS prenom_employe,
                   activite.nom_activite AS nom_activite,
                   reservation.date_reservation AS date,
                   reservation.heure_debut AS heure_debut,
                   reservation.heure_fin AS heure_fin
            FROM reservation
            JOIN salle ON reservation.id_salle = salle.id_salle
            JOIN employe ON reservation.id_employe = employe.id_employe
            JOIN activite ON reservation.id_activite = activite.id_activite
            WHERE reservation.id_employe = :id_employe';

            $stmt = $this->pdo->prepare($maRequete);
            $stmt->bindValue(':id_employe', $idEmploye, PDO::PARAM_INT);
            $stmt->execute();

            $reservations = $stmt->fetchAll();
            sendJSON($reservations, 200);
        } catch (PDOException $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function getIncident(): void {
        try {
            $maRequete = 'SELECT id_incident, resume, description, service_technique,
                                 incident.id_reservation, intitule, reservation.date_reservation,
                                 reservation.heure_debut, reservation.heure_fin, salle.nom,
                                 incident.date_signalement, incident.heure_signalement
                          FROM incident
                          JOIN gravite ON incident.id_gravite = gravite.id_gravite
                          JOIN reservation ON incident.id_reservation = reservation.id_reservation
                          JOIN salle ON reservation.id_salle = salle.id_salle';

            $stmt = $this->pdo->prepare($maRequete);
            $stmt->execute();

            $incidents = $stmt->fetchAll();
            sendJSON($incidents, 200);
        } catch (PDOException $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function putIncident(String $resume, String $description, String $service_technique, 
                                String $id_gravite, String $id_incident): void {
        try {
            $maRequete = 'UPDATE incident 
                          SET resume = :resume, description = :description, 
                              service_technique = :service_technique, id_gravite = :id_gravite 
                          WHERE id_incident = :id_incident';

            $stmt = $this->pdo->prepare($maRequete);
            $stmt->bindParam(":resume", $resume);
            $stmt->bindParam(":description", $description);
            $stmt->bindParam(":service_technique", $service_technique);
            $stmt->bindParam(":id_gravite", $id_gravite);
            $stmt->bindParam(":id_incident", $id_incident);
            $stmt->execute();

            sendJSON(["Statut" => "OK"], 201);
        } catch (PDOException $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    /**
     * @param array<string, mixed> $donneesJson
     */
    public function postIncident(array $donneesJson): void {
        if (!empty($donneesJson['RESUME'])
            && isset($donneesJson['SERVICE_TECHNIQUE'])
            && !empty($donneesJson['ID_GRAVITE'])
            && !empty($donneesJson['ID_RESERVATION'])) {
            try {
                $maRequete = 'INSERT INTO incident (RESUME, DESCRIPTION, SERVICE_TECHNIQUE, ID_GRAVITE, ID_RESERVATION, DATE_SIGNALEMENT, HEURE_SIGNALEMENT) 
                              VALUES (:RESUME, :DESCRIPTION, :SERVICE_TECHNIQUE, :ID_GRAVITE, :ID_RESERVATION, CURDATE(), CURTIME())';

                $stmt = $this->pdo->prepare($maRequete);
                $stmt->bindParam(":RESUME", $donneesJson['RESUME']);
                $stmt->bindParam(":DESCRIPTION", $donneesJson['DESCRIPTION']);
                $stmt->bindParam(":SERVICE_TECHNIQUE", $donneesJson['SERVICE_TECHNIQUE']);
                $stmt->bindParam(":ID_GRAVITE", $donneesJson['ID_GRAVITE']);
                $stmt->bindParam(":ID_RESERVATION", $donneesJson['ID_RESERVATION']);
                $stmt->execute();

                sendJSON(["Statut" => "OK", "ID" => $this->pdo->lastInsertId()], 201);
            } catch (PDOException $e) {
                $this->sendError($e->getMessage(), 500);
            }
        } else {
            $this->sendError("Données incomplètes", 400);
        }
    }

    public function getIncidentPourUneReservation(String $idReservation): void {
        try {
            $maRequete = 'SELECT id_incident, resume, description, service_technique,
                                 incident.id_reservation, intitule, reservation.date_reservation,
                                 reservation.heure_debut, reservation.heure_fin, salle.nom,
                                 incident.date_signalement, incident.heure_signalement
                          FROM incident
                          JOIN gravite ON incident.id_gravite = gravite.id_gravite
                          JOIN reservation ON incident.id_reservation = reservation.id_reservation
                          JOIN salle ON reservation.id_salle = salle.id_salle
                          WHERE incident.id_reservation = :reservation';

            $stmt = $this->pdo->prepare($maRequete);
            $stmt->bindParam(":reservation", $idReservation, PDO::PARAM_INT);
            $stmt->execute();

            $incidents = $stmt->fetchAll();
            sendJSON($incidents, 200);
        } catch (PDOException $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function getInfoUnIncident(String $idIncident): void {
        try {
            $maRequete = 'SELECT id_incident, resume, description, service_technique,
                                 incident.id_reservation, intitule, reservation.date_reservation,
                                 reservation.heure_debut, reservation.heure_fin, salle.nom,
                                 incident.date_signalement, incident.heure_signalement
                          FROM incident
                          JOIN gravite ON incident.id_gravite = gravite.id_gravite
                          JOIN reservation ON incident.id_reservation = reservation.id_reservation
                          JOIN salle ON reservation.id_salle = salle.id_salle
                          WHERE incident.id_incident = :idIncident';

            $stmt = $this->pdo->prepare($maRequete);
            $stmt->bindParam(":idIncident", $idIncident, PDO::PARAM_INT);
            $stmt->execute();

            $incident = $stmt->fetchAll();
            sendJSON($incident, 200);
        } catch (PDOException $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    private function sendError(String $message, Int $code): void  {
        sendJSON(["Statut" => "KO", "message" => $message], $code);
    }
}
?>
