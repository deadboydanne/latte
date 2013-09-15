<?php
//
// PHASE: BOOTSTRAP
//
define('LATTE_INSTALL_PATH', dirname(__FILE__));
define('LATTE_SITE_PATH', LATTE_INSTALL_PATH . '/site');

require(LATTE_INSTALL_PATH.'/src/CLatte/bootstrap.php');

$lr = CLatte::Instance();

//
// PHASE: FRONTCONTROLLER ROUTE
//
$lr->FrontControllerRoute();


//
// PHASE: THEME ENGINE RENDER
//
$lr->ThemeEngineRender();