<?php
/**
 * Career — /lending_word/career.php
 */
require_once __DIR__ . '/app/database.php';
require_once __DIR__ . '/app/models/Content.php';
require_once __DIR__ . '/app/models/NavbarLink.php';
require_once __DIR__ . '/app/models/FooterSection.php';
require_once __DIR__ . '/app/models/CareerModel.php';

$careerModel = new CareerModel();

$categories       = $careerModel->getCategories();
$jobs             = $careerModel->getActiveJobs();
$subsidiaries     = $careerModel->getSubsidiaries();
$entryStudents    = $careerModel->getEntryCards('students');
$entryExperienced = $careerModel->getEntryCards('experienced');

$navbarModel        = new NavbarLink();
$footerSectionModel = new FooterSection();
$contentModel       = new Content();

$navbarLinks    = $navbarModel->getAll();
$footerSections = $footerSectionModel->getAllWithLinks();
$getContent     = fn($section, $key) => $contentModel->get($section, $key);
$socialLinks    = $footerSectionModel->getSocialLinks();

require_once __DIR__ . '/app/views/frontend/career.php';