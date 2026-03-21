<?php
/**
 * globalpartnershipcouncil.php — Entry point
 * Letakkan di: /lending_word/globalpartnershipcouncil.php
 * (sama seperti career.php di root)
 */
require_once __DIR__ . '/app/database.php';
require_once __DIR__ . '/app/models/Content.php';
require_once __DIR__ . '/app/models/NavbarLink.php';
require_once __DIR__ . '/app/models/FooterSection.php';
require_once __DIR__ . '/app/models/GpcModel.php';

$gpc          = new GpcModel();
$partners     = $gpc->getActivePartners();
$cooperations = $gpc->getActiveCooperations();

$navbarModel        = new NavbarLink();
$footerSectionModel = new FooterSection();
$contentModel       = new Content();

$navbarLinks    = $navbarModel->getAll();
$footerSections = $footerSectionModel->getAllWithLinks();
$getContent     = fn($section, $key) => $contentModel->get($section, $key);
$socialLinks    = $footerSectionModel->getSocialLinks();

require_once __DIR__ . '/app/views/frontend/globalpartnershipcouncil.php';