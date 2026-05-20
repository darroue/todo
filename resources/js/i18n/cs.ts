export default {
    common: {
        save: 'Uložit',
        cancel: 'Zrušit',
        warning: 'Upozornění',
        caution: 'Postupujte opatrně, tuto akci nelze vrátit.',
        email: 'E-mailová adresa',
        email_placeholder: "email{'@'}example.com",
        password: 'Heslo',
        confirm_password: 'Potvrdit heslo',
        name: 'Název',
        full_name: 'Celé jméno',
        settings: 'Nastavení',
        log_out: 'Odhlásit se',
        continue: 'Pokračovat',
        back: 'Zpět',
        confirm: 'Potvrdit',
        or_you_can: 'nebo můžete',
    },

    appearance: {
        light: 'Světlý',
        dark: 'Tmavý',
        system: 'Systém',
    },

    language: {
        en: 'English',
        cs: 'Čeština',
    },

    nav: {
        dashboard: 'Přehled',
        todos: 'Úkoly',
        teams: 'Týmy',
        repository: 'Repozitář',
        documentation: 'Dokumentace',
        navigation_menu: 'Navigační menu',
        select_team: 'Vybrat tým',
        new_team: 'Nový tým',
    },

    auth: {
        confirm_password: {
            title: 'Potvrďte své heslo',
            description:
                'Toto je zabezpečená část aplikace. Před pokračováním potvrďte své heslo.',
            button: 'Potvrdit heslo',
        },
        forgot_password: {
            title: 'Zapomenuté heslo',
            description: 'Zadejte e-mail pro obdržení odkazu na obnovení hesla',
            button: 'Odeslat odkaz pro obnovení hesla',
            return_to: 'Nebo se vraťte na',
            login_link: 'přihlášení',
        },
        login: {
            title: 'Přihlaste se ke svému účtu',
            description: 'Zadejte e-mail a heslo pro přihlášení',
            forgot_password: 'Zapomenuté heslo?',
            remember_me: 'Zapamatovat si mě',
            button: 'Přihlásit se',
            no_account: 'Nemáte účet?',
            sign_up: 'Zaregistrovat se',
        },
        register: {
            title: 'Vytvořit účet',
            description: 'Zadejte své údaje pro vytvoření účtu',
            confirm_password: 'Potvrdit heslo',
            button: 'Vytvořit účet',
            have_account: 'Již máte účet?',
            login_link: 'Přihlásit se',
        },
        reset_password: {
            title: 'Obnovit heslo',
            description: 'Zadejte nové heslo',
            button: 'Obnovit heslo',
        },
        verify_email: {
            title: 'Ověřit e-mail',
            description:
                'Ověřte svou e-mailovou adresu kliknutím na odkaz, který jsme vám právě odeslali.',
            link_sent:
                'Nový ověřovací odkaz byl odeslán na e-mailovou adresu zadanou při registraci.',
            button: 'Znovu odeslat ověřovací e-mail',
        },
        two_factor: {
            page_title: 'Dvoufaktorové ověření',
            recovery_code: {
                title: 'Záložní kód',
                description:
                    'Potvrďte přístup ke svému účtu zadáním jednoho z nouzových záložních kódů.',
                toggle: 'přihlásit pomocí ověřovacího kódu',
                placeholder: 'Zadejte záložní kód',
            },
            auth_code: {
                title: 'Ověřovací kód',
                description:
                    'Zadejte ověřovací kód ze své autentifikační aplikace.',
                toggle: 'přihlásit pomocí záložního kódu',
            },
        },
    },

    settings: {
        title: 'Nastavení',
        description: 'Spravujte svůj profil a nastavení účtu',
        profile: {
            label: 'Profil',
            title: 'Nastavení profilu',
            section_title: 'Informace o profilu',
            description: 'Aktualizujte své jméno a e-mailovou adresu',
            full_name_placeholder: 'Celé jméno',
            email_placeholder: 'E-mailová adresa',
            unverified: 'Vaše e-mailová adresa není ověřena.',
            resend: 'Klikněte zde pro opětovné odeslání ověřovacího e-mailu.',
            verification_sent:
                'Nový ověřovací odkaz byl odeslán na vaši e-mailovou adresu.',
        },
        security: {
            label: 'Zabezpečení',
            title: 'Nastavení zabezpečení',
            section_title: 'Aktualizovat heslo',
            description:
                'Zajistěte, aby váš účet používal dlouhé, náhodné heslo',
            current_password: 'Aktuální heslo',
            new_password: 'Nové heslo',
            save: 'Uložit heslo',
        },
        appearance: {
            label: 'Vzhled',
            title: 'Nastavení vzhledu',
            description: 'Aktualizujte nastavení vzhledu účtu',
        },
        language: {
            label: 'Jazyk',
            title: 'Nastavení jazyka',
            description: 'Vyberte preferovaný jazyk aplikace',
        },
        delete_account: {
            title: 'Smazat účet',
            description: 'Smažte svůj účet a veškerá jeho data',
            dialog_title: 'Opravdu chcete smazat svůj účet?',
            dialog_description:
                'Po smazání účtu budou trvale smazána veškerá jeho data a zdroje. Zadejte své heslo pro potvrzení trvalého smazání účtu.',
            button: 'Smazat účet',
            confirm: 'Smazat účet',
        },
        two_factor: {
            title: 'Dvoufaktorové ověření',
            description: 'Spravujte nastavení dvoufaktorového ověření',
            enable_info:
                'Po zapnutí dvoufaktorového ověření budete při přihlášení vyzváni k zadání bezpečnostního kódu. Tento kód získáte z aplikace podporující TOTP ve svém telefonu.',
            enabled_info:
                'Při přihlášení budete vyzváni k zadání bezpečnostního náhodného kódu, který získáte z aplikace podporující TOTP ve svém telefonu.',
            continue_setup: 'Pokračovat v nastavení',
            enable: 'Zapnout 2FA',
            disable: 'Vypnout 2FA',
            setup_modal: {
                enabled_title: 'Dvoufaktorové ověření zapnuto',
                enabled_description:
                    'Dvoufaktorové ověření je nyní aktivní. Naskenujte QR kód nebo zadejte nastavovací klíč do své autentifikační aplikace.',
                verify_title: 'Ověřit ověřovací kód',
                verify_description:
                    'Zadejte 6místný kód ze své autentifikační aplikace',
                enable_title: 'Zapnout dvoufaktorové ověření',
                enable_description:
                    'Pro dokončení nastavení dvoufaktorového ověření naskenujte QR kód nebo zadejte nastavovací klíč do své autentifikační aplikace',
                or_manually: 'nebo zadejte kód ručně',
            },
            recovery_codes: {
                title: 'Záložní kódy 2FA',
                description:
                    'Záložní kódy umožňují obnovit přístup při ztrátě 2FA zařízení. Uložte je v bezpečném správci hesel.',
                view: 'Zobrazit záložní kódy',
                hide: 'Skrýt záložní kódy',
                regenerate: 'Vygenerovat nové kódy',
                usage_note:
                    'Každý záložní kód lze použít jednou pro přístup k účtu a po použití bude odstraněn. Potřebujete-li více, klikněte na {link} výše.',
            },
        },
    },

    teams: {
        label: 'Týmy',
        title: 'Týmy',
        description: 'Spravujte své týmy a členství v týmech',
        new_team: 'Nový tým',
        personal: 'Osobní',
        view: 'Zobrazit tým',
        edit: 'Upravit tým',
        empty: 'Zatím nejste členem žádného týmu.',
        edit_title: 'Upravit {name}',
        view_title: 'Zobrazit {name}',
        name_field: 'Název týmu',

        settings: {
            title: 'Nastavení týmu',
            description: 'Aktualizujte název a nastavení týmu',
            save: 'Uložit',
        },

        members: {
            title: 'Členové týmu',
            description: 'Spravujte, kdo patří do tohoto týmu',
            invite: 'Pozvat člena',
            remove: 'Odebrat člena',
        },

        invitations: {
            title: 'Čekající pozvánky',
            description: 'Pozvánky, které ještě nebyly přijaty',
            cancel: 'Zrušit pozvánku',
        },

        danger: {
            title: 'Smazat tým',
            description: 'Trvale smažte svůj tým',
            button: 'Smazat tým',
        },

        create_modal: {
            title: 'Vytvořit nový tým',
            description: 'Vytvořte nový tým pro spolupráci s ostatními.',
            name_placeholder: 'Můj tým',
            submit: 'Vytvořit tým',
        },

        delete_modal: {
            title: 'Opravdu?',
            description:
                'Tuto akci nelze vrátit. Tým „{name}" bude trvale smazán.',
            confirm_label: 'Napište „{name}" pro potvrzení',
            confirm_placeholder: 'Zadejte název týmu',
            submit: 'Smazat tým',
        },

        invite_modal: {
            title: 'Pozvat člena týmu',
            description: 'Odešlete pozvánku ke vstupu do tohoto týmu.',
            email_placeholder: "kolega{'@'}example.com",
            role: 'Role',
            role_placeholder: 'Vybrat roli',
            submit: 'Odeslat pozvánku',
        },

        remove_member_modal: {
            title: 'Odebrat člena týmu',
            description: 'Opravdu chcete odebrat {name} z tohoto týmu?',
            submit: 'Odebrat člena',
        },

        cancel_invitation_modal: {
            title: 'Zrušit pozvánku',
            description: 'Opravdu chcete zrušit pozvánku pro {email}?',
            keep: 'Ponechat pozvánku',
            submit: 'Zrušit pozvánku',
        },
    },

    todos: {
        title: 'Úkoly',
        description: 'Spravujte úkoly svého týmu',
        title_placeholder: 'Název nového úkolu…',
        add: 'Přidat úkol',
        progress: '{completed}/{total} úkolů dokončeno',
        empty: 'Zatím žádné úkoly. Vytvořte jeden výše.',

        show: {
            description: 'Spravujte úlohy pro tento úkol',
            add_heading: 'Přidat novou úlohu',
            task_title: 'Název',
            task_title_placeholder: 'Název úlohy…',
            task_description: 'Popis',
            task_description_placeholder: 'Volitelný popis…',
            add_description_hint: 'Přidat popis…',
            add_task: 'Přidat úlohu',
            empty: 'Zatím žádné úlohy. Přidejte jednu výše.',
            upload: 'Nahrát přílohu',
            uploading: 'Nahrávám…',
            queued: 'Ve frontě',
        },
    },

    dashboard: {
        title: 'Přehled',
        todos: 'Úkoly',
        tasks: 'Úlohy',
        completed: '{count} dokončeno',
        completion_rate: 'Míra dokončení',
    },

    welcome: {
        title: 'Vítejte',
        app_name: 'Todo App',
        tagline: 'Spravujte úlohy, sdílejte týmové projekty a sledujte pokrok.',
        go_to_dashboard: 'Přejít na přehled',
        sign_in: 'Přihlásit se',
        sign_up: 'Zaregistrovat se',
    },

    errors: {
        something_went_wrong: 'Něco se pokazilo.',
    },
}
