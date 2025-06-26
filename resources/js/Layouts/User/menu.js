export const menuType = {
  item: 'item',
  heading: 'heading',
  dropdown: 'dropdown'
}

export var navMenuItems = [
  {
    icon: 'streamline:dashboard-circle',
    text: 'Overview',
    uri: route('user.dashboard'),
    component: 'Dashboard'
  },
  // {
  //   icon: 'ion:wallet-outline',
  //   text: 'Wallets',
  //   uri: route('user.wallet'),
  //   component: 'Wallet'
  // },
  {
    icon: 'ion:wallet-outline',
    text: 'Account',
    uri: route('user.account'),
    component: 'Wallet'
  },
  {
    type: menuType.heading,
    text: 'Cards & Finance'
  },
  {
    
    icon: 'f7:creditcard',
    text: 'My Cards',
    componentGroup: 'CardOrder',
    uri: route('user.credit-cards.index')
  },
  {
    
    icon: 'material-symbols:shopping-cart-outline',
    text: 'Card Orders',
    componentGroup: 'CardOrder',
    uri: route('user.card-orders.index')
  },
  {
    type: menuType.dropdown,
    icon: 'f7:creditcard',
    text: 'Prepaid Cards',
    componentGroup: 'CreditCard',
    subs: [
      // {       
      //   icon: 'fluent:credit-card-toolbox-20-regular',
      //   text: 'Cards',
      //   componentGroup: 'CreditCard',
      //   uri: route('user.credit-cards.index')
      // },
      {       
        icon: 'fluent:credit-card-toolbox-20-regular',
        text: 'Topup',
        componentGroup: 'CreditCard',
        uri: route('user.credit-card.topups')
      },
      {       
        icon: 'fluent:credit-card-toolbox-20-regular',
        text: 'Transactions',
        component: 'Transaction/Index',
        uri: route('user.credit-card.transactions')
      }
    ]
  },
  // {
  //   icon: 'ri:luggage-deposit-line',
  //   text: 'Deposits',
  //   componentGroup: 'Deposit',
  //   uri: route('user.deposits.index')
  // },
  {
    icon: 'solar:hand-money-outline',
    text: 'Topup',
    componentGroup: 'TopUp',
    uri: route('user.top-up.index')
  },
  // {
  //   icon: 'uil:money-withdraw',
  //   text: 'P2P History',
  //   componentGroup: 'Withdraw',
  //   uri: route('user.withdraw.index')
  // },
  // {
  //   icon: 'bx:transfer',
  //   text: 'Transfers',
  //   componentGroup: 'Transfer',
  //   uri: route('user.transfer.logs')
  // },
  // {
  //   icon: 'material-symbols:currency-exchange',
  //   text: 'Exchanges',
  //   componentGroup: 'Exchange',
  //   uri: route('user.exchange.logs')
  // },
  {   
    icon: 'streamline:payment-10',
    text: 'Payouts',
    componentGroup: 'Payout',
    uri: route('user.payout.index')
  },
  {
    type: menuType.heading,
    text: 'Support & Settings'
  },
  {
    icon: 'iconoir:headset-help',
    text: 'Supports',
    uri: route('user.supports.index'),
    componentGroup: 'Support'
  },

  {
   
    icon: 'bx:file-find',
    text: 'Account Verification',
    componentGroup: 'KYC',
    uri: route('user.kyc.index')
  },
  {
    icon: 'jam:task-list',
    text: 'Subscription',
    uri: route('user.subscription.index'),
    componentGroup: 'Subscription'
  },
  {
    icon: 'fe:gear',
    text: 'Account Settings',
    uri: route('user.account-settings'),
    component: 'AccountSettings'
  }
]
