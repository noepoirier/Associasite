<?php
include('db_config.php');

$association_name = $_POST['association_name'];
$mail_address = $_POST['mail_address'];
$password = $_POST['password'];
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt_associations = $connection->prepare("INSERT INTO Associations (association_name, mail_address, hash_password) VALUES (?, ?, ?)");
$stmt_associations->bind_param("sss", $association_name, $mail_address, $hashed_password);
$stmt_associations->execute();
$stmt_associations->close();

$association_id = $connection->insert_id;

$connection->close();

// Définir le chemin du fichier
$file_path = "Associations/$association_id/";

// Créer le répertoire du site de l'association
mkdir($file_path, 0777, true);

// Créer menu.php
$template_content = get_template_menu($association_id);
$file_name = 'menu.php';
file_put_contents($file_path . $file_name, $template_content);

// Créer index.php
$template_content = get_template_index();
$file_name = 'index.php';
file_put_contents($file_path . $file_name, $template_content);

// Créer donation.php
$template_content = get_template_donation();
$file_name = 'donation.php';
file_put_contents($file_path . $file_name, $template_content);

// Fonction pour récupérer le contenu du template avec les données fournies
function get_template_menu($association_id)
{
    $template = '<?php
include(\'../db_config.php\');

$association_id = ' . $association_id . ';

// Requête SQL pour récupérer le nom de l\'association associée à l\'ID de l\'association
$stmt_get_info = $connection->prepare("SELECT association_name, logo_url, slogan FROM Associations WHERE association_id = ?");
$stmt_get_info->bind_param("i", $association_id);
$stmt_get_info->execute();
$result = $stmt_get_info->get_result();

// Vérification si un résultat a été trouvé
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $association_name = $row[\'association_name\'];
    $logo_url = $row[\'logo_url\'];
    $slogan = $row[\'slogan\'];
}

$stmt_get_info->close();

// Obtient le nom de la page actuelle
$currentPage = basename($_SERVER[\'PHP_SELF\']);

// Crée le menu de navigation
$menu_site = "<ul>";
// Vérifier si l\'URL du logo est vide
if (!empty($logo)) {
    $menu_site .= "<li><img src=\'$logo\' width=\'50\' border-radius=\'45px\'></li>";
}
$menu_site .= "<li><a href=\"index.php\"" . ($currentPage === "index.php" ? \' class="active"\' : \'\') . ">Accueil</a></li>";
$menu_site .= "<li><a href=\"donation.php\"" . ($currentPage === "donation.php" ? \' class="active"\' : \'\') . ">Donation</a></li>";
    
$directory = __DIR__ . "/"; // Remplacez par le chemin réel de votre répertoire de pages

// Récupérer les noms des fichiers dans le répertoire
$files = scandir($directory);
$page_files = array_filter($files, function($file) {
    return pathinfo($file, PATHINFO_EXTENSION) === \'php\';
});

// Préparer une requête pour récupérer les informations des pages
$page_names = array_map(function($file) {
    return pathinfo($file, PATHINFO_FILENAME);
}, $page_files);

if (!empty($page_names)) {
    $placeholders = implode(\',\', array_fill(0, count($page_names), \'?\'));
    $types = str_repeat(\'s\', count($page_names));
    
    $stmt_get_pages = $connection->prepare("SELECT page_name, page_order FROM Pages WHERE association_id = ? AND page_name IN ($placeholders) ORDER BY page_order");
    $params = array_merge(array($association_id), $page_names);
    $types = \'i\' . $types;
    $stmt_get_pages->bind_param($types, ...$params);
    $stmt_get_pages->execute();
    $result = $stmt_get_pages->get_result();

    $page_name = array();

    while ($row = $result->fetch_assoc()) {
        $page_name[$row[\'page_order\']] = $row[\'page_name\'];
    }

    $stmt_get_pages->close();

    foreach ($page_name as $order => $page) {
        $menu_site .= "<li><a href=\"{$page}.php\"" . ($currentPage === "{$page}.php" ? \' class="active"\' : \'\') . ">{$page}</a></li>";
        
        // Mettre à jour le fichier avec le page_order
        $file_path = $directory . $page . \'.php\';
        if (file_exists($file_path)) {
            $file_contents = file_get_contents($file_path);
            if ($file_contents !== false) {
                // Ajouter ou mettre à jour le code pour afficher le page_order
                $updated_contents = preg_replace(\'/\$page_order = \d+;/\', \'$page_order = \' . $order . \';\', $file_contents);
                if ($updated_contents === null) {
                    $updated_contents = $file_contents; // Si preg_replace échoue, on garde le contenu original
                }
                file_put_contents($file_path, $updated_contents);
            }
        }
    }
}

$menu_site .= "</ul>";
?>';

    return $template;
}

// Fonction pour récupérer le contenu du template avec les données fournies
function get_template_index()
{
    $template = '<?php
include(\'menu.php\');
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
        
        <center>
            <?php if (!empty($logo)) : ?>
                <img class="logo" src="<?php echo $logo; ?>">
            <?php endif; ?>
        </center>

        <div style="margin-bottom: 30px;"></div>

        <div class="name_rectangle">
            <h1><?php echo $association_name ?></h1>
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

// Fonction pour récupérer le contenu du template avec les données fournies
function get_template_donation()
{
    $template = '<?php
include(\'menu.php\');
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
        
        <center>
            <?php if (!empty($logo)) : ?>
                <img class="logo" src="<?php echo $logo; ?>">
            <?php endif; ?>
        </center>

        <div style="margin-bottom: 30px;"></div>

        <div class="page_name_rectangle">
            <h1>Donation</h1>
        </div>

        <div style="margin-bottom: 25px;"></div>

        <center><h2>Effectuer un don</h2></center>

        <div style="margin-bottom: 50px;"></div>

        <div class="separation_rectangle"></div>

        <div style="margin-bottom: 50px;"></div>

        <div class="content_rectangle">
            <div class="content">
                <center>
                    <iframe id="haWidget" allowtransparency="true" scrolling="auto" src="<?php echo $donation_link ?>" style="width: 100%; height: 750px; border: none;"></iframe>
                </center>
            </div>
        </div>
    </body>
</html>';

    return $template;
}

header("Location: Connexion.php");
exit;
?>

// https://www.helloasso.com/associations/associa/formulaires/1/widget-bouton