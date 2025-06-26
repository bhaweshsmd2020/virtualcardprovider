<script setup>
import { onMounted, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import UserLayout from '@/Layouts/User/UserLayout.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import Deposit from './Deposit.vue'
import { useModalStore } from '@/Store/modalStore'
import StripePagination from '@/Components/StripePagination.vue'
import CreditCardDetails from '@/Components/CreditCardDetails.vue'
import NoDataFound from '@/Components/Admin/NoDataFound.vue'
import sharedComposable from '@/Composables/sharedComposable'
import moment from 'moment'

const model = useModalStore()
defineOptions({ layout: UserLayout })
const props = defineProps([
  'creditCard',
  'cardholder_data',
  'buttons',
  'gateways',
  'transactions',
  'has_more',
  'next',
  'prev',
  'stripe_public_key',
  'stripe_currency',
  'card_details',
  'prepaidcard',
  'other_state',
  'other_city'
])

const { formatAmount } = sharedComposable()
const page = usePage() // Get Inertia page props

onMounted(() => {
  showAlert(page.props.flash)
})

// Watch for flash messages and trigger alert
watch(() => page.props.flash, (newFlash) => {
  showAlert(newFlash)
})

function showAlert(flash) {
  if (flash && flash.message) {
    Swal.fire({
      icon: flash.type === "success" ? "success" : "warning",
      title: `<h2 style='font-size: 20px;'>${flash.message}</h2>`,
      confirmButtonText: "Okay",
      allowOutsideClick: false,
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
    })
  }
}
</script>

<template>
  <Deposit :gateways="gateways" :credit_card_id="creditCard.id" :virtual_card="creditCard.virtual_card" :prepaidcard="prepaidcard" :card_details="card_details" />
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader title="Card Details" />
    <div class="mb-4 text-right">
      <button class="btn btn-primary" @click="model.open('deposit')">
        <span><i class="bx bx-plus"></i>&nbsp&nbsp{{ trans('Card Topup') }}</span>
      </button>
    </div>
    <!-- card -->

    <CreditCardDetails
      :stripe_pk_key="stripe_public_key"
      :revealBtn="true"
      :credit-card="creditCard"
      :card-details="card_details"
      :other-state="other_state"
      :other-city="other_city"
    />
    <div class="table-responsive mt-8 whitespace-nowrap rounded-primary">
      <p class="mb-2 text-xl font-semibold">{{ trans('Transactions') }}</p>
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
