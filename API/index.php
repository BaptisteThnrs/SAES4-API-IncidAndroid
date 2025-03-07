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
					case 'toutesReservations' :
						getReservation();
					    break;
                    case 'tousIncidents' :
						getIncident();
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
        case "POST" :
            if (!empty($_GET['demande'])) {
				// décomposition URL par les / et  FILTER_SANITIZE_URL-> Supprime les caractères illégaux des URL
				$url = explode("/", filter_var($_GET['demande'],FILTER_SANITIZE_URL));
				
				if ($url[0] === 'ajoutIncident' && !empty($url[1]) && !empty($url[2]) && !empty($url[3]) && !empty($url[4]) && !empty($url[5])) {
                    // récupération des parametre dans l'url
					$resume = $url[1];
					$description = $url[2];
					$serviceTechnique = $url[3];
					$idGravite = $url[4];
					$idReservation = $url[5];
				}
			} else {
				$infos['Statut']="KO";
				$infos['message']="URL non valide";
				sendJSON($infos, 404) ;
			}

            $stmt = postIncident($resume, $description, $serviceTechnique, $idGravite, $idReservation);
		
            if ($stmt->rowCount() > 0) {
                $infos['Statut'] = "OK";
                $infos['message'] = "incident ajouté avec succès";
                sendJSON($infos, 201);
            } else {
                $infos['Statut'] = "KO";
                $infos['message'] = "Erreur lors de l'ajout de l'incident";
                sendJSON($infos, 404);
            }

        default :
		$infos['Statut']="KO";
		$infos['message']="URL non valide";
		sendJSON($infos, 404);
    }
?>