<?php
    include '../fonction/liaisonBD.php';
    $pdo = connecteBD();

    function getReservation() {
        try {
            $pdo=connecteBD();
            $maReqete='SELECT reservation.id_reservation as id_reservation, salle.nom as nom_salle, employe.nom as nom_employe,
                        employe.prenom as prenom_employe, activite.nom_activite as nom_activite, reservation.date_reservation 
                        as date, reservation.heure_debut as heure_debut, reservation.heure_fin as heure_fin, reservation.id_employe as id_employe
                        FROM reservation
                        JOIN salle
                        ON reservation.id_salle = salle.id_salle
                        JOIN employe
                        ON reservation.id_employe = employe.id_employe
                        JOIN activite
                        ON reservation.id_activite = activite.id_activite
                        ORDER BY date DESC';
            $stmt = $pdo->prepare($maReqete);
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
            $maReqete='SELECT';
        }catch(PDOException $e){
			$infos['Statut']="KO";
			$infos['message']=$e->getMessage();
			sendJSON($infos, 500) ;
		}
    }
    function postIncident() {
        if(!empty($donneesJson['RESUME'])
			&& !empty($donneesJson['SERVICE_TECHNIQUE']) 
			&& !empty($donneesJson['ID_GRAVITE'])
			&& !empty($donneesJson['ID_RESERVATION'])
		  ){
			  // Données remplies, on insère dans la table client
			try {
				$pdo=getPDO();
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
            $maReqete='SELECT id_incident,resume,description,service_technique
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