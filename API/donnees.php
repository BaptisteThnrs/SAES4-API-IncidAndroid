<?php
    include '../fonction/liaisonBD.php';
    $pdo = connecteBD();

    function getReservation($idEmploye) {
        try {
            $pdo=connecteBD();
            $maRequete='
            SELECT  reservation.id_reservation AS id_reservation,
                    salle.nom AS nom_salle,
                    employe.nom AS nom_employe,
                    employe.prenom AS prenom_employe,
                    activite.nom_activite AS nom_activite,
                    reservation.date_reservation AS date,
                    reservation.heure_debut AS heure_debut,
                    reservation.heure_fin AS heure_fin
                FROM 
                    reservation
                JOIN 
                    salle ON reservation.id_salle = salle.id_salle
                JOIN 
                    employe ON reservation.id_employe = employe.id_employe
                JOIN 
                    activite ON reservation.id_activite = activite.id_activite
                WHERE 
                    reservation.id_employe = :id_employe';
            $stmt = $pdo->prepare($maRequete);
            $stmt->bindValue(':id_employe', $idEmploye, PDO::PARAM_INT); // Vérifie que $idEmploye est un entier
            $stmt->execute();

            $reservations=$stmt->fetchALL();
			$stmt->closeCursor();
			$stmt=null;
			$pdo=null;

			sendJSON($reservations, 200);
		} catch(PDOException $e){
			$infos['Statut']="KO";
			$infos['message']=$e->getMessage();
			sendJSON($infos, 500) ;
        }
    }

    function getIncident() {
        try {
            $pdo=connecteBD();
            $maRequete='SELECT id_incident,resume,description,service_technique
                                ,incident.id_reservation,intitule,reservation.date_reservation,
                                reservation.heure_debut,reservation.heure_fin, salle.nom,
                                incident.date_signalement,incident.heure_signalement
                                FROM incident
                                JOIN gravite
                                ON incident.id_gravite = gravite.id_gravite
                                JOIN reservation
                                ON incident.id_reservation = reservation.id_reservation
                                JOIN salle
                                ON reservation.id_salle = salle.id_salle';
        $stmt = $pdo->prepare($maRequete);										// Préparation de la requête
        $stmt->execute();	
            
        $incidents=$stmt ->fetchALL();
        $stmt->closeCursor();
        $stmt=null;
        $pdo=null;

        sendJSON($incidents, 200) ;
        
        }catch(PDOException $e){
			$infos['Statut']="KO";
			$infos['message']=$e->getMessage();
			sendJSON($infos, 500) ;
		}
    }
    function putIncident() {
        try {
            $pdo=connecteBD();
            $maRequete='SELECT';
        }catch(PDOException $e){
			$infos['Statut']="KO";
			$infos['message']=$e->getMessage();
			sendJSON($infos, 500) ;
		}
    }
    function postIncident($donneesJson) {
        if (!empty($donneesJson['RESUME'])
            && isset($donneesJson['SERVICE_TECHNIQUE']) // Remplacer empty() par isset()
            && !empty($donneesJson['ID_GRAVITE'])
            && !empty($donneesJson['ID_RESERVATION'])) {

			  // Données remplies, on insère dans la table client
			try {
				$pdo=connecteBD();
				$maRequete='INSERT INTO incident(RESUME, DESCRIPTION, SERVICE_TECHNIQUE, ID_GRAVITE, ID_RESERVATION, DATE_SIGNALEMENT, HEURE_SIGNALEMENT) 
                            VALUES (:RESUME, :DESCRIPTION, :SERVICE_TECHNIQUE, :ID_GRAVITE, :ID_RESERVATION, CURDATE(), CURTIME())';
				$stmt = $pdo->prepare($maRequete);						// Préparation de la requête
				$stmt->bindParam("RESUME", $donneesJson['RESUME']);				
				$stmt->bindParam("DESCRIPTION", $donneesJson['DESCRIPTION']);
				$stmt->bindParam("SERVICE_TECHNIQUE", $donneesJson['SERVICE_TECHNIQUE']);
				$stmt->bindParam("ID_GRAVITE", $donneesJson['ID_GRAVITE']);
				$stmt->bindParam("ID_RESERVATION", $donneesJson['ID_RESERVATION']);
				$stmt->execute();	
				
				$IdInsere=$pdo->lastInsertId() ;
					
				$stmt=null;
				$pdo=null;
				
				// Retour des informations au client (statut + id créé)
				$infos['Statut']="OK";
				$infos['ID']=$IdInsere;

				sendJSON($infos, 201) ;
			} catch(PDOException $e){
				// Retour des informations au client 
				$infos['Statut']="KO";
				$infos['message']=$e->getMessage();

				sendJSON($infos, 500) ;
			}
		} else {
			// Données manquantes, Retour des informations au client 
			$infos['Statut']="KO";
			$infos['message']="Données incomplètes";
			sendJSON($infos, 400) ;
		}
    }

    function getIncidentPourUneReservation($idReservation) {
        try {
            $pdo=connecteBD();
            $maRequete='SELECT id_incident,resume,description,service_technique
                                ,incident.id_reservation,intitule,reservation.date_reservation,
                                reservation.heure_debut,reservation.heure_fin, salle.nom,
                                incident.date_signalement,incident.heure_signalement
                                FROM incident
                                JOIN gravite
                                ON incident.id_gravite = gravite.id_gravite
                                JOIN reservation
                                ON incident.id_reservation = reservation.id_reservation
                                JOIN salle
                                ON reservation.id_salle = salle.id_salle
                                WHERE incident.id_reservation = :reservation';
        $stmt = $pdo->prepare($maRequete);						// Préparation de la requête
        $stmt->bindParam("reservation", $idReservation);				// Envoi du paramètre 1
        
        $stmt->execute();	
        $nb = $stmt->rowCount();
        
        $incident=$stmt ->fetchALL();
        $stmt->closeCursor();
        $stmt=null;
        $pdo=null;

        sendJSON($incident, 200) ;
        
        }catch(PDOException $e){
			$infos['Statut']="KO";
			$infos['message']=$e->getMessage();
			sendJSON($infos, 500) ;
		}
    }
?>