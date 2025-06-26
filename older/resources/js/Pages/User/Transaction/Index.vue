<script setup>
import UserLayout from '@/Layouts/User/UserLayout.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import trans from '@/Composables/transComposable'
import StripePagination from '@/Components/StripePagination.vue'
import NoDataFound from '@/Components/Admin/NoDataFound.vue'
import sharedComposable from '@/Composables/sharedComposable'
import moment from 'moment'

defineOptions({ layout: UserLayout })

const { formatAmount } = sharedComposable()
const props = defineProps([
  'transactions',
  'request',
  'has_more',
  'next',
  'prev',
  'stripe_currency'
])
</script>

<template>
  <!-- Main Content Starts -->
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader :title="trans('Transactions')" />
    <div class="space-y-4">
      <p class="text-lg font-semibold">{{ trans('Transaction History') }}</p>
      <div class="table-responsive whitespace-nowrap rounded-primary">
        <table class="table">
          <thead>
            <tr>
              <th>{{ trans('S.No.') }}</th>
              <th>{{ trans('Transaction id') }}</th>
              <th>{{ trans('User') }}</th>
              <th>{{ trans('Merchant') }}</th>
              <th>{{ trans('Amount') }}</th>
              <th>{{ trans('Date') }}</th>
              <th>{{ trans('Status') }}</th>
            </tr>
          </thead>
          <tbody v-if="transactions?.data?.length > 0">
            <tr v-for="(transaction, index) in transactions.data" :key="transaction.id">
              <td>
                {{ index + 1 }}
              </td>

              <td>
                {{ transaction.id }}
              </td>

              <td>
                {{ transaction.card_holder.first_name }} {{ transaction.card_holder.last_name }}
              </td>

              <td>
                {{ transaction.merchant_name }} <br> {{ transaction.sk_category_name }}
              </td>

              <td>
                {{ formatAmount(+transaction.amount) }}
                <span class="text-xs uppercase">{{ transaction.currency_code }}</span>
              </td>             

              <td class="text-center">
                {{ moment(transaction.user_transaction_time).format('MMM DD, YYYY, h:mm A') }}
              </td>
              
              <td v-if="transaction.state === 'CLEARED'">
                <span class="badge capitalize badge-success">Success</span>
              </td>
              <td v-else-if="transaction.state === 'PENDING'">
                <span class="badge capitalize badge-info">Pending</span>
              </td>
              <td v-else>
                <span class="badge capitalize badge-danger">Failed</span>
              </td>
            </tr>
          </tbody>
          <NoDataFound v-else for-table="true" />
        </table>
      </div>
      <StripePagination :has_prev="prev" :has_more="has_more" :prev="prev" :next="next" />
    </div>
  </main>
</template>
