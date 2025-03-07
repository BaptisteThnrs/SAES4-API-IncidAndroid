<?php
    $startTime = microtime(true); // temps de chargement de la page
    include '../fonction/employe.php';
    include '../fonction/connexion.php';

    session_start();
    verif_session();

    // Initialisation des variables et messages d'erreur
    $nom = $prenom = $numTel = $login = $mdp = $cmdp = $messageSucces = "";
    $erreurs = [];

    //vérifie si la modification est souhaitée
    $modifie = $_POST['modifie'] ?? false;
    $id      = $_POST['id_employe'] ?? null;

    // Récupération des attributs de l'employé en fonction de l'id de l'employé
    $tabAttributEmploye = recupAttributEmploye($id);

    // Récupération des attributs du login en fonction de l'id de l'employé
    $tabAttributLogin = recupAttributLogin($id);

if (isset($_POST['modifier'])) {
    // Récupération de la valeur de la case admin
    $admin = $_POST['admin'] ?? false; // Par défaut, pas d'admin
    $id_type = $admin ? 1 : 2;  // Si admin est coché, id_type = 1 (admin), sinon 2 (employé).

    $nom    = htmlspecialchars($_POST['nom'])    ?? '';
    $prenom = htmlspecialchars($_POST['prenom']) ?? '';
    $numTel = htmlspecialchars($_POST['numTel']) ?? '';
    $login  = htmlspecialchars($_POST['login'])  ?? '';
    $mdp    = isset($_POST['mdp']) && $_POST['mdp'] !== '' ? htmlspecialchars($_POST['mdp']) : null;
    $cmdp   = isset($_POST['cmdp']) && $_POST['cmdp'] !== '' ? htmlspecialchars($_POST['cmdp']) : null;

    // Vérification des champs requis
    if (!isset($nom) || $nom === '')    $erreurs['nom'] = "Le nom est requis.";
    if (!isset($prenom) || $prenom === '') $erreurs['prenom'] = "Le prénom est requis.";
    if (!isset($numTel) || $numTel === '') $erreurs['numTel'] = "Le numéro de téléphone est requis.";
    if (!isset($login) || $login === '')  $erreurs['login'] = "Le login est requis.";

    // Vérification des longueurs des champs
    if (strlen($numTel) < 4 || strlen($numTel) > 10) {
        $erreurs['numTel'] = "Le numéro de téléphone doit contenir entre 4 et 10 caractères.";
    }
    if (strlen($nom) < 1 || strlen($nom) > 50) {
        $erreurs['nom'] = "Le nom doit contenir entre 1 et 50 caractères.";
    }
    if (strlen($prenom) < 1 || strlen($prenom) > 50) {
        $erreurs['prenom'] = "Le prénom doit contenir entre 1 et 50 caractères.";
    }

    // Vérification que le numéro de téléphone contient uniquement des chiffres
    if (!ctype_digit($numTel)) {
        $erreurs['numTel'] = "Le numéro de téléphone doit contenir uniquement des chiffres.";
    }

    // Vérification du login uniquement s'il a été modifié
    if ($login !== $tabAttributLogin['login']) {
        if (verifLoginExiste($login)) {
            $erreurs['login'] = "Ce login existe déjà. Veuillez en choisir un autre.";
        }
    }

    // Vérification des mots de passe si modifiés
    if ($mdp !== null) {
        if ($mdp !== $cmdp) {
            $erreurs['cmdp'] = "Les mots de passe ne correspondent pas.";
        }
        if (!verifMdp($mdp)) {
            $erreurs['mdp'] = "Le mot de passe doit faire plus de 8 caractères et contenir un caractère spécial, par exemple : @, #, $, %, & ou *.";
        }
    }

    // Si aucune erreur, on effectue la modification
    if (empty($erreurs)) {
        try {
            // Si un mot de passe a été fourni, on le modifie, sinon on le laisse inchangé
            if ($mdp !== null) {
                modifierEmploye($id, $nom, $prenom, $login, $numTel, $mdp, $id_type);
            } else {
                modifierEmployeSansMdp($id, $nom, $prenom, $login, $numTel, $id_type);
            }

            // Actualisation des données pour affichage
            $tabAttributEmploye = recupAttributEmploye($id);
            $tabAttributLogin = recupAttributLogin($id);

            $messageSucces = "Employé modifié avec succès !";
        } catch (Exception $e) {
            $erreurs[] = "Impossible de modifier l'employé dans la base de données : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>StatiSalle - Modifications Employés</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <!-- CSS -->
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="../css/header.css">
        <link rel="stylesheet" href="../css/footer.css">
        <!-- Icon du site -->
        <link rel="icon" href=" ../img/logo.ico">
    </head>
    <body>
        <div class="container-fluid">
            <!-- Header de la page -->
            <?php include '../include/header.php'; ?>

            <div class="full-screen">
                <!-- Titre de la page -->
                <div class="padding-header row">
                    <div class="text-center">
                        <br>
                        <h1>Modifications d'un employé</h1>
                    </div>
                </div>

                <!-- Affichage des erreurs globales seulement après soumission -->
                <?php if (!empty($erreurs)): ?>
                    <div class="row">
                        <div class="col-md-6 offset-md-3">
                            <div class="alert alert-danger">
                                <ul>
                                    <?php foreach ($erreurs as $erreur): ?>
                                        <li><?= $erreur ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Affichage du message de succès -->
                <?php if ($messageSucces): ?>
                    <div class="row">
                        <div class="col-md-6 offset-md-3">
                            <div class="alert alert-success">
                                <?= $messageSucces ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Contenu de la page -->
                <div class="container">
                    <form method="POST" action="modificationEmploye.php">
                        <!-- Nom et prénom -->
                        <div class="row">
                            <div class="col-md-3 offset-md-3">
                                <label for="nom"></label><input class="form-text form-control" type="text" placeholder="Nom" id="nom" name="nom" value="<?= htmlentities($tabAttributEmploye['nom']) ?>" required>
                                <?php if (isset($erreurs['nom'])): ?>
                                    <small class="text-danger"><?= $erreurs['nom'] ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-3">
                                <label for="prenom"></label><input class="form-text form-control" type="text" placeholder="Prénom" id="prenom" name="prenom" value="<?= htmlentities($tabAttributEmploye['prenom']) ?>" required>
                                <?php if (isset($erreurs['prenom'])): ?>
                                    <small class="text-danger"><?= $erreurs['prenom'] ?></small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Numéro de tel -->
                        <div class="row">
                            <div class="col-md-6 offset-md-3">
                                <label for="numTel"></label><input class="form-text form-control" type="text" placeholder="Numéro de téléphone" id="numTel" name="numTel" value="<?= htmlentities($tabAttributEmploye['telephone'], ENT_QUOTES) ?>" required>
                                <?php if (isset($erreurs['numTel'])): ?>
                                    <small class="text-danger"><?= $erreurs['numTel'] ?></small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- login -->
                        <div class="row">
                            <div class="col-md-6 offset-md-3">
                                <label for="login"></label><input class="form-text form-control" type="text" placeholder="Compte utilisateur" id="login" name="login" value="<?= htmlentities($tabAttributLogin['login'], ENT_QUOTES) ?>" required>
                                <?php if (isset($erreurs['login'])): ?>
                                    <small class="text-danger"><?= $erreurs['login'] ?></small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Mdp -->
                        <div class="row">
                            <div class="col-md-6 offset-md-3">
                                <label for="mdp"></label><input class="form-text form-control" type="password" placeholder="Mot de passe" id="mdp" name="mdp" value="">
                                <?php if (isset($erreurs['mdp'])): ?>
                                    <small class="text-danger"><?= $erreurs['mdp'] ?></small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Confirmation mot de passe -->
                        <div class="row">
                            <div class="col-md-6 offset-md-3">
                                <label for="cmdp"></label><input class="form-text form-control" type="password" placeholder="Confirmez le mot de passe" id="cmdp" name="cmdp" value="">
                                <?php if (isset($erreurs['cmdp'])): ?>
                                    <small class="text-danger"><?= $erreurs['cmdp'] ?></small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Case à cocher pour les permissions administratives -->
                        <div class="row mt-3">
                            <div class="col-md-6 offset-md-3">
                                <label for="admin">Permissions administratives</label>
                                <?php $checked = ($tabAttributLogin['id_type'] == 1) ? 'checked' : ''; ?>
                                <input type="checkbox" id="admin" name="admin" value="<?= $tabAttributLogin['id_type'] ?>" <?= $checked?> >
                                <small class="text-muted">Cochez cette case si l'employé a des permissions administratives.</small>
                            </div>
                        </div>

                        <!-- Boutton envoyer le formulaire -->
                        <div class="row mt-4">
                            <div class="col-md-6 offset-md-3">
                                <input type="hidden" id="modifier" name="modifier" value="true">
                                <input type="hidden" id="id_employe" name="id_employe" value="<?= $id ?>">
                                <button type="submit" class="btn-bleu rounded w-100">Modifier le compte</button>
                            </div>
                        </div>

                        <!-- Boutton retour -->
                        <br class="d-md-block d-none">
                        <div class="row mt-4 offset-md-2">
                            <div class="col-12 col-md-auto">
                                <button class="btn-suppr rounded-2 w-100 w-md-auto" type="button"
                                        onclick="window.location.href='affichageEmploye.php'">
                                    Retour
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Footer de la page -->
            <?php include '../include/footer.php'; ?>
        </div>
    </body>
</html>
