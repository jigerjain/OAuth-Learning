<?php

// Fill out values from Github
$githubClientID = '8771161b48146cb81269';
$githubClientSecret = 'f6f913f55c81b1b9896d02dfcbf752b70d701745';

// This is the url we will send the user to first get their authorization
$authorizeURL = "https://github.com/login/oauth/authorize";

// This is the endpoint we will request an access token form
$token_URL = "https://github.com/login/oauth/access_token";

// This is the Github base URL for API Requests
$apiURLBase = "https://api.github.com/";


// This URL for this script, used as the redirect URL
$baseURL = "http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];

//Start a session so we have a place to store things between redirects 
session_start();

// Start the login process by sending the user to the Github Login page
if(isset($_GET['action']) && $_GET['action']=='login'){
    unset($_SESSION['access_token']);

    // Generate a random hash:
    $_SESSION['state'] = bin2hex(random_bytes(16));

    $params = array(
        'response_type' => 'code',
        'client_id' => $githubClientID,
        'redirect_uri' => $baseURL,
        'scope' => 'user public_group',
        'state' => $_SESSION['state']
    );

    // Redirect the user to Github's auth page:
    header('location: '.$authorizeURL.'?'.http_build_query($params));
    die();
}

// Logout Functionality
if(isset($_GET['action']) && $_GET['action']=='logout'){
    unset($_SESSION['access_token']);
    header('location: '.$baseURL);
    die();
}

// Checking Code and State Parameter for generating token
if(isset($_GET['code'])){
    // Verify the state of the return parameter
    if ( !isset($_GET['state']) || $_GET['state']!=$_SESSION['state'])
    {
        header('location: '.$baseURL.'?error=invalid_state');
        die();
    }

    //Exchange the auth code for access token
    $token = apiRequest($token_URL, array(
        'grant_type' => 'authorization_code',
        'client_id' => $githubClientID,
        'client_secret' => $githubClientSecret,
        'redirect_uri' => $baseURL,
        'code' => $_GET['code']
    ));
    $_SESSION['access_token'] = $token['access_token'];
    header('location: '.$baseURL);
    
    die();
}

// Calling for repositories as part of using token and calling the Resource Server
if(isset($_GET['action']) && $_GET['action']=='repos'){
    // Find all repos created by the authenticated user
    $repos = apiRequest($apiURLBase.'user/repos?'.http_build_query(['sort'=>'created', 'direction' => 'desc']));

    echo '<ul>';
    foreach($repos as $repo){
       echo '<li> <a href="' . $repo['html_url']. '"> ' .$repo['name'] . '</a></li>';
    }
    echo '</ul>'; 
}

// Base page
if(!isset($_GET['action'])){
    if(!empty($_SESSION['access_token'])){
        echo '<h3>logged in </h3>';
        echo '<p><a href="?action=repos"> View Repos </a> </p>';

        echo '<p><a href="?action=logout"> Logout </a> </p>';
        }  
    else{
        echo '<h3> Not logged in </h3>';
        echo '<p><a href="?action=login"> Log In </a> </p>';
    }
    die();
}

function apiRequest($url, $post=FALSE, $headers=array()){
    $ch = curl_init($url);
    if($post)
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    
    $headers = [
        'Accept: application/vnd.github.v3+json, application/json',
        'User-Agent: http://helloworld:80'
    ];

    if(isset($_SESSION['access_token']))
        $headers[] = 'Authorization: Bearer '.$_SESSION['access_token'];
    
    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_PROXY   => '127.0.0.1',
        CURLOPT_PROXYPORT => '8080',
        CURLOPT_HTTPHEADER => $headers
    );

    /*
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_PROXY , '127.0.0.1');
    curl_setopt($ch, CURLOPT_PROXYPORT, '8080');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    */
    
    curl_setopt_array($ch, $options);

    $response = curl_exec($ch);
    return json_decode($response, true);
}

