<?php
include('menu.php');
include('db_config.php');

// Récupération des éléments du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt_get_pwd = $connection->prepare("SELECT association_id, hash_password FROM Associations WHERE mail_address = ?");
    $stmt_get_pwd->bind_param("s", $email);
    $stmt_get_pwd->execute();
    $result = $stmt_get_pwd->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $association_id = $row['association_id'];
        $hash_password = $row['hash_password'];

        // Vérification si le mot de passe correspond au mot de passe haché dans la base de données
        if (password_verify($password, $hash_password)) {
            header("Location: Gestion.php?association_id=$association_id");
            exit;
        } else {
            echo "<script>alert('Erreur : Le mot de passe saisi est incorrect.');</script>";
        }
    } else {
        echo "<script>alert('Erreur : L\'adresse e-mail saisie est incorrecte.');</script>";
    }
    
    $stmt_connection->close();
}

if (isset($_GET['error']) && $_GET['error'] == 'not_logged_in') {
    echo "<script>alert('Connectez-vous afin d\'accéder à la page de gestion.');</script>";
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

        <center><h2>Connectez-vous afin de gérer le site de votre association</h2></center>

        <div style="margin-bottom: 50px;"></div>

        <div class="separation_rectangle"></div>

        <div style="margin-bottom: 50px;"></div>

        <div class="content_rectangle">
            <div class="content">
                <center>
                    <h1>Accédez à votre espace de gestion</h1><br>

                    <div class="formulaire">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <h3>
                                <label for="email">Adresse mail :</label>
                                <input type="text" id="email" name="email" required><br>

                                <label for="password">Mot de passe :</label>
                                <input type="password" id="password" name="password" required><br>
                            </h3>
                            <input class="bouton" type="submit" value="Se connecter">
                        </form>
                    </div>
                </center>
            </div>
        </div>
    </body>
</html>