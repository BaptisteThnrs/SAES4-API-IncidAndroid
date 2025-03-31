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
            sendError($e->getMessage(), 500);
        }
    }

    public function getIncident($idEmploye): void {
        try {
            $maRequete = 'SELECT id_incident, resume, description, service_technique,
                                 incident.id_reservation, intitule, reservation.date_reservation,
                                 reservation.heure_debut, reservation.heure_fin, salle.nom,
                                 incident.date_signalement, incident.heure_signalement, incident.id_employe,
                                 gravite.id_gravite
                          FROM incident
                          JOIN gravite ON incident.id_gravite = gravite.id_gravite
                          JOIN reservation ON incident.id_reservation = reservation.id_reservation
                          JOIN salle ON reservation.id_salle = salle.id_salle
                          WHERE incident.id_employe = :idEmploye';

            $stmt = $this->pdo->prepare($maRequete);
            $stmt->bindValue(':idEmploye', $idEmploye, PDO::PARAM_INT);
            $stmt->execute();

            $incidents = $stmt->fetchAll();
            sendJSON($incidents, 200);
        } catch (PDOException $e) {
            sendError($e->getMessage(), 500);
        }
    }

    public function putIncident(array $donneesJson, $id_incident): void {
        if (!empty($donneesJson['RESUME'])
            && isset($donneesJson['SERVICE_TECHNIQUE'])
            && !empty($donneesJson['ID_GRAVITE'])) {
            try {
                $maRequete = 'UPDATE incident 
                            SET resume = :resume, description = :description, 
                                service_technique = :service_technique, id_gravite = :id_gravite
                            WHERE id_incident = :id_incident';

                $stmt = $this->pdo->prepare($maRequete);
                $stmt->bindParam(":resume", $donneesJson['RESUME']);
                $stmt->bindParam(":description", $donneesJson['DESCRIPTION']);
                $stmt->bindParam(":service_technique", $donneesJson['SERVICE_TECHNIQUE']);
                $stmt->bindParam(":id_gravite", $donneesJson['ID_GRAVITE']);
                $stmt->bindParam(":id_incident", $id_incident);
                $stmt->execute();

                sendJSON(["Statut" => "OK"], 201);
            } catch (PDOException $e) {
                sendError($e->getMessage(), 500);
            }
        } else {
            sendError("Données incomplètes", 400);
        }
    }

    /**
     * @param array<string, mixed> $donneesJson
     */
    public function postIncident(array $donneesJson): void {
        if (!empty($donneesJson['RESUME'])
            && isset($donneesJson['SERVICE_TECHNIQUE'])
            && !empty($donneesJson['ID_GRAVITE'])
            && !empty($donneesJson['ID_RESERVATION'])
            && !empty($donneesJson['ID_EMPLOYE'])) {
            try {
                $maRequete = 'INSERT INTO incident (RESUME, DESCRIPTION, SERVICE_TECHNIQUE, ID_GRAVITE, ID_RESERVATION, ID_EMPLOYE, DATE_SIGNALEMENT, HEURE_SIGNALEMENT) 
                              VALUES (:RESUME, :DESCRIPTION, :SERVICE_TECHNIQUE, :ID_GRAVITE, :ID_RESERVATION, :ID_EMPLOYE, CURDATE(), CURTIME())';

                $stmt = $this->pdo->prepare($maRequete);
                $stmt->bindParam(":RESUME", $donneesJson['RESUME']);
                $stmt->bindParam(":DESCRIPTION", $donneesJson['DESCRIPTION']);
                $stmt->bindParam(":SERVICE_TECHNIQUE", $donneesJson['SERVICE_TECHNIQUE']);
                $stmt->bindParam(":ID_GRAVITE", $donneesJson['ID_GRAVITE']);
                $stmt->bindParam(":ID_RESERVATION", $donneesJson['ID_RESERVATION']);
                $stmt->bindParam(":ID_EMPLOYE", $donneesJson['ID_EMPLOYE']);
                $stmt->execute();

                sendJSON(["Statut" => "OK", "ID" => $this->pdo->lastInsertId()], 201);
            } catch (PDOException $e) {
                sendError($e->getMessage(), 500);
            }
        } else {
            sendError("Données incomplètes", 400);
        }
    }

    public function getIncidentPourUneReservation(String $idReservation): void {
        try {
            $maRequete = 'SELECT id_incident, resume, gravite.intitule, service_technique,
                                 date_signalement, heure_signalement
                          FROM incident
                          JOIN gravite ON incident.id_gravite = gravite.id_gravite
                          WHERE incident.id_reservation = :reservation';

            $stmt = $this->pdo->prepare($maRequete);
            $stmt->bindParam(":reservation", $idReservation, PDO::PARAM_INT);
            $stmt->execute();

            $incidents = $stmt->fetchAll();
            sendJSON($incidents, 200);
        } catch (PDOException $e) {
            sendError($e->getMessage(), 500);
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
            sendError($e->getMessage(), 500);
        }
    }

    public function getInfoUnIncidentModif(String $idIncident): void {
        try {
            $maRequete = 'SELECT resume, description, service_technique, incident.id_gravite
                          FROM incident
                          JOIN gravite ON incident.id_gravite = gravite.id_gravite
                          WHERE incident.id_incident = :idIncident';

            $stmt = $this->pdo->prepare($maRequete);
            $stmt->bindParam(":idIncident", $idIncident, PDO::PARAM_INT);
            $stmt->execute();

            $incident = $stmt->fetchAll();
            sendJSON($incident, 200);
        } catch (PDOException $e) {
            sendError($e->getMessage(), 500);
        }
    }

    public function getToutesGravite(): void {
        try {
            $maRequete = 'SELECT intitule, id_gravite
                          FROM gravite';

            $stmt = $this->pdo->prepare($maRequete);
            $stmt->execute();

            $incident = $stmt->fetchAll();
            sendJSON($incident, 200);
        } catch (PDOException $e) {
            sendError($e->getMessage(), 500);
        }
    }
}
?>
