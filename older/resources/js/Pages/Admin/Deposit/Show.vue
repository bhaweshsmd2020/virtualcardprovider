<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import sharedComposable from '@/Composables/sharedComposable'
import moment from 'moment'
import { useForm } from '@inertiajs/vue3'
import CreditCardDetails from '@/Components/CreditCardDetails.vue'
defineOptions({ layout: AdminLayout })
const props = defineProps(['order', 'invoice_data', 'meta', 'creditCard'])
const { formatCurrency } = sharedComposable()

const form = useForm({
  status: props.order.status,
  cardholder_address: 'user'
})
const updateOrder = () => {
  form.put(route('admin.deposits.update', props.order.id))
}
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader title="Order Details" />

    <!-- card -->
    <CreditCardDetails :can-edit="true" :credit-card="creditCard" />

    <!-- details -->
    <div class="card-body card mt-5 space-y-6">
      <!-- Invoice Header Starts -->
      <div class="flex flex-col justify-between space-y-4 p-1 md:flex-row">
        <div class="flex items-center justify-center md:justify-start">
          <!-- Logo Starts -->
          <div class="flex h-16 w-full items-center gap-4 pr-4">
            <img
              v-lazy="$page.props.primaryData.deep_logo"
              alt="logo"
              class="block h-[45px] dark:hidden"
            />
            <img
              v-lazy="$page.props.primaryData.logo"
              alt="logo"
              class="hidden h-[45px] dark:block"
            />
          </div>
          <!-- Logo Ends -->
        </div>
        <div class="flex flex-col items-start justify-center md:items-end">
          <h4>Invoice #{{ order.invoice_no }}</h4>
          <p class="my-0 py-0 text-sm font-medium text-slate-700 dark:text-slate-300">
            {{ trans('Order Date') }}:
            <span class="font-normal text-slate-600 dark:text-slate-300">
              {{ moment(order.created_at).format('DD-MM-YYYY') }}
            </span>
          </p>

          <p class="my-0 py-0 text-sm font-medium text-slate-700 dark:text-slate-300">
            {{ trans('Status') }}:
            <span class="font-normal capitalize text-slate-600 dark:text-slate-300">
              {{ order.status }}
            </span>
          </p>
        </div>
      </div>
      <!-- Invoice Header Ends -->

      <!-- Invoice Info Starts -->
      <template v-if="order?.gateway && order.payment_id">
        <div class="flex flex-col justify-between space-y-6 p-1 md:flex-row md:space-y-0">
          <div
            class="flex w-full flex-col items-start justify-center md:mb-0 md:w-2/3 md:justify-center"
          >
            <p class="text-xs font-medium uppercase text-slate-400">{{ trans('BILLED FROM') }}</p>
            <h6 class="my-1">{{ invoice_data.company_name }}</h6>
            <p class="whitespace-nowrap text-sm font-normal text-slate-600 dark:text-slate-300">
              {{ invoice_data.address }}
            </p>
            <p class="whitespace-nowrap text-sm font-normal text-slate-600 dark:text-slate-300">
              {{ invoice_data.city }}
            </p>
            <p class="whitespace-nowrap text-sm font-normal text-slate-600 dark:text-slate-300">
              {{ invoice_data.post_code }}
            </p>
            <p class="whitespace-nowrap text-sm font-normal text-slate-600 dark:text-slate-300">
              {{ invoice_data.country }}
            </p>
          </div>

          <div class="flex w-full flex-col items-start justify-center md:w-1/3 md:items-end">
            <p class="text-xs font-medium uppercase text-slate-400">{{ trans('BILLED TO') }}</p>
            <h6 class="my-1">{{ order.user.name }}</h6>
            <p class="whitespace-nowrap text-sm font-normal text-slate-600 dark:text-slate-300">
              {{ order.user.address_line }}
            </p>
            <p class="whitespace-nowrap text-sm font-normal text-slate-600 dark:text-slate-300">
              {{ order.user.email }}
            </p>
            <p class="whitespace-nowrap text-sm font-normal text-slate-600 dark:text-slate-300">
              {{ order.user.phone }}
            </p>
          </div>
        </div>
      </template>
      <!-- Invoice Info Ends -->

      <!-- Product Table Starts -->
      <div class="w-full overflow-auto p-1">
        <div class="min-w-[38rem]">
          <div
            class="table-responsive whitespace-nowrap rounded-primary border border-slate-200 dark:border-slate-600"
          >
            <table class="table-striped table-hover table-md table">
              <tbody>
                <tr>
                  <td>{{ trans('User Name') }}</td>
                  <td>
                    <p class="text-end">{{ trans('Amount') }}</p>
                  </td>
                </tr>
                <tr>
                  <td>{{ order.user.name }}</td>
                  <td>
                    <p class="text-end">{{ formatCurrency(order.amount) }}</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="mt-4 flex items-stretch justify-between">
            <div>
              <p class="my-2 py-0 text-sm font-semibold">
                {{ trans('Payment Method') }}:
                <span class="font-normal"> {{ order?.gateway?.name || 'Wallet Balance' }}</span>
              </p>
              <p class="my-2 py-0 text-sm font-semibold">
                {{ trans('Payment Id') }}:
                <span class="font-normal">{{ order?.payment_id || 'Wallet Balance' }}</span>
              </p>
              <div class="">
                <template v-if="meta != null">
                  <div class="font-semibold">{{ trans('Payment Info:') }}</div>
                  <div class="space-y-1 text-sm">
                    <p v-for="(m, key) in meta" :key="key">
                      <a v-if="key === 'screenshot'" target="_blank" :href="m">
                        {{ trans('Attachment') }}
                      </a>
                      <template v-else>
                        <span class="font-semibold capitalize">{{ key }}</span>
                        : {{ m }}
                      </template>
                    </p>
                  </div>
                </template>
              </div>
            </div>

            <div class="w-60 space-y-3 pr-6">
              <div class="flex items-center justify-between gap-x-2">
                <p class="text-sm text-slate-400 dark:text-slate-300">{{ trans('Subtotal') }}:</p>

                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                  {{
                    formatCurrency(parseFloat(order.amount) - parseFloat(order.deposit_fee || 0))
                  }}
                </p>
              </div>

              <div class="flex items-center justify-between gap-x-2" v-if="order.deposit_fee">
                <p class="text-sm text-slate-400 dark:text-slate-300">
                  {{ trans('Deposit Fee') }}:
                </p>

                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                  {{ formatCurrency(order.deposit_fee) }}
                </p>
              </div>
              <hr class="mb-1 mt-5 border-slate-200 dark:border-slate-600" />
              <div class="flex items-center justify-between gap-x-2">
                <p class="text-sm text-slate-400">{{ trans('Total') }}:</p>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                  {{ formatCurrency(order.amount || 0) }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Product Table Ends -->

      <p class="py-2 text-center text-sm">{{ trans('Thanks for your Business') }}</p>
    </div>

    <div class="card mt-4" v-if="order.status === 'pending'">
      <form @submit.prevent="updateOrder">
        <div class="card-body flex items-end space-x-2">
          <div>
            <label class="label mb-1">{{ trans('Order Status') }}</label>
            <select class="select capitalize" name="status" v-model="form.status">
              <option value="pending">{{ trans('pending') }}</option>

              <option value="approved">{{ trans('approve') }}</option>
              <option value="rejected">{{ trans('Reject') }}</option>
            </select>
          </div>

          <button type="submit" class="btn btn-primary" :disabled="form.processing">
            {{ trans('Update') }}
          </button>
        </div>
      </form>
    </div>
  </main>
</template>
