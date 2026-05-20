<?php

return [
    'label' => 'Teams',
    'title' => 'Teams',
    'description' => 'Manage your teams and team memberships',
    'new_team' => 'New team',
    'personal' => 'Personal',
    'view' => 'View team',
    'edit' => 'Edit team',
    'empty' => 'You don\'t belong to any teams yet.',
    'edit_title' => 'Edit :name',
    'view_title' => 'View :name',
    'name_field' => 'Team name',
    'settings' => [
        'title' => 'Team settings',
        'description' => 'Update your team name and settings',
        'save' => 'Save',
    ],
    'members' => [
        'title' => 'Team members',
        'description' => 'Manage who belongs to this team',
        'invite' => 'Invite member',
        'remove' => 'Remove member',
    ],
    'invitations' => [
        'title' => 'Pending invitations',
        'description' => 'Invitations that haven\'t been accepted yet',
        'cancel' => 'Cancel invitation',
    ],
    'danger' => [
        'title' => 'Delete team',
        'description' => 'Permanently delete your team',
        'button' => 'Delete team',
    ],
    'create_modal' => [
        'title' => 'Create a new team',
        'description' => 'Create a new team to collaborate with others.',
        'name_placeholder' => 'My team',
        'submit' => 'Create team',
    ],
    'delete_modal' => [
        'title' => 'Are you sure?',
        'description' => 'This action cannot be undone. This will permanently delete the team ":name".',
        'confirm_label' => 'Type ":name" to confirm',
        'confirm_placeholder' => 'Enter team name',
        'submit' => 'Delete team',
    ],
    'invite_modal' => [
        'title' => 'Invite a team member',
        'description' => 'Send an invitation to join this team.',
        'email_placeholder' => 'colleague@example.com',
        'role' => 'Role',
        'role_placeholder' => 'Select a role',
        'submit' => 'Send invitation',
    ],
    'remove_member_modal' => [
        'title' => 'Remove team member',
        'description' => 'Are you sure you want to remove :name from this team?',
        'submit' => 'Remove member',
    ],
    'cancel_invitation_modal' => [
        'title' => 'Cancel invitation',
        'description' => 'Are you sure you want to cancel the invitation for :email?',
        'keep' => 'Keep invitation',
        'submit' => 'Cancel invitation',
    ],
];
