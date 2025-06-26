<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import CreditCardDetails from '@/Components/CreditCardDetails.vue'
import StripePagination from '@/Components/StripePagination.vue'
import NoDataFound from '@/Components/Admin/NoDataFound.vue'
import sharedComposable from '@/Composables/sharedComposable'
import moment from 'moment'

defineOptions({ layout: AdminLayout })
const props = defineProps([
  'creditCard',
  'cardholder_data',
  'transactions',
  'has_more',
  'next',
  'prev',
  'stripe_public_key',
  'stripe_currency',
  'card_details',
  'other_state',
  'other_city'
])
const { formatAmount } = sharedComposable()
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader title="Prepaid Card Details" />

    <!-- card -->
    <CreditCardDetails
      :stripe_pk_key="stripe_public_key"
      :revealBtn="true"
      :can-edit="true"
      :credit-card="creditCard"
      :card-details="card_details"
      :other-state="other_state"
      :other-city="other_city"
    />

    <div class="table-responsive mt-8 whitespace-nowrap rounded-primary">
      <p class="mb-2 text-xl font-semibold">Transactions</p>
      <table class="table">
        <thead>
          <tr>
            <th>{{ trans('S.No.') }}</th>
            <th>{{ trans('Transaction id') }}</th>
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
  </main>
</template>
