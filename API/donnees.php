<?php
    include 'liaisonBD.php';
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
            $maReqete='SELECT'
        }
    }
    function postIncident() {
        try {
            $pdo=connecteBD();
            $maReqete='SELECT'
        }
    }
?>