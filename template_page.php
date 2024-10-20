<?php
include('db_config.php');

session_start();
$association_id = $_SESSION['association_id'];
if (isset($_FILES["association_logo"]) && $_FILES["association_logo"]["error"] != UPLOAD_ERR_NO_FILE) {
    $target_dir = "Associations/$association_id/";
    $target_file = $target_dir . basename($_FILES["association_logo"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Vérifie si le fichier est une image réelle ou une fausse image
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["association_logo"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $uploadOk = 0;
        }
    }

    // Vérifie si le fichier existe déjà
    if (file_exists($target_file)) {
        $_SESSION['alert'] = "Désolé, le fichier existe déjà.";
        $uploadOk = 0;
    }

    // Vérifie la taille du fichier
    if ($_FILES["association_logo"]["size"] > 500000) { // Limite de 500KB
        $_SESSION['alert'] = "Désolé, votre fichier est trop volumineux.";
        $uploadOk = 0;
    }

    // Autoriser certains formats de fichier
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $_SESSION['alert'] = "Désolé, seuls les fichiers JPG, JPEG& PNG sont autorisés.";
        $uploadOk = 0;
    }

    // Vérifie si $uploadOk est mis à 0 par une erreur
    if ($uploadOk == 0) {
        $_SESSION['alert'] = "Désolé, votre fichier n'a pas été téléchargé.";
    // Si tout est correct, essaye de télécharger le fichier
    } else {
        if (move_uploaded_file($_FILES["association_logo"]["tmp_name"], $target_file)) {
            $_SESSION['alert'] = "Le fichier ". htmlspecialchars(basename( $_FILES["association_logo"]["name"])). " a été téléchargé.";
            // Enregistre le chemin de l'image dans la base de données
            $association_logo = $target_file;
        } else {
            $_SESSION['alert'] = "Désolé, une erreur s'est produite lors du téléchargement de votre fichier.";
        }
    }
} else {
    // Si aucun fichier n'est téléchargé, ne rien faire ou afficher un message spécifique si nécessaire
    $association_logo = "";
}

$association_slogan = $_POST['association_slogan'];
$site_color = $_POST['site_color'];
$bg_color = $_POST['bg_color'];
$new_page_name = $_POST['new_page_name'];

// Requête SQL de mise à jour avec des paramètres facultatifs
$sql = "UPDATE Associations SET ";
$params = array();

$has_values = false;

if (!empty($association_logo)) {
    $sql .= "logo_url = ?, ";
    $params[] = $association_logo;
    $has_values = true;
}
if (!empty($association_slogan)) {
    $sql .= "slogan = ?, ";
    $params[] = $association_slogan;
    $has_values = true;
}
if (!empty($site_color)) {
    $sql .= "page_color = ?, ";
    $params[] = $site_color;
    $has_values = true;
}
if (!empty($bg_color)) {
    $sql .= "background_color = ?, ";
    $params[] = $bg_color;
    $has_values = true;
}

// Supprimer la dernière virgule et l'espace après les conditions
$sql = rtrim($sql, ", ");

if ($has_values) {
    $sql .= " WHERE association_id = ?";
    $params[] = $association_id;
    $stmt_associations = $connection->prepare($sql);
    $types = str_repeat("s", count($params) - 1) . "i";
    $stmt_associations->bind_param($types, ...$params);
    $stmt_associations->execute();
    $stmt_associations->close();
}

$stmt_check_page = $connection->prepare("SELECT COUNT(*) FROM Pages WHERE page_name = ? AND association_id = ?");
$stmt_check_page->bind_param("si", $new_page_name, $association_id);
$stmt_check_page->execute();
$stmt_check_page->bind_result($page_count);
$stmt_check_page->fetch();
$stmt_check_page->close();

if ($page_count > 0) {
    $_SESSION['alert'] = "Erreur : Une page avec ce nom existe déjà.";
} else {
    if (!empty($new_page_name)) {
        $stmt_get_max_order = $connection->prepare("SELECT MAX(page_order) AS max_order FROM Pages WHERE association_id = ?");
        $stmt_get_max_order->bind_param("i", $association_id);
        $stmt_get_max_order->execute();
        $result = $stmt_get_max_order->get_result();
        $row = $result->fetch_assoc();
        $max_order = $row['max_order'] ?? 0; // Si aucune page n'existe, définir max_order à 0
        $new_page_order = $max_order + 1;
        $stmt_get_max_order->close();

        $stmt_insert_page = $connection->prepare("INSERT INTO Pages (page_name, page_order, association_id) VALUES (?, ?, ?)");
        $stmt_insert_page->bind_param("sii", $new_page_name, $new_page_order, $association_id);
        $stmt_insert_page->execute();
        $stmt_insert_page->close();

        // Définir le chemin du fichier
        $file_path = "Associations/$association_id/";

        // Fonction pour récupérer le contenu du template avec les données fournies
        function get_template_page($new_page_order)
        {
            $template = '<?php
    include(\'menu.php\');
    $page_order = 0;
    ?>

    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title><?php echo $association_name ?></title>
            <link rel="shortcut icon" type="image/x-icon" href="<?php echo $logo ?>">
            <link href="../style.php?association_id=<?php echo $association_id ?>" rel="stylesheet" type="text/css"/>
        </head>
        <body>
            <div class="menu_site"><?php echo $menu_site ?></div>

            <div style="margin-bottom: 20px;"></div>

            <div class="page_name_rectangle">
                <?php if (!empty($logo_url)) : ?>
                    <img src="<?php echo $logo_url; ?>">
                <?php endif; ?>
                <h1><?php echo $page_name[$page_order] ?></h1>
            </div>

            <div style="margin-bottom: 25px;"></div>

            <center><h2><?php echo $slogan ?></h2></center>

            <div style="margin-bottom: 50px;"></div>

            <div class="separation_rectangle"></div>

            <div style="margin-bottom: 50px;"></div>

            <div class="content_rectangle">
                <div class="content">
                </div>
            </div>
        </body>
    </html>';

            return $template;
        }

        // Créer une nouvelle page
        $template_content = get_template_page($new_page_order);
        $file_name = $new_page_name . ".php";
        file_put_contents($file_path . $file_name, $template_content);
    }
}
header("Location: Gestion.php?association_id=$association_id");
exit;

$connection->close();
?>