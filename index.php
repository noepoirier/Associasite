<?php
include ('menu.php');

// Récupération des éléments du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $assoc_name = $_POST["assoc_name"];
    $email = $_POST["email"];
    $mdp = $_POST["mdp"];
    $mdp_confirm = $_POST["mdp_confirm"];

    // Condition vérifiant le mdp saisi dans le formulaire
    if ($mdp !== $mdp_confirm) {
        echo "<script>alert('Erreur : Les mots de passe ne correspondent pas.');</script>";
    } else {
        $assoc_file = str_replace("+", "_", urlencode($assoc_name));
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $page_name ?></title>
        <link rel="shortcut icon" type="image/x-icon" href="<?php echo $logo ?>">
        <link href="style.css" rel="stylesheet"/>
    </head>
    <body>
        <div class="menu_site"><?php echo $menu_site ?></div>

        <div style="margin-bottom: 20px;"></div>
        
        <center><img class="logo" src="<?php echo $logo ?>"></center>

        <div style="margin-bottom: 30px;"></div>

        <div class="name_rectangle">
            <h1><?php echo $page_name ?></h1>
        </div>

        <div style="margin-bottom: 25px;"></div>

        <center><h2>Créez votre site pour votre association</h2></center>

        <div style="margin-bottom: 50px;"></div>

        <div class="separation_rectangle"></div>

        <div style="margin-bottom: 50px;"></div>

        <div class="content_rectangle">
            <div class="content">
                <center>
                    <h1>Renseignez vos informations</h1><br>
                    <div class="formulaire">
                        <form action="template.php" method="post">
                            <h3>
                                <label for="association_name">Nom de l'association :</label>
                                <input type="text" id="association_name" name="association_name" required><br>

                                <label for="mail_address">Adresse mail :</label>
                                <input type="text" id="mail_address" name="mail_address" required><br>

                                <label for="password">Mot de passe :</label>
                                <input type="password" id="password" name="password" required><br>

                                <label for="password_confirm">Retaper le mot de passe :</label>
                                <input type="password" id="password_confirm" name="password_confirm" required><br>
                            </h3>
                            <input class="bouton" type="submit" value="S'inscrire">
                        </form>
                    </div>
                </center>
            </div>
        </div>
    </body>
</html>