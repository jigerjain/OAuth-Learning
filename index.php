<?php

function apiRequest($url, $post=FALSE, $headers=array()){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    if($post)
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    
    $headers = [
        'Accept: application/vnd.github.v3+json, application/json',
        'User-Agent: https://exmaple-app.com'
    ];

    if(isset($_SESSION['access_token']))
        $headers[] = 'Authorization: Bearer '.$_SESSION['access_token'];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    return json_decode($response, true);
}

// Fill out values from Github
$githubClientID = '';
$githubClientSecret = '';

// This is the url we will send the user to first get their authorization
$authorizeURL = "https://github.com/login/oauth/authorize";

// This is the endpoint we will request an access token form
$token_URL = "https://github.com/login/oauth/access_token";

// This is the Github base URL for API Requests
$apiURLBase = "https://api.github.com";

// This URL for this script, used as the redirect URL
$baseURL = "https://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];

//Start a session so we have a place to store things between redirects 
session_start();

if(!isset($_GET['action'])){
    if(!empty($_SESSION['access_token'])){
        echo '<h3>logged in </h3>';
        echo '<p><a href="?action=repos"> View Repos </a> </p>';
        }
        
    else{
        echo '<h3> Not logged in </h3>';
        echo '<p><a href="?action=login"> Log In </a> </p>';
    }
    die();
}


// Start the login process by sending the user to the Github Login page

if(isset($_GET['action']) && $_GET['action']=='login'){
    unset($_SESSION['access_token']);
}

// Generate a random hash:
$_SESSION['state'] = bin2hex(random_bytes(16));

$params = array(
    'response_type' => 'code',
    'client_id' => $githubClientID,
    'redirect_uri' => $baseURL,
    'scope' => 'user public_group',
    'state' => $_SESSION['state']
);

#Redirect the user to Github's auth page:

header('location: '.$authorizeURL.'?'.http_build_query($params));
die();

