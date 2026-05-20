<?php

return [
    'failed' => 'Tyto přihlašovací údaje neodpovídají našim záznamům.',
    'password' => 'Zadané heslo je nesprávné.',
    'throttle' => 'Příliš mnoho pokusů o přihlášení. Zkuste to znovu za :seconds sekund.',

    'confirm_password' => [
        'title' => 'Potvrďte své heslo',
        'description' => 'Toto je zabezpečená část aplikace. Před pokračováním potvrďte své heslo.',
        'button' => 'Potvrdit heslo',
    ],
    'forgot_password' => [
        'title' => 'Zapomenuté heslo',
        'description' => 'Zadejte e-mail pro obdržení odkazu na obnovení hesla',
        'button' => 'Odeslat odkaz pro obnovení hesla',
        'return_to' => 'Nebo se vraťte na',
        'login_link' => 'přihlášení',
    ],
    'login' => [
        'title' => 'Přihlaste se ke svému účtu',
        'description' => 'Zadejte e-mail a heslo pro přihlášení',
        'forgot_password' => 'Zapomenuté heslo?',
        'remember_me' => 'Zapamatovat si mě',
        'button' => 'Přihlásit se',
        'no_account' => 'Nemáte účet?',
        'sign_up' => 'Zaregistrovat se',
    ],
    'register' => [
        'title' => 'Vytvořit účet',
        'description' => 'Zadejte své údaje pro vytvoření účtu',
        'confirm_password' => 'Potvrdit heslo',
        'button' => 'Vytvořit účet',
        'have_account' => 'Již máte účet?',
        'login_link' => 'Přihlásit se',
    ],
    'reset_password' => [
        'title' => 'Obnovit heslo',
        'description' => 'Zadejte nové heslo',
        'button' => 'Obnovit heslo',
    ],
    'verify_email' => [
        'title' => 'Ověřit e-mail',
        'description' => 'Ověřte svou e-mailovou adresu kliknutím na odkaz, který jsme vám právě odeslali.',
        'link_sent' => 'Nový ověřovací odkaz byl odeslán na e-mailovou adresu zadanou při registraci.',
        'button' => 'Znovu odeslat ověřovací e-mail',
    ],
    'two_factor' => [
        'page_title' => 'Dvoufaktorové ověření',
        'recovery_code' => [
            'title' => 'Záložní kód',
            'description' => 'Potvrďte přístup ke svému účtu zadáním jednoho z nouzových záložních kódů.',
            'toggle' => 'přihlásit pomocí ověřovacího kódu',
            'placeholder' => 'Zadejte záložní kód',
        ],
        'auth_code' => [
            'title' => 'Ověřovací kód',
            'description' => 'Zadejte ověřovací kód ze své autentifikační aplikace.',
            'toggle' => 'přihlásit pomocí záložního kódu',
        ],
    ],
];
