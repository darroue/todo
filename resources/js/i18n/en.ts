export default {
    common: {
        save: 'Save',
        cancel: 'Cancel',
        warning: 'Warning',
        caution: 'Please proceed with caution, this cannot be undone.',
        email: 'Email address',
        email_placeholder: 'email@example.com',
        password: 'Password',
        confirm_password: 'Confirm password',
        name: 'Name',
        full_name: 'Full name',
        settings: 'Settings',
        log_out: 'Log out',
        continue: 'Continue',
        back: 'Back',
        confirm: 'Confirm',
        or_you_can: 'or you can',
    },

    appearance: {
        light: 'Light',
        dark: 'Dark',
        system: 'System',
    },

    nav: {
        dashboard: 'Dashboard',
        todos: 'Todos',
        teams: 'Teams',
        repository: 'Repository',
        documentation: 'Documentation',
        navigation_menu: 'Navigation menu',
        select_team: 'Select team',
        new_team: 'New team',
    },

    auth: {
        confirm_password: {
            title: 'Confirm your password',
            description:
                'This is a secure area of the application. Please confirm your password before continuing.',
            button: 'Confirm password',
        },
        forgot_password: {
            title: 'Forgot password',
            description: 'Enter your email to receive a password reset link',
            button: 'Email password reset link',
            return_to: 'Or, return to',
            login_link: 'log in',
        },
        login: {
            title: 'Log in to your account',
            description: 'Enter your email and password below to log in',
            forgot_password: 'Forgot password?',
            remember_me: 'Remember me',
            button: 'Log in',
            no_account: "Don't have an account?",
            sign_up: 'Sign up',
        },
        register: {
            title: 'Create an account',
            description: 'Enter your details below to create your account',
            confirm_password: 'Confirm password',
            button: 'Create account',
            have_account: 'Already have an account?',
            login_link: 'Log in',
        },
        reset_password: {
            title: 'Reset password',
            description: 'Please enter your new password below',
            button: 'Reset password',
        },
        verify_email: {
            title: 'Verify email',
            description:
                'Please verify your email address by clicking on the link we just emailed to you.',
            link_sent:
                'A new verification link has been sent to the email address you provided during registration.',
            button: 'Resend verification email',
        },
        two_factor: {
            page_title: 'Two-factor authentication',
            recovery_code: {
                title: 'Recovery code',
                description:
                    'Please confirm access to your account by entering one of your emergency recovery codes.',
                toggle: 'login using an authentication code',
                placeholder: 'Enter recovery code',
            },
            auth_code: {
                title: 'Authentication code',
                description:
                    'Enter the authentication code provided by your authenticator application.',
                toggle: 'login using a recovery code',
            },
        },
    },

    settings: {
        title: 'Settings',
        description: 'Manage your profile and account settings',
        profile: {
            label: 'Profile',
            title: 'Profile settings',
            section_title: 'Profile information',
            description: 'Update your name and email address',
            full_name_placeholder: 'Full name',
            email_placeholder: 'Email address',
            unverified: 'Your email address is unverified.',
            resend: 'Click here to resend the verification email.',
            verification_sent: 'A new verification link has been sent to your email address.',
        },
        security: {
            label: 'Security',
            title: 'Security settings',
            section_title: 'Update password',
            description:
                'Ensure your account is using a long, random password to stay secure',
            current_password: 'Current password',
            new_password: 'New password',
            save: 'Save password',
        },
        appearance: {
            label: 'Appearance',
            title: 'Appearance settings',
            description: "Update your account's appearance settings",
        },
        delete_account: {
            title: 'Delete account',
            description: 'Delete your account and all of its resources',
            dialog_title: 'Are you sure you want to delete your account?',
            dialog_description:
                'Once your account is deleted, all of its resources and data will also be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.',
            button: 'Delete account',
            confirm: 'Delete account',
        },
        two_factor: {
            title: 'Two-factor authentication',
            description: 'Manage your two-factor authentication settings',
            enable_info:
                'When you enable two-factor authentication, you will be prompted for a secure pin during login. This pin can be retrieved from a TOTP-supported application on your phone.',
            enabled_info:
                'You will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.',
            continue_setup: 'Continue setup',
            enable: 'Enable 2FA',
            disable: 'Disable 2FA',
            setup_modal: {
                enabled_title: 'Two-factor authentication enabled',
                enabled_description:
                    'Two-factor authentication is now enabled. Scan the QR code or enter the setup key in your authenticator app.',
                verify_title: 'Verify authentication code',
                verify_description: 'Enter the 6-digit code from your authenticator app',
                enable_title: 'Enable two-factor authentication',
                enable_description:
                    'To finish enabling two-factor authentication, scan the QR code or enter the setup key in your authenticator app',
                or_manually: 'or, enter the code manually',
            },
            recovery_codes: {
                title: '2FA recovery codes',
                description:
                    'Recovery codes let you regain access if you lose your 2FA device. Store them in a secure password manager.',
                view: 'View recovery codes',
                hide: 'Hide recovery codes',
                regenerate: 'Regenerate codes',
                usage_note:
                    'Each recovery code can be used once to access your account and will be removed after use. If you need more, click {link} above.',
            },
        },
    },

    teams: {
        label: 'Teams',
        title: 'Teams',
        description: 'Manage your teams and team memberships',
        new_team: 'New team',
        personal: 'Personal',
        view: 'View team',
        edit: 'Edit team',
        empty: "You don't belong to any teams yet.",
        edit_title: 'Edit {name}',
        view_title: 'View {name}',
        name_field: 'Team name',

        settings: {
            title: 'Team settings',
            description: 'Update your team name and settings',
            save: 'Save',
        },

        members: {
            title: 'Team members',
            description: 'Manage who belongs to this team',
            invite: 'Invite member',
            remove: 'Remove member',
        },

        invitations: {
            title: 'Pending invitations',
            description: "Invitations that haven't been accepted yet",
            cancel: 'Cancel invitation',
        },

        danger: {
            title: 'Delete team',
            description: 'Permanently delete your team',
            button: 'Delete team',
        },

        create_modal: {
            title: 'Create a new team',
            description: 'Create a new team to collaborate with others.',
            name_placeholder: 'My team',
            submit: 'Create team',
        },

        delete_modal: {
            title: 'Are you sure?',
            description:
                'This action cannot be undone. This will permanently delete the team "{name}".',
            confirm_label: 'Type "{name}" to confirm',
            confirm_placeholder: 'Enter team name',
            submit: 'Delete team',
        },

        invite_modal: {
            title: 'Invite a team member',
            description: 'Send an invitation to join this team.',
            email_placeholder: 'colleague@example.com',
            role: 'Role',
            role_placeholder: 'Select a role',
            submit: 'Send invitation',
        },

        remove_member_modal: {
            title: 'Remove team member',
            description: 'Are you sure you want to remove {name} from this team?',
            submit: 'Remove member',
        },

        cancel_invitation_modal: {
            title: 'Cancel invitation',
            description: 'Are you sure you want to cancel the invitation for {email}?',
            keep: 'Keep invitation',
            submit: 'Cancel invitation',
        },
    },

    todos: {
        title: 'Todos',
        description: 'Manage todos for your team',
        title_placeholder: 'New todo title…',
        add: 'Add todo',
        progress: '{completed}/{total} tasks completed',
        empty: 'No todos yet. Create one above.',

        show: {
            description: 'Manage tasks for this todo',
            add_heading: 'Add a new task',
            task_title: 'Title',
            task_title_placeholder: 'Task title…',
            task_description: 'Description',
            task_description_placeholder: 'Optional description…',
            add_description_hint: 'Add description…',
            add_task: 'Add task',
            empty: 'No tasks yet. Add one above.',
            upload: 'Upload attachment',
            uploading: 'Uploading…',
            queued: 'Queued',
        },
    },

    dashboard: {
        title: 'Dashboard',
        todos: 'Todos',
        tasks: 'Tasks',
        completed: '{count} completed',
        completion_rate: 'Completion rate',
    },

    welcome: {
        title: 'Welcome',
        app_name: 'Todo App',
        tagline: 'Manage tasks, share team projects and track your progress.',
        go_to_dashboard: 'Go to dashboard',
        sign_in: 'Sign in',
        sign_up: 'Sign up',
    },

    errors: {
        something_went_wrong: 'Something went wrong.',
    },
}
