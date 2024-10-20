<?php
include('db_config.php');

$page_name = "AssociaSite";
$logo = "AssociaSite.jpg";

// Liste des pages
$page_associasite = array(
    "Connexion",
    "Gestion"
);

// Obtient le nom de la page actuelle
$currentPage = basename($_SERVER['PHP_SELF']);

// Crée le menu de navigation
$menu_site = "<ul>";
$menu_site .= "<li><img src='AssociaSite.jpg' width='50' border-radius='45px'></li>";
$menu_site .= "<li><a href=\"index.php\"" . ($currentPage === 'index.php' ? ' class="active"' : '') . ">Accueil</a></li>";
foreach ($page_associasite as $page) {
    $menu_site .= "<li><a href=\"{$page}.php\"" . ($currentPage === "{$page}.php" ? ' class="active"' : '') . ">{$page}</a></li>";
}

// Vérifie si l'ID de l'association est défini dans la session ou l'URL
if (isset($_GET['association_id'])) {
    $association_id = $_GET['association_id'];

    $menu_site .= "<div class=separation_menu></div>";
    $menu_site .= "<li><a href=\"Associations/{$association_id}/index.php\"" . ($currentPage === "Associations/{$association_id}/index.php" ? ' class="active"' : '') . ">Accueil</a></li>";
    
    // Préparer la requête pour récupérer les pages spécifiques à l'association
    $stmt_get_pages = $connection->prepare("SELECT page_name, page_order FROM Pages WHERE association_id = ? ORDER BY page_order");
    $stmt_get_pages->bind_param("i", $association_id);
    $stmt_get_pages->execute();
    $result = $stmt_get_pages->get_result();

    $pages_site = array();
    
    // Ajouter les pages de l'association au menu
    while ($row = $result->fetch_assoc()) {
        $pages_site[$row['page_order']] = $row['page_name'];
    }
    $stmt_get_pages->close();

    foreach ($pages_site as $order => $page_site) {
        $menu_site .= "<li><a href=\"Associations/{$association_id}/{$page}.php\"" . ($currentPage === "Associations/{$association_id}/{$page_site}.php" ? ' class="active"' : '') . ">{$page_site}</a></li>";
    }
    
    session_start();
    $_SESSION['pages_site'] = $pages_site;
}

$menu_site .= "</ul>";
?>