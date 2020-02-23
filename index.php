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
sessions_starts();

