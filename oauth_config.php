<?php
/**
 * OAuth Configuration
 * Điền thông tin OAuth credentials của bạn vào đây
 */

return [
    'google' => [
        'client_id' => 'YOUR_GOOGLE_CLIENT_ID',
        'client_secret' => 'YOUR_GOOGLE_CLIENT_SECRET',
        'redirect_uri' => 'http://localhost:8080/vnmt/oauth_callback.php?provider=google',
        'auth_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
        'token_url' => 'https://oauth2.googleapis.com/token',
        'user_info_url' => 'https://www.googleapis.com/oauth2/v2/userinfo',
        'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile'
    ],
    'facebook' => [
        'app_id' => 'YOUR_FACEBOOK_APP_ID',
        'app_secret' => 'YOUR_FACEBOOK_APP_SECRET',
        'redirect_uri' => 'http://localhost:8080/vnmt/oauth_callback.php?provider=facebook',
        'auth_url' => 'https://www.facebook.com/v18.0/dialog/oauth',
        'token_url' => 'https://graph.facebook.com/v18.0/oauth/access_token',
        'user_info_url' => 'https://graph.facebook.com/v18.0/me',
        'scope' => 'email,public_profile'
    ]
];
?>

