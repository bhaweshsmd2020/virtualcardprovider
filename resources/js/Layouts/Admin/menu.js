export const menuType = {
  item: 'item',
  heading: 'heading',
  dropdown: 'dropdown'
}

// RULES
// for menu components and componentGroup is prefixed by 'Admin'
// so we can just use like componentGroup : "Dashboard"
// not componentGroup : "Admin/Dashboard"
// if permission not given it will be publicly visible

export var navMenuItems = [
  {
    icon: 'material-symbols:dashboard-customize-outline',
    text: 'Dashboard',
    uri: route('admin.dashboard'),
    component: 'Dashboard'
  },
  {
    permission: 'users',
    icon: 'heroicons:users',
    text: 'Users',
    componentGroup: 'Users',
    uri: route('admin.users.index')
  },
  {
    type: menuType.dropdown,
    permission: 'locations',
    icon: 'bx:current-location',
    text: 'Locations',
    componentGroup: 'Locations',
    subs: [
      {
        text: 'Countries',
        componentGroup: 'Locations/Countries',
        uri: route('admin.countries.index')
      },
      {
        text: 'States',
        componentGroup: 'Locations/States',
        uri: route('admin.states.index')
      },
      {
        text: 'Cities',
        componentGroup: 'Locations/Cities',
        uri: route('admin.cities.index')
      }
    ]
  },

  {
    permission: 'order',
    icon: 'material-symbols:shopping-cart-outline',
    text: 'Orders',
    componentGroup: 'Order',
    uri: route('admin.order.index')
  },

  {
    permission: 'plan',
    icon: 'material-symbols:list-alt-outline',
    text: 'Subscriptions',
    componentGroup: 'Plan',
    uri: route('admin.plan.index')
  },
  {
    permission: 'gateways',
    icon: 'bx:bxs-calendar',
    text: 'Payment Gateways',
    componentGroup: 'Gateway',
    uri: route('admin.gateways.index')
  },

  {
    permission: 'payout-methods',
    icon: 'bx:briefcase',
    text: 'Payout Methods',
    componentGroup: 'PayoutMethod',
    uri: route('admin.payout-methods.index')
  },
  {
    permission: 'payouts',
    icon: 'bx:credit-card',
    text: 'Payouts',
    componentGroup: 'Payouts',
    uri: route('admin.payouts.index')
  },
  {
    permission: 'cron-job',
    icon: 'bx:code-block',
    text: 'Cron Jobs',
    componentGroup: 'Cron',
    uri: '/admin/cron-job'
  },
  {
    permission: 'support',
    icon: 'material-symbols:spatial-audio-outline',
    text: 'Help & Support',
    componentGroup: 'Support',
    uri: route('admin.support.index')
  },
  {
    permission: 'notification',
    icon: 'bx:bell',
    text: 'Notifications',
    componentGroup: 'Notification',
    uri: route('admin.notification.index')
  },
  {
    type: menuType.heading,
    text: 'Cards & Finances'
  },
  {
    permission: 'cards',
    icon: 'bi:credit-card-2-front',
    text: 'Card Types',
    uri: route('admin.cards.index'),
    componentGroup: 'Card'
  },
  {
    permission: 'cards',
    icon: 'bi:credit-card-2-front',
    text: 'Card Emails',
    uri: route('admin.card.emails.index'),
    componentGroup: 'Card'
  },
  {
    permission: 'card-category',
    icon: 'bx:grid',
    text: 'Card Category',
    uri: route('admin.card-category.index'),
    componentGroup: 'CardCategory'
  },
  {
    permission: 'card-orders',
    icon: 'material-symbols:shopping-cart-outline',
    text: 'Card Orders',
    componentGroup: 'CardOrder',
    uri: route('admin.card-orders.index')
  },
  {
    permission: 'card-orders',
    icon: 'material-symbols:shopping-cart-outline',
    text: 'Card Limit Requests',
    componentGroup: 'CardOrder',
    uri: route('admin.card.limit-requests.index')
  },
  {
    permission: 'deposits',
    icon: 'ri:luggage-deposit-line',
    text: 'Deposits',
    componentGroup: 'Deposit',
    uri: route('admin.deposits.index')
  },
  {
    permission: 'top-up',
    icon: 'solar:hand-money-outline',
    text: 'Topup',
    componentGroup: 'TopUp',
    uri: route('admin.top-up.index')
  },
  {
    permission: 'transfers',
    icon: 'bx:transfer',
    text: 'Transfers',
    component: 'Transfer/Index',
    uri: route('admin.transfers.index')
  },
  {
    permission: 'exchanges',
    icon: 'material-symbols:currency-exchange',
    text: 'Exchanges',
    component: 'ExchangeTransaction/Index',
    uri: route('admin.exchanges.index')
  },
  {
    permission: 'withdraw',
    icon: 'uil:money-withdraw',
    text: 'Withdraws',
    componentGroup: 'Withdraw',
    uri: route('admin.withdraw.index')
  },
  {
    permission: 'credit-card',
    type: menuType.dropdown,
    icon: 'bx:book',
    text: 'Prepaid Cards',
    componentGroup: 'CreditCard',
    subs: [
      {
        permission: 'order',
        icon: 'fluent:credit-card-toolbox-20-regular',
        text: 'Cards',
        componentGroup: 'CreditCard',
        uri: route('admin.credit-cards.index')
      },
      {
        permission: 'order',
        icon: 'fluent:credit-card-toolbox-20-regular',
        text: 'Authorizations',
        component: 'Authorization/Index',
        uri: route('admin.credit-card.authorizations')
      },
      {
        permission: 'order',
        icon: 'fluent:credit-card-toolbox-20-regular',
        text: 'Topup',
        componentGroup: 'CreditCard',
        uri: route('admin.credit-card.topups')
      },
      {
        permission: 'order',
        icon: 'fluent:credit-card-toolbox-20-regular',
        text: 'Transactions',
        component: 'Transaction/Index',
        uri: route('admin.credit-card.transactions')
      }
    ]
  },

  {
    permission: 'virtual-currency',
    icon: 'material-symbols:attach-money-rounded',
    text: 'Virtual Currency',
    componentGroup: 'VirtualCurrency',
    uri: route('admin.virtual-currency.index')
  },
  {
    type: menuType.heading,
    text: 'Appearance'
  },
  {
    type: menuType.dropdown,
    permission: 'services',
    icon: 'bx:columns',
    text: 'Services',
    componentGroup: 'Service',
    subs: [
      {
        permission: 'services',
        text: 'Services',
        uri: route('admin.services.index'),
        componentGroup: 'Services'
      },
      {
        permission: 'service-category',
        text: 'Service Categories',
        uri: route('admin.service-categories.index'),
        componentGroup: 'ServiceCategory'
      }
    ]
  },
  {
    type: menuType.dropdown,
    permission: 'projects',
    icon: 'bx:grid',
    text: 'Projects',
    componentGroup: 'Project',
    subs: [
      {
        permission: 'projects',
        text: 'Projects',
        uri: route('admin.projects.index'),
        componentGroup: 'Projects'
      },
      {
        permission: 'project-category',
        text: 'Project Categories',
        uri: route('admin.project-categories.index'),
        componentGroup: 'ProjectCategory'
      }
    ]
  },
  {
    permission: 'team',
    icon: 'bx:user',
    text: 'Team',
    uri: route('admin.team.index'),
    componentGroup: 'Team'
  },
  {
    permission: 'partner',
    icon: 'bxs:contact',
    text: 'Partners',
    uri: route('admin.partner.index'),
    componentGroup: 'Brand'
  },

  {
    type: menuType.dropdown,
    permission: 'blog',
    icon: 'bx:book',
    text: 'Blogs',
    componentGroup: 'Blog',
    subs: [
      {
        permission: 'blogs',
        text: 'Blog',
        componentGroup: 'Blog/Index',
        uri: route('admin.blog.index')
      },
      {
        permission: 'blog-category',
        text: 'Category',
        componentGroup: 'Blog/Category',
        uri: route('admin.category.index')
      },
      {
        permission: 'blog-tags',
        text: 'Tags',
        componentGroup: 'Blog/Tag',
        uri: route('admin.tag.index')
      }
    ]
  },
  {
    type: menuType.dropdown,
    permission: 'faq',
    icon: 'bx:help-circle',
    text: 'Faq',
    componentGroup: 'Faq',
    subs: [
      {
        permission: 'faq',
        text: "Faq's",
        component: 'Faq/Index',
        uri: route('admin.faq.index')
      },
      {
        permission: 'faq-category',
        text: 'Category',
        componentGroup: 'Faq/Category',
        uri: route('admin.faq-category.index')
      }
    ]
  },
  {
    permission: 'testimonials',
    icon: 'bx:message-square-detail',
    text: 'Testimonials',
    componentGroup: 'Testimonial',
    uri: route('admin.testimonials.index')
  },

  {
    permission: 'language',
    icon: 'iconoir:language',
    text: 'Language',
    componentGroup: 'Language',
    uri: route('admin.language.index')
  },
  {
    permission: 'menu',
    icon: 'bx:food-menu',
    text: 'Menu',
    componentGroup: 'Menu',
    uri: route('admin.menu.index')
  },
  {
    permission: 'page',
    icon: 'bx:bxs-file',
    text: 'Custom Pages',
    uri: route('admin.page.index'),
    exact: true,
  },
  {
    permission: 'seo',
    icon: 'bx:shape-polygon',
    text: 'Seo Settings',
    componentGroup: 'Seo',
    uri: route('admin.seo.index')
  },

  {
    type: menuType.heading,
    text: 'SITE SETTINGS'
  },

  {
    type: menuType.dropdown,
    permission: 'kyc',
    icon: 'bxs:user-detail',
    componentGroup: 'KYC',
    text: 'KYC',
    subs: [
      {
        permission: 'kyc-methods',
        text: 'Create Method',
        component: 'KYC/Methods/Create',
        uri: route('admin.kyc-methods.create')
      },
      {
        permission: 'kyc-methods',
        text: 'All Methods',
        component: 'KYC/Methods/Index',
        uri: route('admin.kyc-methods.index')
      },
      {
        permission: 'kyc-requests',
        text: 'KYC Requests',
        componentGroup: ['KYC/Requests'],
        uri: route('admin.kyc-requests.index')
      }
    ]
  },

  {
    permission: 'page-settings',
    icon: 'bx:cog',
    text: 'Page Settings',
    componentGroup: 'PageSetting',
    uri: route('admin.page-settings.index')
  },
  {
    type: menuType.dropdown,
    permission: 'admin-and-roles',
    icon: 'bx:lock-open',
    text: 'Admin and Role',
    componentGroup: 'Role',
    subs: [
      {
        permission: 'admin',
        text: 'Admin',
        uri: route('admin.admin.index'),
        componentGroup: 'Users/Admin'
      },
      {
        permission: 'roles',
        text: 'Roles',
        uri: route('admin.role.index'),
        componentGroup: 'Role'
      }
    ]
  },
  {
    permission: 'developer-settings',
    type: menuType.dropdown,
    icon: 'material-symbols:credit-card-gear-sharp',
    text: 'Developer Settings',
    componentGroup: 'Developer',
    subs: [
      {
        text: 'App Settings',
        uri: route('admin.developer-settings.show', 'app-settings')
      },
      {
        text: 'Card Settings',
        uri: route('admin.developer-settings.show', 'card-settings')
      },
      {
        text: 'Features Settings',
        uri: route('admin.developer-settings.show', 'features-settings')
      },
      {
        text: 'Currency Settings',
        uri: route('admin.developer-settings.show', 'currency-settings')
      },
      {
        text: 'Stripe Issuing Settings',
        uri: route('admin.developer-settings.show', 'stripe-settings')
      },
      {
        text: 'Twilio Settings',
        uri: route('admin.developer-settings.show', 'twilio-settings')
      },
      {
        text: 'SMTP Settings',
        uri: route('admin.developer-settings.show', 'mail-settings')
      },      
      {
        text: 'Email Templates',
        uri: route('admin.email-templates.index')
      },
      {
        text: 'Storage Settings',
        uri: route('admin.developer-settings.show', 'storage-settings')
      },
      {
        text: 'Newsletter Settings',
        uri: route('admin.developer-settings.show', 'newsletter-settings')
      },
      {
        text: 'Social Login Settings',
        uri: route('admin.developer-settings.show', 'social-login-settings')
      },
      {
        text: 'Cookie Settings',
        uri: route('admin.developer-settings.show', 'cookie-settings')
      },
      {
        text: 'App Update',
        uri: route('admin.update.index')
      }
    ]
  },

  {
    type: menuType.heading,
    text: 'MY SETTINGS'
  },
  {
    icon: 'material-symbols:account-circle-full',
    text: 'Profile Settings',
    componentGroup: 'Profile',
    uri: route('admin.profile.setting')
  }
]
