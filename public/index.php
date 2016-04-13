<?php

if(strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false)
{
    // Boot laravel framework
    require_once 'l-index.php';
} else
{    
	require_once '../vendor/vlucas/phpdotenv/src/Dotenv.php';
	
	// Loading env variable
	Dotenv::load(__DIR__ .'/../');
	
    // Boot wordpress
    require_once 'wp-index.php'; 
}
