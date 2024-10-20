<?php
include('menu.php');
include('db_config.php');

if (isset($_GET['association_id'])) {
    $association_id = $_GET['association_id'];
    $_SESSION['association_id'] = $association_id;
} else {
    header("Location: Connexion.php?error=not_logged_in");
    exit;
}

$stmt_get_name = $connection->prepare("SELECT association_name, logo_url FROM Associations WHERE association_id = ?");
$stmt_get_name->bind_param("i", $association_id);
$stmt_get_name->execute();
$result = $stmt_get_name->get_result();
$row = $result->fetch_assoc();
$association_name = $row['association_name'];
$logo_url = $row['logo_url'];
$stmt_get_name->close();

$couleurs = array(
    "" => "",
    "black" => "Noir",
    "silver" => "Argent",
    "gray" => "Gris",
    "white" => "Blanc",
    "maroon" => "Marron",
    "red" => "Rouge",
    "purple" => "Violet",
    "fuchsia" => "Fuchsia",
    "green" => "Vert",
    "lime" => "Citron vert",
    "olive" => "Olive",
    "yellow" => "Jaune",
    "navy" => "Bleu marine",
    "blue" => "Bleu",
    "royalblue" => "Bleu royal",
    "teal" => "Sarcelle",
    "aqua" => "Aqua"
);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $page_name ?></title>
        <link rel="shortcut icon" type="image/x-icon" href="<?php echo $logo ?>">
        <link href="style.css" rel="stylesheet"/>
        <script>
            <?php if(isset($_SESSION['alert'])): ?>
                alert("<?php echo $_SESSION['alert']; ?>");
            <?php endif; ?>
        </script>
    </head>
    <body>
        <div class="menu_site"><?php echo $menu_site ?></div>

        <div style="margin-bottom: 20px;"></div>

        <div class="gestion_name">
            <?php if (!empty($logo_url)) : ?>
                <img class="logo" src="<?php echo $logo_url; ?>">
            <?php endif; ?>
            <h1><?php echo $association_name ?></h1>
        </div>

        <div style="margin-bottom: 25px;"></div>

        <center><h2>Gérez votre site</h2></center>

        <div style="margin-bottom: 50px;"></div>

        <div class="separation_rectangle"></div>

        <div style="margin-bottom: 50px;"></div>

        <div class="content_rectangle">
            <div class="content">
                <center>
                    <div class="formulaire">
                        <form action="template_page.php" method="post" enctype="multipart/form-data">
                            <h2 style="margin-bottom: 50px">Configuration du site</h2>
                            <div class="add_items">
                                <div>
                                    <h3>Ajouter un logo</h3>
                                    <h4>
                                        <label for="association_logo" class=logo_upload>Choisir un fichier</label>
                                        <input type="file" id="association_logo" name="association_logo" accept="image/*"><br>
                                    </h4>
                                </div>
                                <div>
                                    <h3>Ajouter un slogan</h3>
                                    <h4>
                                        <label for="association_slogan">Slogan :</label>
                                        <input type="text" id="association_slogan" name="association_slogan"><br>
                                    </h4>
                                </div>
                            </div>

                            <div style="margin-bottom: 75px;"></div>

                            <h3>Configurer les couleurs</h3>
                            <h4>
                                <div class="color_select">
                                    <div>
                                        <label for="site_color">Couleur du site :</label>
                                        <select name="site_color" style="margin-right: 10%">
                                            <?php foreach ($couleurs as $color => $name) : ?>
                                                <option value="<?php echo $color; ?>"><?php echo ucfirst($name); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="bg_color">Couleur de fond :</label>
                                        <select name="bg_color">
                                            <?php foreach ($couleurs as $color => $name) : ?>
                                                <option value="<?php echo $color; ?>"><?php echo ucfirst($name); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </h4>

                            <div style="margin-bottom: 75px;"></div>

                            <h3>Ajouter une page</h3>
                            <h4>
                                <label for="new_page_name">Nom de la page :</label>
                                <input type="text" id="new_page_name" name="new_page_name"><br>
                            </h4>
                            <input class="bouton" type="submit" value="Appliquer les changements">
                        </form>
                    </div>
                </center>
            </div>
        </div>
    </body>
</html>