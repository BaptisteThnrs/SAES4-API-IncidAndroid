<?php
class Authentification {
    private $pdo;

    public function __construct() {
        $this->pdo = connecteBD();
    }

    public function authentification() {
        // Vérifie si la clé API est fournie dans les en-têtes HTTP
        if (!isset($_SERVER["HTTP_APIKEY"])) {
            $this->sendError("Authentification nécessaire par APIKEY.", 401);
        }

        $cleAPI = $_SERVER["HTTP_APIKEY"];

        try {
            // Vérifie si la clé API existe dans la base
            $requete = "SELECT id_login FROM login WHERE api_key = :api_key";
            $stmt = $this->pdo->prepare($requete);
            $stmt->bindParam(':api_key', $cleAPI);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $this->sendError("APIKEY invalide.", 403);
            }
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
        }
    }

    public function verifLoginPassword($login, $password) {
        try {
            $password = sha1($password);
            var_dump($login);
            var_dump($password);
            $requete = "SELECT id_login, id_employe FROM login WHERE login = :identifiant AND mdp = :mdp";
            $stmt = $this->pdo->prepare($requete);
            $stmt->bindParam(':identifiant', $login);
            $stmt->bindParam(':mdp', $password);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Générer une clé API sécurisée
                $apiKey = substr(bin2hex(random_bytes(6)), 0, 12);

                // Vérifier si l'utilisateur a déjà une clé API
                $checkApiKey = $this->pdo->prepare("SELECT api_key FROM login WHERE id_login = :id_login");
                $checkApiKey->execute(['id_login' => $user['id_login']]);
                $existingApiKey = $checkApiKey->fetch(PDO::FETCH_ASSOC);

                if ($existingApiKey) {
                    // Mise à jour de la clé API existante
                    $update = "UPDATE login SET api_key = :api_key WHERE id_login = :id_login";
                    $stmt = $this->pdo->prepare($update);
                } else {
                    // Insertion d'une nouvelle clé API (ERREUR SQL CORRIGÉE)
                    $insert = "UPDATE login SET api_key = :api_key WHERE id_login = :id_login";
                    $stmt = $this->pdo->prepare($insert);
                }

                // Exécuter la requête (UPDATE uniquement, INSERT corrigé)
                $stmt->execute(['id_login' => $user['id_login'], 'api_key' => $apiKey]);

                // Retourner la clé API et l'ID employé
                $infos['APIKEY'] = $apiKey;
                $infos['id_employe'] = $user['id_employe'];
                $this->sendJSON($infos, 200);
            } else {
                // Login incorrect
                $infos['Statut'] = "KO";
                $infos['message'] = "Logins incorrects.";
                $this->sendJSON($infos, 401);
            }
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
        }
    }

    private function sendJSON($data, $status = 200) {
        header("Content-Type: application/json");
        http_response_code($status);
        echo json_encode($data);
        exit();
    }
}
?>
