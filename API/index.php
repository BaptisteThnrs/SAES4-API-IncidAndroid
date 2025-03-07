<?php 
	require("json.php");
	require("donnee.php");

    $request_method = $_SERVER["REQUEST_METHOD"];  // GET / POST / DELETE / PUT
    switch($_SERVER["REQUEST_METHOD"]) {
		case "GET" :
            if (!empty($_GET['demande'])) {
				// décomposition URL par les / et  FILTER_SANITIZE_URL-> Supprime les caractères illégaux des URL
				$url = explode("/", filter_var($_GET['demande'],FILTER_SANITIZE_URL));
				
				switch($url[0]) {
					case 'toutesReservations' :
						affichageReservation();
					    break;
                    case 'TousIncidents' :
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
        default :
		$infos['Statut']="KO";
		$infos['message']="URL non valide";
		sendJSON($infos, 404);
    }
?>