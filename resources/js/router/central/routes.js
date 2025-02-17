function page(path) {
  return () =>
    import(/* webpackChunkName: '' */ `~/pages/${path}`).then(
      m => m.default || m
    )
}

export default [
  // Auth routes
  {
    path: '/',
    name: 'welcome',
    component: page('central/template/home.vue')
  },
  {
    path: '/admin/login',
    name: 'login',
    component: page('auth/login.vue')
  },
  {
    path: '/register',
    name: 'register',
    component: page('auth/register.vue')
  },
  {
    path: '/login',
    name: 'find-domain',
    component: page('auth/find-domain.vue')
  },
  {
    path: '/resend',
    name: 'resend',
    component: page('auth/resend.vue')
  },
  {
    path: '/password/reset',
    name: 'password.request',
    component: page('auth/password/email.vue')
  },
  {
    path: '/password/reset/:token',
    name: 'password.reset',
    component: page('auth/password/reset.vue')
  },
  {
    path: '/email/verify/:id',
    name: 'verification.verify',
    component: page('auth/verification/verify.vue')
  },
  {
    path: '/email/resend',
    name: 'verification.resend',
    component: page('auth/verification/resend.vue')
  },

  // Dashboard route
  {
    path: '/dashboard',
    name: 'home',
    component: page('central/dashboard.vue')
  },

  // Settings route
  {
    path: '/setup',
    name: 'setup.index',
    component: page('central/setup/index.vue'),
    meta: {
      permissions: [
        'general-settings',
        'user-permission',
        'user-role',
      ]
    }
  },

  // advanced settings route
  {
    path: '/setup/advanced',
    name: 'advanced-settings',
    component: page('central/setup/advanced.vue'),
    meta: { permissions: ['payment-settings'] }
  },

  // Update general settings route
  {
    path: '/setup/general',
    name: 'setup.general',
    component: page('central/setup/general.vue'),
    meta: { permissions: ['general-settings'] }
  },
  // Update mail configuration route
  {
    path: "/setup/mail-configuration",
    name: "setup.mailConfiguration",
    component: page("central/setup/mail-configuration.vue"),
    meta: { permissions: ["general-settings"] }
  },

  // Update sms configuration route
  {
    path: "/setup/sms-configuration",
    name: "setup.smsConfiguration",
    component: page("central/setup/sms-configuration.vue"),
    meta: { permissions: ["general-settings"] }
  },

  // currency index
  {
    path: "/setup/currency",
    name: "setup.centralCurrency.index",
    component: page("central/setup/currency/index.vue"),
    meta: { permissions: ["general-settings"] }
  },

  // currency create
  {
    path: "/setup/currency/create",
    name: "currency.create",
    component: page("central/setup/currency/create.vue"),
    meta: { permissions: ["general-settings"] }
  },

  {
    path: "/setup/currency/edit/:slug",
    name: "currency.edit",
    component: page("central/setup/currency/edit.vue"),
    meta: { permissions: ["general-settings"] }
  },

  // User setup
  {
    path: '/setup/user',
    name: 'user.index',
    component: page('central/setup/user/Index.vue'),
    meta: { permissions: ['general-settings'] }
  },
  {
    path: '/setup/user/create',
    name: 'user.create',
    component: page('central/setup/user/Create.vue'),
    meta: { permissions: ['general-settings'] }
  },
  {
    path: '/setup/user/edit/:slug',
    name: 'user.edit',
    component: page('central/setup/user/Edit.vue'),
    meta: { permissions: ['general-settings'] }
  },

  // Permissions routes
  {
    path: '/setup/permissions',
    name: 'permissions.index',
    component: page('central/setup/permissions/index.vue'),
    meta: { permissions: ['user-permission'] }
  },
  {
    path: '/setup/permissions/create',
    name: 'permissions.create',
    component: page('central/setup/permissions/create.vue'),
    meta: { permissions: ['user-permission'] }
  },
  {
    path: '/setup/permissions/edit/:slug',
    name: 'permissions.edit',
    component: page('central/setup/permissions/edit.vue'),
    meta: { permissions: ['user-permission'] }
  },

  // Plans routes
  {
    path: '/plans',
    name: 'plans.index',
    component: page('central/plans/index.vue'),
    meta: { permissions: ['plans-management'] }
  },
  {
    path: '/plans/create',
    name: 'plans.create',
    component: page('central/plans/create.vue'),
    meta: { permissions: ['plans-management'] }
  },
  {
    path: '/plans/edit/:id',
    name: 'plans.edit',
    component: page('central/plans/edit.vue'),
    meta: { permissions: ['plans-management'] }
  },

  // features routes
  {
    path: '/features',
    name: 'features.index',
    component: page('central/features/index.vue'),
    meta: { permissions: ['features-management'] }
  },
  {
    path: '/features/create',
    name: 'features.create',
    component: page('central/features/create.vue'),
    meta: { permissions: ['features-management'] }
  },
  {
    path: '/features/edit/:id',
    name: 'features.edit',
    component: page('central/features/edit.vue'),
    meta: { permissions: ['features-management'] }
  },

  // Tenant manage routes
  {
    path: '/tenants',
    name: 'tenants.index',
    component: page('central/tenants/index.vue'),
    meta: { permissions: ['tenants-management'] }
  },
  {
    path: '/tenants/create',
    name: 'tenants.create',
    component: page('central/tenants/create.vue'),
    meta: { permissions: ['tenants-management'] }
  },
  {
    path: '/tenants/:id',
    name: 'tenants.show',
    component: page('central/tenants/show.vue'),
    meta: { permissions: ['tenants-management'] }
  },
  {
    path: '/tenants/edit/:id',
    name: 'tenants.edit',
    component: page('central/tenants/edit.vue'),
    meta: { permissions: ['tenants-management'] }
  },

  // Role routes
  {
    path: '/setup/roles',
    name: 'roles.index',
    component: page('central/setup/roles/index.vue'),
    meta: { permissions: ['user-role'] }
  },
  {
    path: '/setup/roles/create',
    name: 'roles.create',
    component: page('central/setup/roles/create.vue'),
    meta: { permissions: ['user-role'] }
  },
  {
    path: '/setup/roles/edit/:slug',
    name: 'roles.edit',
    component: page('central/setup/roles/edit.vue'),
    meta: { permissions: ['user-role'] }
  },
  // Billing History routes
  {
    path: '/payments',
    name: 'payments.index',
    component: page('central/payments/index.vue'),
    meta: { permissions: ['payments'] }
  },

  // Update profile route
  {
    path: '/profile',
    name: 'profile',
    component: page('central/profile.vue'),
    meta: {
      permissions: ['update-profile']
    }
  },
  // Database backup route
  {
    path: '/backup',
    name: 'backup',
    component: page('central/backup.vue'),
    meta: {
      permissions: ['database-backup']
    }
  },
  // version route
  {
    path: '/version',
    name: 'version',
    component: page('central/version.vue'),
  },

  // newsletters crete
  {
    path: '/newsletters-create',
    name: 'newsletters-create',
    component: page('central/newsletters/create.vue'),
    meta: { permissions: ['promotion'] }
  },
  {
    path: '/newsletters',
    name: 'newsletters.index',
    component: page('central/newsletters/index.vue'),
    meta: { permissions: ['newsletters-management'] }
  },

  // Domain management routes
  {
    path: '/domains',
    name: 'domains.index',
    component: page('central/domains/index.vue'),
    meta: {
      permissions: ['domain-management'],
    },
  },
  {
    path: '/domain-requests',
    name: 'domain-requests.index',
    component: page('central/domain-requests/index.vue'),
    meta: {
      permissions: ['domain-management'],
    },
  },

  // subscription management routes
  {
    path: '/subscriptions',
    name: 'subscriptions.index',
    component: page('central/subscriptions/index.vue'),
  },
  {
    path: '/subscription-requests',
    name: 'subscription-requests.index',
    component: page('central/subscription-requests/index.vue'),
  },

  // Pages routes
  {
    path: '/pages',
    name: 'pages.index',
    component: page('central/pages/index.vue'),
    meta: { permissions: ['pages-management'] }
  },
  {
    path: '/pages/create',
    name: 'pages.create',
    component: page('central/pages/create.vue'),
    meta: { permissions: ['pages-management'] }
  },
  {
    path: '/pages/edit/:id',
    name: 'pages.edit',
    component: page('central/pages/edit.vue'),
    meta: { permissions: ['pages-management'] }
  },

  // Frontend Panel routes
  {
    path: '/settings',
    name: 'settings.index',
    component: page('central/settings/index.vue'),
    redirect: { name: 'settings.hero' },
    children: [
      { path: '', redirect: { name: 'settings.hero' } },
      {
        path: 'hero',
        name: 'settings.hero',
        component: page('central/settings/hero.vue'),
        meta: { permissions: ['landing-page-management'] },
      },
      {
        path: 'about-us',
        name: 'settings.about-us',
        component: page('central/settings/about-us.vue'),
        meta: { permissions: ['landing-page-management'] },
      },
      {
        path: 'why-us',
        name: 'settings.why-us',
        component: page('central/settings/why-us.vue'),
        meta: { permissions: ['landing-page-management'] },
      },
      {
        path: 'business-start',
        name: 'settings.business-start',
        component: page('central/settings/business-start.vue'),
        meta: { permissions: ['landing-page-management'] },
      },
      {
        path: 'features',
        name: 'settings.features',
        component: page('central/settings/features.vue'),
        meta: { permissions: ['landing-page-management'] },
      },
      {
        path: 'explorers',
        name: 'settings.explorers',
        component: page('central/settings/explorers.vue'),
        meta: { permissions: ['landing-page-management'] },
      },
      {
        path: 'all-features',
        name: 'settings.all-features',
        component: page('central/settings/all-features.vue'),
        meta: { permissions: ['landing-page-management'] },
      },
      {
        path: 'cta',
        name: 'settings.cta',
        component: page('central/settings/cta.vue'),
        meta: { permissions: ['landing-page-management'] },
      },
      {
        path: 'software-overview',
        name: 'settings.software-overview',
        component: page('central/settings/software-overview.vue'),
        meta: { permissions: ['landing-page-management'] },
      },
      {
        path: 'pricing-plan',
        name: 'settings.pricing-plan',
        component: page('central/settings/pricing-plan.vue'),
        meta: { permissions: ['landing-page-management'] },
      },
      {
        path: 'brands',
        name: 'settings.brands',
        component: page('central/settings/brand.vue'),
        meta: { permissions: ['landing-page-management'] },
      },
      {
        path: 'testimonial',
        name: 'settings.testimonial',
        component: page('central/settings/testimonial.vue'),
        meta: { permissions: ['landing-page-management'] },
      },
      {
        path: 'newsletter',
        name: 'settings.newsletter',
        component: page('central/settings/newsletter.vue'),
        meta: { permissions: ['landing-page-management'] },
      },
      {
        path: 'custom-html',
        name: 'settings.custom-html',
        component: page('central/settings/custom-html.vue'),
        meta: { permissions: ['landing-page-management'] },
      },
    ]
  },

  // dynamic pages view from frontend
  {
    path: '/pages/:slug',
    name: 'pages.show',
    component: page('central/template/pages/show.vue'),
  },

  // dynamic pages view from frontend
  {
    path: '/send-notification/:id',
    name: 'send-notification',
    component: page('central/send-notification/create.vue'),
    meta: { permissions: ['tenants-management'] }
  },

  // activity log
  {
    path: '/activity-log',
    name: 'activity.log',
    component: page('central/activity-log.vue'),
    meta: {
      permissions: ['update-profile']
    }
  },

  // application update routes
  {
    path: "/application-update",
    name: "applicationUpdate.index",
    component: page("central/application/index.vue"),
    meta: {
      permissions: ["database-backup"]
    }
  },

  // Permission denied
  {
    path: '/permission-denied',
    name: 'permission-denied',
    component: page('central/permission-denied.vue')
  },

  {
    path: '*',
    component: page('errors/404.vue')
  }
]
