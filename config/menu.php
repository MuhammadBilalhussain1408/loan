<?php
return [
    'admin' => [
        [
            'name' => 'Dashboard',
            'icon' => 'home',
            'route' => 'dashboard',
            'permissions' => '',
            'dropdown' => false,
            'children' => [],
            'order' => 0,
        ],
        [
            'name' => 'Members',
            'icon' => 'users',
            'route' => 'members.index',
            'route_check' => 'members.*',
            'permissions' => 'members',
            'dropdown' => false,
            'order' => 1,
        ],
        [
            'name' => 'Loans',
            'icon' => 'money-bill',
            'route' => '',
            'permissions' => 'loans',
            'dropdown' => true,
            'children' => [
                [
                    'name' => 'View Loan Applications',
                    'icon' => 'circle',
                    'route' => 'loans.applications.index',
                    'permissions' => 'loans.index',
                    'dropdown' => false,
                    'children' => [],
                    'order' => 1,
                ],
                [
                    'name' => 'View Loans',
                    'icon' => 'circle',
                    'route' => 'loans.index',
                    'permissions' => 'loans.index',
                    'dropdown' => false,
                    'children' => [],
                    'order' => 0,
                ],
                [
                    'name' => 'Loan Statement',
                    'icon' => 'circle',
                    'route' => 'statement.index',
                    'permissions' => 'loan.index',
                    'dropdown' => false,
                    'children' => [],
                    'order' => 0,
                ],
                [
                    'name' => 'Loan Repayments',
                    'icon' => 'circle',
                    'route' => 'loans.repayments.index',
                    'permissions' => 'loans.transactions.index',
                    'dropdown' => false,
                    'children' => [],
                    'order' => 2,
                ],
                [
                    'name' => 'Add Application',
                    'icon' => 'circle',
                    'route' => 'loans.applications.create',
                    'permissions' => 'loans.applications.create',
                    'dropdown' => false,
                    'children' => [],
                    'order' => 3,
                ],
                [
                    'name' => 'Loan Calculator',
                    'icon' => 'circle',
                    'route' => 'loans.calculator',
                    'permissions' => 'loans.index',
                    'dropdown' => false,
                    'children' => [],
                    'order' => 4,
                ],
                [
                    'name' => 'Manage Products',
                    'icon' => 'circle',
                    'route' => 'loans.products.index',
                    'permissions' => 'loans.products.index',
                    'dropdown' => false,
                    'children' => [],
                    'order' => 5,
                ],
                [
                    'name' => 'Manage Charges',
                    'icon' => 'circle',
                    'route' => 'loans.charges.index',
                    'permissions' => 'loans.charges.index',
                    'dropdown' => false,
                    'children' => [],
                    'order' => 5,
                ],
                [
                    'name' => 'Loan Checklists',
                    'icon' => 'circle',
                    'route' => 'loans.checklists.index',
                    'permissions' => 'loans.checklists.index',
                    'dropdown' => false,
                    'children' => [],
                    'order' => 6,
                ],
                [
                    'name' => 'Loan Approval Stages',
                    'icon' => 'circle',
                    'route' => 'loans.approval_stages.index',
                    'permissions' => 'loans.approval_stages.index',
                    'dropdown' => false,
                    'children' => [],
                    'order' => 6,
                ],
                [
                    'name' => 'Stop Order',
                    'icon' => 'circle',
                    'route' => 'loans.stop_loan.index',
                    'permissions' => 'loans.approval_stages.index',
                    'dropdown' => false,
                    'children' => [],
                    'order' => 6,
                ],
            ],
            'order' => 2,
        ],
        [
            'name' => 'Communication',
            'icon' => 'mail-bulk',
            'route' => '',
            'permissions' => 'communication',
            'dropdown' => true,
            'children' => [
                [
                    'name' => 'View Campaigns',
                    'icon' => 'circle',
                    'route' => 'communication.campaigns.index',
                    'permissions' => 'communication.campaigns.index',
                    'dropdown' => false,
                    'children' => [],
                    'order' => 0,
                ],
                [
                    'name' => 'Create Campaign',
                    'icon' => 'circle',
                    'route' => 'communication.campaigns.create',
                    'permissions' => 'communication.campaigns.create',
                    'dropdown' => false,
                    'children' => [],
                    'order' => 1,
                ],
                [
                    'name' => 'Manage Templates',
                    'icon' => 'circle',
                    'route' => 'communication.templates.index',
                    'permissions' => 'communication.templates.index',
                    'dropdown' => false,
                    'children' => [],
                    'order' => 2,
                ],
                [
                    'name' => ' SMS Gateways',
                    'icon' => 'circle',
                    'route' => 'communication.sms_gateways.index',
                    'permissions' => 'communication.sms_gateways.index',
                    'dropdown' => false,
                    'children' => [],
                    'order' => 3,
                ],
            ],
            'order' => 4,
        ],
        [
            'name' => 'Contributions',
            'icon' => 'mail-bulk',
            'route' => '',
            'permissions' => '',
            'dropdown' => true,
            'children' => [
                [
                    'name' => 'View Contributions',
                    'icon' => 'circle',
                    'route' => 'contribution.index',
                    'permissions' => '',
                    'dropdown' => false,
                    'children' => [],
                    'order' => 1,
                ],
            ],
            'order' => 4,
        ],
        [
            'name' => 'Users',
            'icon' => 'users',
            'route' => 'users.index',
            'permissions' => 'users',
            'dropdown' => false,
            'children' => [],
            'order' => 6,
        ],
        [
            'name' => 'Reports',
            'icon' => 'chart-bar',
            'route' => 'reports.index',
            'route_check' => 'reports.*',
            'permissions' => 'reports',
            'dropdown' => false,
            'children' => [],
            'order' => 13,
        ],
        [
            'name' => 'Activity Logs',
            'icon' => 'database',
            'route' => 'activity_logs.index',
            'permissions' => 'activity_logs',
            'dropdown' => false,
            'children' => [],
            'order' => 14,
        ],
        [
            'name' => 'Settings',
            'icon' => 'cogs',
            'route' => 'settings.index',
            'permissions' => 'settings',
            'dropdown' => false,
            'children' => [],
            'order' => 15,
        ],
    ],
    'member' => [
        [
            'name' => 'Dashboard',
            'icon' => 'home',
            'route' => 'portal.dashboard',
            'permissions' => '',
            'dropdown' => false,
            'children' => [],
            'order' => 0,
        ],

        [
            'name' => 'Loans',
            'icon' => 'database',
            'route' => 'portal.loans.index',
            'permissions' => '',
            'dropdown' => false,
            'children' => [],
            'order' => 1,
        ],
        [
            'name' => 'Loan Applications',
            'icon' => 'money-bill',
            'route' => 'portal.loans.applications.index',
            'permissions' => '',
            'dropdown' => false,
            'children' => [],
            'order' => 1,
        ],

        [
            'name' => 'My Profile',
            'icon' => 'user',
            'route' => 'portal.member.index',
            'permissions' => '',
            'dropdown' => false,
            'children' => [],
            'order' => 3,
        ],
    ]
];
