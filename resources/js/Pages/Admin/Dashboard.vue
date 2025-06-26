<script setup>
import sharedComposable from '@/Composables/sharedComposable'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import NoDataFound from '@/Components/Admin/NoDataFound.vue'
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'
import VueApexCharts from 'vue3-apexcharts'
import { router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import moment from 'moment'
defineOptions({ layout: AdminLayout })

const props = defineProps([
  'totalOrders',
  'totalCardOrders',
  'totalCreditCards',
  'totalWallets',
  'totalSales',
  'mostOrderedPlan',
  'mostOrderedCard',
  'recentCardOrders',
  'popularPlans',
  'recentOrders',
  'salesOverview',
  'cardSalesOverview',
  'recentDeposits',
  'recentTopUps',
  'transactions',
  'authorizations',
  'walletBalances',
  'withdrawServiceFeeOverview',
  'transferServiceFeeOverview',
  'exchangeServiceFeeOverview',
  'topUpTransactionFeeOverview',
  'depositFeeOverview',
  'stripe_currency',
  'request'
])
const { formatCurrency, pickBy, formatAmount, badgeClass } = sharedComposable()

const primaryOverview = computed(() => {
  return [
    {
      title: 'Total Orders',
      value: props.totalOrders,
      url: route('admin.order.index'),
      icon: 'bx bx-cart',
      classes: 'bg-primary-500 text-primary-500'
    },
    {
      title: 'Total Card Orders',
      value: props.totalCardOrders,
      url: route('admin.order.index'),
      icon: 'bx bx-shopping-bag',
      classes: 'text-success-500 bg-success-500'
    },
    {
      title: 'Total Prepaid Cards',
      value: props.totalCreditCards,
      url: '#',
      icon: 'bx bx-credit-card',
      classes: 'bg-primary-500 text-primary-500'
    },
    {
      title: 'Total Wallets',
      value: props.totalWallets,
      icon: 'bx bx-wallet',
      classes: 'text-success-500 bg-success-500'
    }
  ]
})

const filterForm = ref({
  plan: props.request.plan || '',
  card: props.request.card || '',
  sales: props.request.sales || '',
  card_sales: props.request.card_sales || '',
  withdraw: props.request.withdraw || '',
  transfer: props.request.transfer || '',
  exchange: props.request.exchange || '',
  top_up: props.request.top_up || '',
  deposit: props.request.deposit || ''
})

const filter = () => {
  router.get(route('admin.dashboard'), pickBy(filterForm.value), {
    preserveState: true,
    preserveScroll: true,
    replace: true
  })
}

const generateChartData = (seriesName, data) => {
  return {
    series: [
      {
        name: seriesName,
        data: data.map((item) => item.sales)
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
        categories: data.map((item) => item.date)
      }
    }
  }
}
const salesChart = computed(() => {
  return generateChartData('Sales', props.salesOverview)
})
const cardSalesChart = computed(() => {
  return generateChartData('Card Sales', props.cardSalesOverview)
})

const withdrawServiceFeeChart = computed(() => {
  return generateChartData('Withdraw Service Fee', props.withdrawServiceFeeOverview)
})
const transferServiceFeeChart = computed(() => {
  return generateChartData('Transfer Service Fee', props.transferServiceFeeOverview)
})

const exchangeServiceFeeChart = computed(() => {
  return generateChartData('Exchange Service Fee', props.exchangeServiceFeeOverview)
})
const topUpTransactionFeeChart = computed(() => {
  return generateChartData('Topup Transaction Fee', props.topUpTransactionFeeOverview)
})
const depositFeeChart = computed(() => {
  return generateChartData('Deposit Fee', props.depositFeeOverview)
})
const mostSorts = [
  { label: 'Today', value: 'today' },
  { label: 'Month', value: 'month' },
  { label: 'All', value: '' }
]
const bgColors = [
  'bg-green-200/30',
  'bg-yellow-200/30',
  'bg-purple-200/30',
  'bg-blue-200/30',
  'bg-red-200/30',
  'bg-pink-200/30',
  'bg-orange-200/30',
  'bg-gray-200/30',
  'bg-teal-200/30'
]

const getBackgroundColor = (index) => {
  const colors = ['#ff000017', '#00ff0017', '#0000ff17', '#ffa50017']
  return colors[index % 4]
}
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader :title="trans('Dashboard')" />

    <div class="space-y-6">
      <!-- Overview Section Start -->
      <section class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
        <!-- Product Views  -->
        <template v-for="(item, index) in primaryOverview" :key="index">
          <Link :href="item.url">
            <div class="card" :style="{ background: getBackgroundColor(index) }">
              <div class="card-body flex items-center gap-4">
                <div
                  class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-opacity-20"
                  :class="item.classes"
                >
                  <i class="text-3xl" :class="item.icon"></i>
                </div>
                <div class="flex flex-1 flex-col gap-1">
                  <p class="text-sm tracking-wide text-slate-500">{{ item.title }}</p>
                  <div class="flex flex-wrap items-baseline justify-between gap-2">
                    <h4>{{ item.value }}</h4>
                  </div>
                </div>
              </div>
            </div>
          </Link>
        </template>
        <p class="col-span-full text-lg font-semibold" v-if="walletBalances.length > 0">{{ trans('Wallet Balances') }}</p>
        <template v-for="(item, i) in walletBalances" :key="i">
          <div
            class="rounded-md p-4 text-secondary-950 dark:text-secondary-300"
            :class="bgColors[[parseInt(i.toString()[0])]]"
          >
            <div class="flex h-20 items-center justify-between">
              <div class="flex flex-col gap-y-2 text-xl uppercase">
                <p class="font-semibold">{{ item.currency.currency }}</p>

                <p>
                  {{
                    +item.total_balance >= 1
                      ? (+item.total_balance).toFixed(item.currency.precision)
                      : 0
                  }}
                  <span class="text-sm">{{ item.currency.currency }}</span>
                </p>
              </div>

              <img
                class="w-20 object-contain opacity-20 dark:opacity-40 dark:invert dark:filter"
                v-lazy="item.currency.preview"
                alt="preview"
              />
            </div>
          </div>
        </template>
      </section>
      <!-- Overview Section End -->

      <!-- Sales Chart  -->
      <section class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
        <div class="card order-2 col-span-1 md:col-span-2 xl:order-3">
          <div class="card-body flex h-full flex-col justify-between gap-4">
            <div class="flex flex-wrap justify-between gap-2">
              <h6>{{ trans('Overview Of Sales') }}</h6>
              <select
                v-model="filterForm.sales"
                @change="filter"
                class="select select-xl w-full capitalize md:w-40"
              >
                <option value="" selected>{{ trans('Filter By') }}</option>
                <option
                  :value="item"
                  v-for="item in ['day', 'week', 'month', 'year']"
                  :key="item"
                  :selected="filterForm.sales === item"
                >
                  {{ item }}
                </option>
              </select>
            </div>
            <div class="min-h-min">
              <!-- Sales Location Chart  -->
              <VueApexCharts
                type="area"
                height="350"
                :options="salesChart.chartOptions"
                :series="salesChart.series"
              />
            </div>
          </div>
        </div>

        <!-- Most Ordered Plan  -->
        <div class="order-4 col-span-1 space-y-6">
          <div class="card">
            <div class="card-body">
              <div class="flex flex-wrap items-center justify-between">
                <h6>{{ trans('Most Ordered Plan') }}</h6>
                <div class="flex items-center gap-2">
                  <template v-for="(sort, i) in mostSorts" :key="sort.label">
                    <button
                      type="button"
                      @click="
                        () => {
                          filterForm.plan = sort.value
                          filter()
                        }
                      "
                    >
                      <span
                        class="text-xs capitalize"
                        :class="{
                          'font-medium text-primary-500': filterForm.plan == sort.value
                        }"
                      >
                        {{ trans(sort.label) }}
                      </span>
                    </button>
                    <span v-if="i < 2" class="text-sm text-slate-200 dark:text-slate-600">|</span>
                  </template>
                </div>
              </div>
              <div
                v-if="mostOrderedPlan"
                class="mt-4 flex items-center gap-4 rounded-primary bg-slate-50 p-4 dark:bg-slate-900"
              >
                <Link :href="route('admin.plan.index')">
                  <div class="flex flex-1 flex-col gap-1">
                    <h3 class="text-sm font-semibold">{{ mostOrderedPlan.title }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                      {{ trans('Price') }}: {{ formatCurrency(mostOrderedPlan.price) }},
                      {{ mostOrderedPlan.days == 30 ? 'Monthly' : 'Yearly' }},
                      {{ trans('Total Order') }}:
                      {{ mostOrderedPlan.orders_count }}
                    </p>
                  </div>
                </Link>
              </div>
              <p v-else v-text="'Nothing found...'" />
            </div>
          </div>
          <div class="card">
            <div class="card-body">
              <div class="flex flex-wrap items-center justify-between">
                <h6>{{ trans('Most Ordered Card') }}</h6>
                <div class="flex items-center gap-2">
                  <template v-for="(sort, i) in mostSorts" :key="sort.label">
                    <button
                      type="button"
                      @click="
                        () => {
                          filterForm.card = sort.value
                          filter()
                        }
                      "
                    >
                      <span
                        class="text-xs capitalize"
                        :class="{
                          'font-medium text-primary-500': filterForm.card == sort.value
                        }"
                      >
                        {{ trans(sort.label) }}
                      </span>
                    </button>
                    <span v-if="i < 2" class="text-sm text-slate-200 dark:text-slate-600">|</span>
                  </template>
                </div>
              </div>
              <template  v-if="mostOrderedCard.length > 0">
              <div
                v-for="cardOrder in mostOrderedCard" :key="cardOrder.id"
                class="mt-4 flex items-center gap-4 rounded-primary bg-slate-50 p-4 dark:bg-slate-900"
              >
                <Link :href="route('admin.cards.index')" >
                  <div class="flex flex-1 flex-col gap-1">
                    <h3 class="text-sm font-semibold">{{ cardOrder.title }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                      {{ trans('Card Variant') }}:
                      <span class="capitalize">{{ cardOrder.card_variant }}</span
                      >, {{ trans('Total Order') }}:
                      {{ cardOrder.card_orders_count }}
                    </p>
                  </div>
                </Link>
              </div>
              </template>
              <p v-else v-text="'Nothing found...'" />
            </div>
          </div>
        </div>
      </section>
      <!--card Sales Chart  -->
      <section class="grid grid-cols-1 gap-6 md:grid-cols-12">
        <div class="card order-2 col-span-1 md:col-span-7 xl:order-3">
          <div class="card-body flex h-full flex-col justify-between gap-4">
            <div class="flex flex-wrap justify-between gap-2">
              <h6>{{ trans('Overview Of Card Sales') }}</h6>
              <select
                v-model="filterForm.card_sales"
                @change="filter"
                class="select select-xl w-full capitalize md:w-40"
              >
                <option value="" selected>{{ trans('Filter By') }}</option>
                <option
                  :value="item"
                  v-for="item in ['day', 'week', 'month', 'year']"
                  :key="item"
                  :selected="filterForm.card_sales === item"
                >
                  {{ item }}
                </option>
              </select>
            </div>
            <div class="min-h-min">
              <!-- Sales Location Chart  -->
              <VueApexCharts
                type="area"
                height="350"
                :options="cardSalesChart.chartOptions"
                :series="cardSalesChart.series"
              />
            </div>
          </div>
        </div>

        <!-- Most Ordered Plan  -->
        <div class="order-4 col-span-1 space-y-6 md:col-span-5">
          <div class="card">
            <div class="card-body">
              <h6>{{ trans('Recent Card Orders') }}</h6>
              <div class="table-responsive whitespace-nowrap rounded-primary">
                <table class="table">
                  <thead>
                    <tr>
                      <th>{{ trans('Order No') }}</th>
                      <th>{{ trans('Card Name') }}</th>
                      <th>{{ trans('User Name') }}</th>

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
                      <td>{{ order?.user?.name }}</td>

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
        </div>
      </section>
      <!-- service fee charts  -->
      <section class="grid grid-cols-1 gap-6 md:grid-cols-12">
        <div class="card order-2 col-span-1 md:col-span-6 xl:order-3">
          <div class="card-body flex h-full flex-col justify-between gap-4">
            <div class="flex flex-wrap justify-between gap-2">
              <h6>{{ trans('Withdraw Service Fees') }}</h6>
              <select
                v-model="filterForm.withdraw"
                @change="filter"
                class="select select-xl w-full capitalize md:w-40"
              >
                <option value="" selected>{{ trans('Filter By') }}</option>
                <option
                  :value="item"
                  v-for="item in ['day', 'week', 'month', 'year']"
                  :key="item"
                  :selected="filterForm.withdraw === item"
                >
                  {{ item }}
                </option>
              </select>
            </div>
            <div class="min-h-min">
              <!-- Sales Location Chart  -->
              <VueApexCharts
                type="area"
                height="350"
                :options="withdrawServiceFeeChart.chartOptions"
                :series="withdrawServiceFeeChart.series"
              />
            </div>
          </div>
        </div>
        <div class="card order-2 col-span-1 md:col-span-6 xl:order-3">
          <div class="card-body flex h-full flex-col justify-between gap-4">
            <div class="flex flex-wrap justify-between gap-2">
              <h6>{{ trans('Transfer Service Fees') }}</h6>
              <select
                v-model="filterForm.transfer"
                @change="filter"
                class="select select-xl w-full capitalize md:w-40"
              >
                <option value="" selected>{{ trans('Filter By') }}</option>
                <option
                  :value="item"
                  v-for="item in ['day', 'week', 'month', 'year']"
                  :key="item"
                  :selected="filterForm.transfer === item"
                >
                  {{ item }}
                </option>
              </select>
            </div>
            <div class="min-h-min">
              <!-- Sales Location Chart  -->
              <VueApexCharts
                type="area"
                height="350"
                :options="transferServiceFeeChart.chartOptions"
                :series="transferServiceFeeChart.series"
              />
            </div>
          </div>
        </div>
      </section>
      <section class="grid grid-cols-1 gap-6 md:grid-cols-12">
        <div class="card order-2 col-span-1 md:col-span-6 xl:order-3">
          <div class="card-body flex h-full flex-col justify-between gap-4">
            <div class="flex flex-wrap justify-between gap-2">
              <h6>{{ trans('Exchange Service Fees') }}</h6>
              <select
                v-model="filterForm.exchange"
                @change="filter"
                class="select select-xl w-full capitalize md:w-40"
              >
                <option value="" selected>{{ trans('Filter By') }}</option>
                <option
                  :value="item"
                  v-for="item in ['day', 'week', 'month', 'year']"
                  :key="item"
                  :selected="filterForm.exchange === item"
                >
                  {{ item }}
                </option>
              </select>
            </div>
            <div class="min-h-min">
              <!-- Sales Location Chart  -->
              <VueApexCharts
                type="area"
                height="350"
                :options="exchangeServiceFeeChart.chartOptions"
                :series="exchangeServiceFeeChart.series"
              />
            </div>
          </div>
        </div>
        <div class="card order-2 col-span-1 md:col-span-6 xl:order-3">
          <div class="card-body flex h-full flex-col justify-between gap-4">
            <div class="flex flex-wrap justify-between gap-2">
              <h6>{{ trans('Topup Transaction Fees') }}</h6>
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
              <!-- Sales Location Chart  -->
              <VueApexCharts
                type="area"
                height="350"
                :options="topUpTransactionFeeChart.chartOptions"
                :series="topUpTransactionFeeChart.series"
              />
            </div>
          </div>
        </div>
      </section>
      <section class="grid grid-cols-1 gap-6 md:grid-cols-12">
        <div class="card order-2 col-span-1 md:col-span-12 xl:order-3">
          <div class="card-body flex h-full flex-col justify-between gap-4">
            <div class="flex flex-wrap justify-between gap-2">
              <h6>{{ trans('Deposit Fees') }}</h6>
              <select
                v-model="filterForm.deposit"
                @change="filter"
                class="select select-xl w-full capitalize md:w-40"
              >
                <option value="" selected>{{ trans('Filter By') }}</option>
                <option
                  :value="item"
                  v-for="item in ['day', 'week', 'month', 'year']"
                  :key="item"
                  :selected="filterForm.deposit === item"
                >
                  {{ item }}
                </option>
              </select>
            </div>
            <div class="min-h-min">
              <!-- Sales Location Chart  -->
              <VueApexCharts
                type="area"
                height="350"
                :options="depositFeeChart.chartOptions"
                :series="depositFeeChart.series"
              />
            </div>
          </div>
        </div>
      </section>

      <!-- Top Sellers Section start  -->
      <section class="grid md:grid-cols-12 place-items-start gap-6" style="overflow: scroll;">
        <div class="card md:col-span-8">
          <div class="card-body space-y-2">
            <h6>{{ trans('Popular Plans') }}</h6>
            <!-- Seller Table  -->
            <div
              v-if="popularPlans?.length > 0"
              class="table-responsive whitespace-nowrap rounded-primary"
            >
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ trans('Plan') }}</th>
                    <th>{{ trans('Active Users') }}</th>
                    <th>{{ trans('Sales') }}</th>
                    <th>{{ trans('Total Amount') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="plan in popularPlans" :key="plan.id">
                    <td class="whitespace-nowrap">
                      <Link :href="route('admin.plan.index')">
                        {{ plan.name }}
                      </Link>
                    </td>
                    <td class="whitespace-nowrap">{{ plan.activeuser }}</td>
                    <td class="whitespace-nowrap">
                      <p>{{ plan.orders_count }}</p>
                    </td>
                    <td>{{ plan.total_amount }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <p v-else v-text="'Nothing found...'" />
          </div>
        </div>
        <div class="card md:col-span-4">
          <div class="card-body flex h-full flex-col">
            <!-- Header  -->
            <h6>{{ trans('Recent Orders') }}</h6>

            <div v-if="recentOrders?.length > 0" class="mt-auto divide-y dark:divide-slate-600">
              <template v-for="order in recentOrders" :key="order.id">
                <Link :href="route('admin.order.index')">
                  <div class="flex items-center gap-4 py-2">
                    <div class="flex h-12 w-12 min-w-12 items-center justify-center">
                      <img
                        v-lazy="
                          order.avatar == null
                            ? `https://ui-avatars.com/api/?name=${order?.name}`
                            : `${order.avatar}`
                        "
                        class="rounded-primary"
                        alt="avatar"
                      />
                    </div>
                    <div class="flex w-full items-center justify-between">
                      <div>
                        <h6 class="text-sm font-medium text-slate-600 dark:text-slate-300">
                          {{ order.invoice }}
                        </h6>
                        <p class="text-sm text-slate-400">{{ order.plan }}</p>
                        <p class="text-xs dark:text-slate-200">By {{ order.name }}</p>
                      </div>

                      <div>
                        <h6 class="text-sm font-medium text-slate-600 dark:text-slate-300">
                          {{ order.amount }}
                        </h6>
                        <p class="text-sm text-slate-400">{{ order.created_at }}</p>
                      </div>
                    </div>
                  </div>
                </Link>
              </template>
            </div>
            <p v-else v-text="'Nothing found...'" />
          </div>
        </div>
      </section>
      <!-- Top Sellers Section End  -->

      <!-- Recent Posts -->
      <section class="grid grid-cols-1 place-items-start gap-6 lg:grid-cols-12">
        <div class="card lg:col-span-6">
          <div class="card-body space-y-2">
            <!-- Header  -->
            <h6>{{ trans('Recent Topup') }}</h6>
            <div class="table-responsive whitespace-nowrap rounded-primary">
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ trans('Invoice') }}</th>
                    <th>{{ trans('User Name') }}</th>

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
                    <td>{{ topUp?.user?.name }}</td>

                    <td>{{ topUp.type }}</td>
                    <td>{{ formatCurrency(topUp.amount) }}</td>
                    <td>
                      <div class="badge badge-success capitalize">
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
        <!-- jobs  -->
        <div class="card lg:col-span-6">
          <div class="card-body space-y-2">
            <h6>{{ trans('Recent Deposits') }}</h6>

            <div class="table-responsive whitespace-nowrap rounded-primary">
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ trans('Invoice') }}</th>
                    <th>{{ trans('User Name') }}</th>

                    <th>{{ trans('Amount') }}</th>
                    <th>{{ trans('Status') }}</th>
                    <th>{{ trans('Created At') }}</th>
                  </tr>
                </thead>
                <tbody v-if="recentDeposits.length > 0">
                  <tr v-for="deposit in recentDeposits" :key="deposit.id">
                    <td>
                      <Link
                        :href="'/admin/deposits/' + deposit.id"
                        class="text-sm font-medium text-primary-500 transition duration-150 ease-in-out hover:underline"
                      >
                        {{ deposit.invoice_no }}
                      </Link>
                    </td>
                    <td>{{ deposit?.user?.name }}</td>
                    <td>{{ formatCurrency(deposit.amount) }}</td>
                    <td>
                      <div class="badge badge-success capitalize">
                        {{ deposit.status }}
                      </div>
                    </td>
                    <td class="text-center">
                      {{ moment(deposit.created_at).format('DD MMM, YYYY') }}
                    </td>
                  </tr>
                </tbody>
                <NoDataFound v-else for-table="true" />
              </table>
            </div>
          </div>
        </div>
      </section>

      <!-- authorizations, transactions -->
      <section class="grid grid-cols-1 place-items-start gap-6 lg:grid-cols-12">
        <div class="card lg:col-span-5">
          <div class="card-body space-y-2">
            <h6>{{ trans('Recent Transactions') }}</h6>
            <div class="table-responsive whitespace-nowrap rounded-primary">
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ trans('Transaction id') }}</th>
                    <th>{{ trans('Amount') }}</th>
                    <th>{{ trans('Date') }}</th>
                  </tr>
                </thead>
                <tbody v-if="transactions?.data?.length > 0">
                  <tr v-for="transaction in transactions.data" :key="transaction.id">
                    <td>
                      {{ transaction.network_data.transaction_id }}
                    </td>

                    <td>
                      {{ formatAmount(+transaction.amount / 100) }}
                      <span class="text-xs uppercase">{{ stripe_currency }}</span>
                    </td>

                    <td class="text-center">
                      {{ transaction.network_data.processing_date }}
                    </td>
                  </tr>
                </tbody>
                <NoDataFound v-else for-table="true" />
              </table>
            </div>
          </div>
        </div>
        <!-- jobs  -->
        <div class="card lg:col-span-7">
          <div class="card-body space-y-2">
            <h6>{{ trans('Recent Authorization') }}</h6>
            <div class="table-responsive whitespace-nowrap rounded-primary">
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ trans('Transaction id') }}</th>
                    <th>{{ trans('Cardholder Name') }}</th>

                    <th>{{ trans('Last4') }}</th>
                    <th>{{ trans('Amount') }}</th>
                    <th>{{ trans('Status') }}</th>
                    <th>{{ trans('Date') }}</th>
                  </tr>
                </thead>
                <tbody v-if="authorizations?.data?.length > 0">
                  <tr v-for="authorization in authorizations.data" :key="authorization.id">
                    <td>
                      {{ authorization.network_data.transaction_id }}
                    </td>
                    <td>
                      {{ authorization.card.cardholder.name }}
                    </td>

                    <td>{{ authorization.card.last4 }}</td>

                    <td>
                      {{ formatAmount(+authorization.amount / 100) }}
                      <span class="text-xs uppercase">{{ stripe_currency }}</span>
                    </td>
                    <td>
                      <div class="capitalize" :class="badgeClass(authorization.status)">
                        {{ authorization.status }}
                      </div>
                    </td>
                    <td class="text-center">
                      {{ moment.unix(authorization.created).format('DD MMM, YYYY') }}
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
