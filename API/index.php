<?php 
	require("json.php");
	require("donnees.php");

    $request_method = $_SERVER["REQUEST_METHOD"];  // GET / POST / DELETE / PUT
    switch($_SERVER["REQUEST_METHOD"]) {
		case "GET" :
            if (!empty($_GET['demande'])) {
				// décomposition URL par les / et  FILTER_SANITIZE_URL-> Supprime les caractères illégaux des URL
				$url = explode("/", filter_var($_GET['demande'],FILTER_SANITIZE_URL));
				
				switch($url[0]) {
					case 'toutesReservations' : // http://localhost/web/StatiSalle/API/index.php?demande=toutesReservations/E000001
						if (!empty($url[1])) {
							$idEmploye = $url[1];
							getReservation($idEmploye);
						} else {
							$infos['Statut']="KO";
							$infos['message']=$url[0]." employé pas trouvé";
							sendJSON($infos, 404) ;
						}
					break;
                    case 'tousIncidents' : // http://localhost/web/StatiSalle/API/index.php?demande=tousIncidents
						getIncident();
					break;
					case 'unIncident' : // http://localhost/web/StatiSalle/API/index.php?demande=unIncident/1
						if (!empty($url[1])) {
							$idReservation = $url[1];
							getIncidentPourUneReservation($idReservation);
						} else {
							$infos['Statut']="KO";
							$infos['message']=$url[0]." résérvation pas trouvé";
							sendJSON($infos, 404) ;
						}
					break;
                    default : 
						$infos['Statut']="KO";
						$infos['message']=$url[0]." inexistant";
						sendJSON($infos, 404) ;
				}
			} else {
				$infos['Statut']="KO";
				$infos['message']="URL non valide";
				sendJSON($infos, 404) ;
			}
        break;
        case "POST":
			if (!empty($_GET['demande'])) {
				// Décomposition de l'URL
				$url = explode("/", filter_var($_GET['demande'], FILTER_SANITIZE_URL));
		
				if ($url[0] === 'ajoutIncident') {
					// Récupération des données JSON envoyées dans la requête POST
					$inputJSON = file_get_contents("php://input");
					$donneesJson = json_decode($inputJSON, true);
		
					if ($donneesJson === null) {
						$infos['Statut'] = "KO";
						$infos['message'] = "Données JSON invalides";
						sendJSON($infos, 400);
						exit;
					}
		
					// Appel de la fonction postIncident avec les données JSON
					postIncident($donneesJson);

					/*
					{
						"RESUME": "Problème de connexion",
						"DESCRIPTION": "Impossible d'accéder à la plateforme",
						"SERVICE_TECHNIQUE": 0,
						"ID_GRAVITE": 2,
						"ID_RESERVATION": "R000002"
					}
					*/

					exit;
				}
			}
		
			// Si l'URL est invalide
			$infos['Statut'] = "KO";
			$infos['message'] = "URL non valide POST";
			sendJSON($infos, 404);
			break;
		case "PUT":
			if (!empty($_GET['demande'])) {
				// Décomposition de l'URL
				$url = explode("/", filter_var($_GET['demande'], FILTER_SANITIZE_URL));
		
				switch ($url[0]) {
					case 'modifIncident':
						if (!empty($url[1])) {
							$idIncident = $url[1];

							$input = file_get_contents("php://input");
							$donnees = json_decode($input, true);

							if (!isset($donnees['resume']) || !isset($donnees['service_technique']) || !isset($donnees['id_gravite'])) {
								$infos['Statut'] = "KO";
								$infos['message'] = "Données incomplètes";
								sendJSON($infos, 400);
							} else {
								putIncident($donnees['resume'], $donnees['description'], $donnees['service_technique'], $donnees['id_gravite'], $idIncident);
							}
						}
					break;
				}
			}
		// Si l'URL est invalide
		$infos['Statut'] = "KO";
		$infos['message'] = "URL non valide PUT";
		sendJSON($infos, 404);
		break;
	}	
		
?>