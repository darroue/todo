<?php

return [
    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    'confirm_password' => [
        'title' => 'Confirm your password',
        'description' => 'This is a secure area of the application. Please confirm your password before continuing.',
        'button' => 'Confirm password',
    ],
    'forgot_password' => [
        'title' => 'Forgot password',
        'description' => 'Enter your email to receive a password reset link',
        'button' => 'Email password reset link',
        'return_to' => 'Or, return to',
        'login_link' => 'log in',
    ],
    'login' => [
        'title' => 'Log in to your account',
        'description' => 'Enter your email and password below to log in',
        'forgot_password' => 'Forgot password?',
        'remember_me' => 'Remember me',
        'button' => 'Log in',
        'no_account' => 'Don\'t have an account?',
        'sign_up' => 'Sign up',
    ],
    'register' => [
        'title' => 'Create an account',
        'description' => 'Enter your details below to create your account',
        'confirm_password' => 'Confirm password',
        'button' => 'Create account',
        'have_account' => 'Already have an account?',
        'login_link' => 'Log in',
    ],
    'reset_password' => [
        'title' => 'Reset password',
        'description' => 'Please enter your new password below',
        'button' => 'Reset password',
    ],
    'verify_email' => [
        'title' => 'Verify email',
        'description' => 'Please verify your email address by clicking on the link we just emailed to you.',
        'link_sent' => 'A new verification link has been sent to the email address you provided during registration.',
        'button' => 'Resend verification email',
    ],
    'two_factor' => [
        'page_title' => 'Two-factor authentication',
        'recovery_code' => [
            'title' => 'Recovery code',
            'description' => 'Please confirm access to your account by entering one of your emergency recovery codes.',
            'toggle' => 'login using an authentication code',
            'placeholder' => 'Enter recovery code',
        ],
        'auth_code' => [
            'title' => 'Authentication code',
            'description' => 'Enter the authentication code provided by your authenticator application.',
            'toggle' => 'login using a recovery code',
        ],
    ],
];
