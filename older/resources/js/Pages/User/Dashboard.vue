<script setup>
import { router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Navigation, Autoplay } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import { useModalStore } from '@/Store/modalStore'
import NoDataFound from '@/Components/Admin/NoDataFound.vue'
import sharedComposable from '@/Composables/sharedComposable'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import UserLayout from '@/Layouts/User/UserLayout.vue'
import moment from 'moment'
import VueApexCharts from 'vue3-apexcharts'
import DashboardCreditCard from '@/Components/DashboardCreditCard.vue'
import GetCard from '@/Components/User/GetCard.vue'
import Modal from '@/Components/Admin/Modal.vue'
import Swal from 'sweetalert2'
import { usePage } from '@inertiajs/vue3'

defineOptions({ layout: UserLayout })

const props = defineProps([
  'accountBalance',
  'totalOrders',
  'totalCardOrders',
  'totalCreditCards',
  'totalWallets',
  'subscriptionsOrders',
  'recentCardOrders',
  'recentTopUps',
  'topUpSalesOverview',
  'transactions',
  'authorizations',
  'creditCards',
  'card_intro_details',
  'stripe_currency',
  'qrCode',
  'qrCodeLink',
  'request',
  'user'
])
const modal = useModalStore()
const { formatCurrency, authUser, pickBy, badgeClass, formatAmount, copyToClipboard } = sharedComposable()

// notification
const userNotifications = ref(usePage().props.userNotifications ?? [])
const unreadNotifications = computed(() => {
  return userNotifications.value?.filter((item) => item.seen == 0).length ?? 0
})

const primaryOverview = computed(() => {
  return [
    {
      title: 'Total Orders',
      value: props.totalOrders,
      url: route('user.subscription.index'),
      icon: 'bx bx-cart',
      classes: 'bg-primary-500 text-primary-500'
    },
    {
      title: 'Total Card Orders',
      value: props.totalCardOrders,
      url: route('user.card-orders.index'),
      icon: 'bx bx-shopping-bag',
      classes: 'text-success-500 bg-success-500'
    },
    {
      title: 'Total Prepaid Cards',
      value: props.totalCreditCards,
      url: route('user.credit-cards.index'),
      icon: 'bx bx-credit-card',
      classes: 'bg-primary-500 text-primary-500'
    },
    {
      title: 'Account Balance',
      value: formatCurrency(props.accountBalance),
      icon: 'bx bx-wallet',
      classes: 'text-success-500 bg-success-500'
    }
  ]
})
const filterForm = ref({
  top_up: props.request.top_up || ''
})

const filter = () => {
  router.get(route('user.dashboard'), pickBy(filterForm.value), {
    preserveState: true,
    replace: true
  })
}
const topUpSalesChart = computed(() => {
  return {
    series: [
      {
        name: 'Topup',
        data: props.topUpSalesOverview.map((item) => item.sales)
      }
    ],
    chartOptions: {
      colors: ['#69ae84', '#69ae84', '#69ae84'],
      chart: {
        height: 350,
        type: 'area',
        toolbar: {
          show: false
        }
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        curve: 'smooth'
      },
      xaxis: {
        type: 'string',
        categories: props.topUpSalesOverview.map((item) => item.date)
      }
    }
  }
})

const isNearExpiry = computed(() => {
  if (authUser.value.will_expire) {
    const userWillExpireDate = moment(authUser.value.will_expire)
    const daysDifference = userWillExpireDate.diff(moment(), 'days')
    return daysDifference < 7
  }
  return false
})
const hasExpired = computed(() => {
  if (authUser.value.will_expire) {
    const userWillExpireDate = moment(authUser.value.will_expire)
    return userWillExpireDate.isBefore(moment())
  }
  return false
})

function convertSvgToPng(svgContent) {
  const canvas = document.createElement('canvas')
  const ctx = canvas.getContext('2d')

  const DOMURL = window.URL || window.webkitURL || window
  const img = new Image()
  const svgBlob = new Blob([svgContent], { type: 'image/svg+xml' })
  const url = DOMURL.createObjectURL(svgBlob)

  img.onload = function () {
    canvas.width = img.width
    canvas.height = img.height
    ctx.drawImage(img, 0, 0)

    const imgData = canvas.toDataURL('image/png')

    //download link
    const link = document.createElement('a')
    link.href = imgData
    link.download = 'qr-code.png'
    link.textContent = 'Download PNG'
    document.body.appendChild(link)
    link.click()

    DOMURL.revokeObjectURL(url)
  }

  img.src = url
}

if (authUser.value.google2fa_secret == null) {
  function showAlert() {
    Swal.fire({
      icon: "warning",
      title: "<h2 style='font-size: 20px;'>Enable Two-Factor Authentication</h2>",
      text: "Enhance your account security by enabling 2FA",
      confirmButtonText: "Enable Now",
      allowOutsideClick: true,
      customClass: {
        popup: "custom-swal-popup",
      },
      didOpen: (toast) => {
        const closeButton = document.createElement("button")
        closeButton.innerHTML = "&times;"
        closeButton.classList.add("swal-close-btn")
        closeButton.onclick = () => Swal.close()

        toast.appendChild(closeButton)
      },
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = route('user.account-settings');
      }
    });
  }
  showAlert();
}

const getCardStyle = (type) => {
  const colorMap = {
    basic: '#1E1D22',
    pro: '#E5E4E2',
    silver: '#FFC58D',
    gold: '#0D1F2B'
  };
  return { backgroundColor: colorMap[type] || colorMap.default };
};

const activityBoxStyle = computed(() => {
  if (authUser.value.will_expire) {
    return {
      height: '685px',
      overflowY: 'scroll',
      maxHeight: '600px',
    };
  } else {
    return {
      height: '685px',
      overflowY: 'scroll',
      maxHeight: '685px',
    };
  }
});

const transBoxStyle = computed(() => {
  if (props.creditCards.length > 0) {
    return {
      height: '445px',
      overflowY: 'scroll',
    };
  } else {
    return {
      height: '425px',
      overflowY: 'scroll',
    };
  }
});
</script>

<template> 
  <Modal :header-state="true" header-title="Download the qr code or share the link" :state="modal.states['qrShare']">
    <div class="mb-2 mt-4 flex gap-2">
      <input type="text" class="input" readonly :value="qrCodeLink" />
      <button class="btn btn-soft-primary" @click="copyToClipboard(qrCodeLink)">
        {{ trans('Copy') }}
      </button>
    </div>
    <div class="flex flex-col items-center justify-center">
      <div v-html="qrCode" class="flex w-full scale-75 items-center justify-center"></div>
      <button type="button" @click="convertSvgToPng(qrCode)" class="btn btn-soft-primary">
        {{ trans('Download') }}
      </button>
    </div>
  </Modal>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader :title="trans('Overview')" />

    <div class="space-y-2">
       <div class="alert alert-danger text-base" v-if="!authUser.will_expire">
        <Link href="/user/subscription">
          <p>{{ trans("You haven't purchased any subscription plans yet.") }}</p>
        </Link>
      </div>
      <div class="alert alert-danger text-base" v-if="hasExpired">
        <p>{{ trans('Your subscription has already expired') }}</p>
      </div>
      <div class="alert alert-warning text-base" v-else-if="isNearExpiry">
        <p>
          {{ trans('Your current subscription plan is about to expire in less than 7 days') }}
        </p>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
        <!-- Left column -->
        <section class="md:col-span-3 flex flex-col space-y-2">   
          <div class="flex justify-between items-center text-sm font-bold text-[#1E293B]">
            <span>
              My Cards
            </span>
            <Link :href="route('user.credit-cards.index')" class="detail-list">
              All Cards
            </Link>
          </div>
          <div class="rounded-xl flex-col space-y-2 card">         
            <template  class="rounded-xl" v-if="creditCards.length > 0">
              <Swiper
                  :modules="[Navigation, Autoplay]"
                  :navigation="true"
                  :autoplay="{ delay:2000, disableOnInteraction: true }"
                  :loop="true"
                  :speed="400"                 
                  space-between="10"
                  slides-per-view="1"
              >
                <SwiperSlide v-for="creditCard in creditCards"
                  :key="creditCard.id"
                  :style="getCardStyle(creditCard.card.card_variant)"
                >
                  <Link :href="route('user.credit-cards.show', creditCard.id)">
                    <DashboardCreditCard classes="w-full" :credit-card="creditCard" />
                  </Link>
                </SwiperSlide>
              </Swiper>
            </template>
            <div class="flex justify-center" v-else>
              <GetCard :card_intro_details="card_intro_details" :show-image="false" />
            </div>     
          </div>

          <div class="wallet-buttons align-items-center mt-3">
            <a href="https://www.apple.com/in/wallet/" target="_blank">
              <img src="/assets/images/icon/googlewallet.png" alt="Google Wallet " class="img-fluid mb-3 rounded-xl" style="width: 100%;">
            </a>
            <a href="https://wallet.google/intl/en_in/" target="_blank">
              <img src="/assets/images/icon/applewallet.png" alt="Ppple Wallet" class="img-fluid rounded-xl" style="width: 100%;">
            </a>
          </div> 
          <p class="text-xs text-gray-400 mb-4">
            Supported by RAMP
          </p>

          <div class="flex  bg-white justify-between  border border-gray-200 rounded-xl p-3 mb-6 gap-4">
            <Link :href="route('user.credit-cards.index')">
              <button class="flex flex-col items-center text-gray-500 text-xs space-y-1" type="button">
                <div class="border ramp-icon rounded-full p-2 flex items-center justify-center w-10 h-10">
                  <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-280h80v-160h160v-80H520v-160h-80v160H280v80h160v160Zm40 200q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
                </div>
                <span class="flex items-center justify-between text-xs font-semibold text-[#1E293B]">
                  Topup
                </span>
              </button>
            </Link>
            <Link :href="route('user.credit-cards.index')">
              <button class="flex flex-col items-center text-gray-500 text-xs space-y-1" type="button">
                <div class="border ramp-icon rounded-full p-2 flex items-center justify-center w-10 h-10">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" width="1em" height="1em" viewBox="0 0 56 56" class="iconify iconify--f7"><path fill="currentColor" d="M9.625 47.71h36.75c4.898 0 7.36-2.413 7.36-7.241V15.555c0-4.828-2.462-7.266-7.36-7.266H9.625c-4.898 0-7.36 2.438-7.36 7.266v24.914c0 4.828 2.461 7.242 7.36 7.242M6.039 15.767c0-2.438 1.313-3.703 3.656-3.703h36.633c2.32 0 3.633 1.265 3.633 3.703v1.968H6.039Zm3.656 28.172c-2.344 0-3.656-1.243-3.656-3.68V23.055h43.922v17.203c0 2.437-1.313 3.68-3.633 3.68ZM12.39 37h5.743c1.383 0 2.297-.914 2.297-2.25v-4.336c0-1.312-.914-2.25-2.297-2.25H12.39c-1.383 0-2.297.938-2.297 2.25v4.336c0 1.336.914 2.25 2.297 2.25"></path></svg>
                </div>
                <span class="flex items-center justify-between text-xs font-semibold text-[#1E293B]">
                  Details
                </span>
              </button>
            </Link>
            <Link :href="route('user.credit-card.transactions')">
              <button class="flex flex-col items-center text-gray-500 text-xs space-y-1" type="button">
                <div class="border ramp-icon rounded-full p-2 flex items-center justify-center w-10 h-10">
                  <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="m136-240-56-56 296-298 160 160 208-206H640v-80h240v240h-80v-104L536-320 376-480 136-240Z"/></svg>
                </div>
                <span class="flex items-center justify-between text-xs font-semibold text-[#1E293B]">
                  Transactions
                </span>
              </button>
            </Link>
            <Link :href="route('user.credit-cards.index')">
              <button class="flex flex-col items-center text-gray-500 text-xs space-y-1" type="button">
                <div class="border ramp-icon rounded-full p-2 flex items-center justify-center w-10 h-10">
                  <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="m370-80-16-128q-13-5-24.5-12T307-235l-119 50L78-375l103-78q-1-7-1-13.5v-27q0-6.5 1-13.5L78-585l110-190 119 50q11-8 23-15t24-12l16-128h220l16 128q13 5 24.5 12t22.5 15l119-50 110 190-103 78q1 7 1 13.5v27q0 6.5-2 13.5l103 78-110 190-118-50q-11 8-23 15t-24 12L590-80H370Zm70-80h79l14-106q31-8 57.5-23.5T639-327l99 41 39-68-86-65q5-14 7-29.5t2-31.5q0-16-2-31.5t-7-29.5l86-65-39-68-99 42q-22-23-48.5-38.5T533-694l-13-106h-79l-14 106q-31 8-57.5 23.5T321-633l-99-41-39 68 86 64q-5 15-7 30t-2 32q0 16 2 31t7 30l-86 65 39 68 99-42q22 23 48.5 38.5T427-266l13 106Zm42-180q58 0 99-41t41-99q0-58-41-99t-99-41q-59 0-99.5 41T342-480q0 58 40.5 99t99.5 41Zm-2-140Z"/></svg>
                </div>
                <span class="flex items-center justify-between text-xs font-semibold text-[#1E293B]">
                  Manage
                </span>
              </button>
            </Link>
          </div>     
          <div class="card card-body p-3" :style="transBoxStyle">
            <div class="flex justify-between items-center text-sm font-bold text-[#1E293B]">
              <span>
                Card Transactions
              </span>
              <Link :href="route('user.credit-card.transactions')" class="detail-list">
                All Transactions
              </Link>
            </div>
            <ul class="space-y-4" v-if="transactions?.data?.length > 0">
              <li class="flex justify-between items-center" v-for="(transaction, index) in transactions.data" :key="transaction.id">
                <div class="flex items-center space-x-3">
                  <img alt="Hotel icon gray circle" class="w-10 h-10 rounded-full" height="40" src="https://storage.googleapis.com/a1aa/image/e76f3818-639d-4a29-ed7a-d955bbacaf15.jpg" width="40"/>
                  <div>
                    <p class="text-gray-900 font-semibold text-sm">
                      {{ transaction.merchant_name }}
                    </p>
                    <p class="text-gray-400 text-xs">
                    {{ moment(transaction.user_transaction_time).format('MMM DD, YYYY, h:mm A') }}
                    </p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-red-600 font-semibold text-sm">
                    {{ formatAmount(+transaction.amount) }} <span class="text-xs uppercase">{{ transaction.currency_code }}</span>
                  </p>
                  <p class="text-red-400 text-xs">
                    Spend
                  </p>
                </div>
              </li>
            </ul>
            <ul class="space-y-4 text-center p-5" v-else>
              <center><img src="/assets/images/no-data.svg" class="h-16 my-3" alt=""></center>
              <p class="text-gray-500">No data found</p>
            </ul>
          </div>
          <div class="order-4 col-span-1 space-y-2 md:col-span-5">        
          </div>
        </section>

        <!-- Middle column -->
        <section class="md:col-span-6 flex flex-col space-y-2">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
            <div class="bg-white rounded-xl p-4 shadow-sm flex flex-col space-y-1">
              <a :href="route('user.account')">
                <div class="flex items-center justify-between text-xs font-semibold text-[#1E293B]">
                  <span>
                  Account Balance
                  </span>
                  <i class="fas fa-info-circle text-gray-400"></i>
                </div>
                <p class="text-xl font-bold text-[#1E293B]">
                {{formatCurrency(props.accountBalance)}}
                </p>
              </a>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm flex flex-col space-y-1">
              <a :href="route('user.credit-cards.index')">
                <div class="flex items-center justify-between text-xs font-semibold text-[#1E293B]">
                  <span>
                  Total Virtual Cards
                  </span>
                  <i class="fas fa-info-circle text-gray-400"></i>
                </div>
                <p class="text-xl font-bold text-[#1E293B]">
                  {{props.totalCreditCards}}
                </p>
              </a>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm flex flex-col space-y-1">
              <a :href="route('user.credit-cards.index')">
                <div class="flex items-center justify-between text-xs font-semibold text-[#1E293B]">
                  <span>
                  Total Physical Cards
                  </span>
                  <i class="fas fa-info-circle text-gray-400"></i>
                </div>
                <p class="text-xl font-bold text-[#1E293B]">
                  0
                </p>
              </a>
            </div>
          </div>
          <div class="bg-white rounded-xl p-4 shadow-sm flex flex-col space-y-2">
          <div class="order-2 col-span-1 md:col-span-7 xl:order-3">
              <div class="card-body flex h-full flex-col justify-between gap-2">
                <div class="flex flex-wrap justify-between gap-2">
                  <h6>{{ trans('Overview Of Card Topup')}}</h6>
                  <select
                    v-model="filterForm.top_up"
                    @change="filter"
                    class="select select-xl w-full capitalize md:w-40"
                  >
                    <option value="" selected>{{ trans('Filter By') }}</option>
                    <option
                      :value="item"
                      v-for="item in ['day', 'week', 'month', 'year']"
                      :key="item"
                      :selected="filterForm.top_up === item"
                    >
                      {{ item }}
                    </option>
                  </select>
                </div>
                <div class="min-h-min">
                  <VueApexCharts
                    type="area"
                    height="350"
                    :options="topUpSalesChart.chartOptions"
                    :series="topUpSalesChart.series"
                  />
                </div>
              </div>
            </div>
          </div>
          <div class="card lg:col-span-12">
            <div class="card-body space-y-2">
              <div class="flex justify-between items-center text-sm font-bold text-[#1E293B]">
                <span>
                  Recent Topups
                </span>
                <Link :href="route('user.credit-card.topups')" class="detail-list">
                  All Topups
                </Link>
              </div>
              <div class="table-responsive whitespace-nowrap rounded-primary" style="height: 230px; overflow-y: scroll;">
                <table class="table">
                  <thead>
                    <tr>
                      <th>{{ trans('Invoice') }}</th>
                      <th>{{ trans('For') }}</th>
                      <th>{{ trans('Amount') }}</th>
                      <th>{{ trans('Status') }}</th>
                      <th>{{ trans('Created At') }}</th>
                    </tr>
                  </thead>
                  <tbody v-if="recentTopUps.length > 0">
                    <tr v-for="topUp in recentTopUps" :key="topUp.id">
                      <td>
                        <Link
                          :href="'/admin/top-up/' + topUp.id"
                          class="text-sm font-medium text-primary-500 transition duration-150 ease-in-out hover:underline"
                        >
                          {{ topUp.invoice_no }}
                        </Link>
                      </td>
                      <td>{{ topUp.type }}</td>
                      <td>{{ formatCurrency(topUp.amount) }}</td>
                      <td>
                        <div class="capitalize" :class="badgeClass(topUp.status)">
                          {{ topUp.status }}
                        </div>
                      </td>
                      <td>
                        {{ moment(topUp.created_at).format('DD MMM, YYYY') }}
                      </td>
                    </tr>
                  </tbody>
                  <NoDataFound v-else for-table="true" />
                </table>
              </div>
            </div>
          </div>
        </section>

        <!-- Right column -->
        <section class="md:col-span-3 flex flex-col space-y-2">
          <div class="bg-white rounded-xl p-4 shadow-sm flex flex-col space-y-1">
            <a :href="route('user.card-orders.index')">
              <div class="flex items-center justify-between text-xs font-semibold text-[#1E293B]">
                <span>
                Total Orders
                </span>
                <i class="fas fa-info-circle text-gray-400"></i>
              </div>
              <p class="text-xl font-bold text-[#1E293B]">
                {{props.totalCardOrders}}
              </p>
            </a>
          </div>
          <div class="bg-white rounded-xl p-4 shadow-sm flex flex-col space-y-2">

            <div class="alert alert-danger text-base" v-if="!authUser.will_expire">
              <Link href="/user/subscription">
                <p>{{ trans("You haven't purchased any subscription plans yet.") }}</p></Link
              >
            </div>
            <div class="alert alert-danger text-base" v-if="hasExpired">
              <p>{{ trans('Your subscription has already expired') }}</p>
            </div>
            <div class="alert alert-warning text-base" v-else-if="isNearExpiry">
              <p>
                {{ trans('Your current subscription plan is about to expire in less than 7 days') }}
              </p>
            </div>
                        
            <div class="flex justify-between items-center text-sm font-bold text-[#1E293B]" v-if="authUser.will_expire">
              <span>
                Your {{user.plan.title}} Plan
              </span>
              <Link :href="route('user.subscription.index')" class="btn btn-primary btn-xs">+ Upgrade Plan</Link>
            </div>
            <p class="text-2xl font-bold text-[#1E293B]" v-if="authUser.will_expire">
              <p class="text-sm font-semibold">
                {{ trans('Expiring On') }}
                {{ moment(authUser.will_expire).format('DD MMM, YYYY') }}
              </p>
            </p>
            <div class="space-y-3 text-xs text-gray-600" v-if="authUser.will_expire">
              <div>
                <div class="flex justify-between mb-1 font-semibold">
                  <span>
                    {{authUser.plan_data.cards.overview}}
                  </span>         
                </div>     
              </div>
              <div>
                <div class="flex justify-between mb-1 font-semibold">
                  <span>
                  {{authUser.plan_data.deposit_fee.overview}}
                  </span>        
                </div>
              </div>
              <div>
                <div class="flex justify-between mb-1 font-semibold">
                  <span>
                    {{authUser.plan_data.transaction_fee.overview}}
                  </span>
                </div>   
              </div>
              <div>
                <div class="flex justify-between mb-1 font-semibold">
                  <span>
                    {{authUser.plan_data.service_fee.overview}}
                  </span>
                </div>   
              </div>
            </div>
          </div>
          <div class="bg-white rounded-xl p-4 shadow-sm flex flex-col space-y-2 overflow-y-auto max-h-[600px]" :style="activityBoxStyle">
            <div class="flex justify-between items-center text-sm font-bold text-[#1E293B]">
              <span>
                Recent Activities
              </span>
              <Link href="#" class="detail-list">
                All Activities
              </Link>
            </div>
            <div class="flex flex-col space-y-3 text-xs text-gray-600">
              <div>        
                <template v-if="userNotifications.length">
                  <li
                    v-for="(item, index) in userNotifications"
                    :key="index"
                    class="flex cursor-pointer gap-2 border-b-2 border-white  py-2 transition-colors duration-150 hover:bg-slate-200/70 dark:border-secondary-600 dark:hover:bg-slate-700"
                    :class="{ 'bg-slate-100/70 dark:bg-slate-700': !item.seen }" style="padding-left: 10px;"
                  >                  
                    <a :href="item.url" @click="($event) => $event.preventDefault()">
                      <h6 class="text-sm font-normal">{{ item.title }}</h6>
                      <p class="text-xs text-slate-400" :title="item.comment">
                        {{ item.comment_short }}
                      </p>
                      <p class="mt-1 flex items-center gap-1 text-xs text-slate-400">
                        <Icon icon="fe:clock" width="1em" height="1em"></Icon>
                        <span>{{ item.created_at_human_date }}</span>
                      </p>
                    </a>
                  </li>
                </template>       
              </div>      
            </div>
          </div>
        </section>
      </div>
   
      <section class="grid grid-cols-1 place-items-start gap-2 lg:grid-cols-12">
        <div class="card lg:col-span-7">
          <div class="card-body">
            <div class="flex justify-between items-center text-sm font-bold text-[#1E293B]">
              <span>
                Subscription Plans
              </span>
              <Link :href="route('user.subscription.index')" class="detail-list">
                All Subscriptions
              </Link>
            </div>
            <div class="table-responsive whitespace-nowrap rounded-primary">
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ trans('Invoice No') }}</th>
                    <th>{{ trans('Payment ID') }}</th>
                    <th>{{ trans('Plan') }}</th>
                    <th>{{ trans('Amount') }}</th>
                    <th>{{ trans('Expire Date') }}</th>
                    <th class="text-center">{{ trans('Created Date') }}</th>
                  </tr>
                </thead>
                <tbody v-if="subscriptionsOrders.length > 0" class="border-0">
                  <tr v-for="order in subscriptionsOrders" :key="order.id">
                    <td>{{ order.invoice_no }}</td>
                    <td>{{ order.payment_id }}</td>
                    <td>{{ order.plan?.title }}</td>
                    <td>{{ formatCurrency(order.amount) }}</td>
                    <td>{{ moment(order.will_expire).format('DD MMM, YYYY') }}</td>
                    <td class="text-center">
                      {{ moment(order.created_at).format('DD MMM, YYYY') }}
                    </td>
                  </tr>
                </tbody>
                <NoDataFound :for-table="true" v-else />
              </table>
            </div>
          </div>
        </div>

        <div class="card lg:col-span-5">
          <div class="card-body space-y-2">
            <div class="flex justify-between items-center text-sm font-bold text-[#1E293B]">
              <span>
                Card Orders
              </span>
              <Link :href="route('user.card-orders.index')" class="detail-list">
                All Orders
              </Link>
            </div>
            <div class="table-responsive whitespace-nowrap rounded-primary" style="height: 235px;">
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ trans('Order No') }}</th>
                    <th>{{ trans('Card Name') }}</th>
                    <th>{{ trans('Amount') }}</th>
                    <th>{{ trans('Created At') }}</th>
                  </tr>
                </thead>
                <tbody v-if="recentCardOrders.length > 0">
                  <tr v-for="order in recentCardOrders" :key="order.id">
                    <td>
                      <Link
                        :href="'/user/card-orders/' + order.id"
                        class="text-sm font-medium text-primary-500 transition duration-150 ease-in-out hover:underline"
                      >
                        {{ order.invoice_no }}
                      </Link>
                    </td>
                    <td>{{ order?.card?.title }}</td>

                    <td>{{ formatCurrency(order.amount) }}</td>

                    <td class="text-center">
                      {{ moment(order.created_at).format('DD MMM, YYYY') }}
                    </td>
                  </tr>
                </tbody>
                <NoDataFound v-else for-table="true" />
              </table>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>
</template>

<style>
  .wallet-buttons{
    margin: 20px 0px 10px !important;
  }

  .detail-list{
    color: #0000EE;
    font-weight: 500;
    text-decoration: underline;
  }
</style>