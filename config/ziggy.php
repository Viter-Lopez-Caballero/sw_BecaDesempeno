<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Ziggy route groups
    |--------------------------------------------------------------------------
    |
    | Limit the routes exposed to the frontend by context/role. This reduces
    | unnecessary route metadata in public pages while keeping route() working
    | on each dashboard.
    |
    */
    'groups' => [
        'public' => [
            'inicio',
            'recognition.verify',
            'recognitions.search',
            'recognitions.public-download',
            'announcement.show',
            'documents.index',
            'contact',

            'login',
            'login.store',
            'register',
            'register.store',
            'register.evaluator',

            'password.request',
            'password.email',
            'password.reset',
            'password.update',

            'verification.notice',
            'verification.resend',
        ],

        'authenticated' => [
            'dashboard.index',
            'logout',

            'profile.*',
            'user-password.*',
            'appearance.*',
            'two-factor.*',
            'verification.*',
        ],

        'superadmin' => [
            'inicio',
            'dashboard.index',
            'logout',

            'profile.*',
            'user-password.*',
            'appearance.*',
            'two-factor.*',
            'verification.*',

            'superadmin.*',
            'security.*',
            'catalog.*',
            'announcements.*',
            'applications.control.*',
        ],

        'admin' => [
            'inicio',
            'dashboard.index',
            'logout',

            'profile.*',
            'user-password.*',
            'appearance.*',
            'two-factor.*',
            'verification.*',

            'admin.*',
            'security.*',
            'catalog.*',
            'announcements.*',
            'applications.control.*',
        ],

        'evaluator' => [
            'inicio',
            'dashboard.index',
            'logout',

            'profile.*',
            'user-password.*',
            'appearance.*',
            'two-factor.*',
            'verification.*',

            'evaluator.*',
        ],

        'teacher' => [
            'inicio',
            'dashboard.index',
            'logout',

            'profile.*',
            'user-password.*',
            'appearance.*',
            'two-factor.*',
            'verification.*',

            'teacher.*',
        ],
    ],
];
