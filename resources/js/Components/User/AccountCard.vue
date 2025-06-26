<script setup>
import sharedComposable from '@/Composables/sharedComposable'
import { router } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  accounts: [Object, Array],
  actionButtons: {
    type: Boolean,
    default: true
  }
})
const { formatCurrency, formatAmount } = sharedComposable()
const submit = (virtual_currency_id) => {
  router.post(route('user.wallet.store', { virtual_currency_id }))
}
const defaultCurrencyRates = computed(() => {
  const account = props.accounts.find((ac) => ac.currency.is_default === 1)
  return account ? account.currency : null
})
const findRates = (id) => {
  const account = props.accounts.find((ac) => ac.currency.id == id)
  const rates = account.currency.rates.find(
    (rate) => rate.virtual_currency_exchange_id === defaultCurrencyRates.value.id
  )
  return rates
}
const bgColors = [
  'bg-orange-200/30',
  'bg-yellow-200/30',
  'bg-purple-200/30',
  'bg-blue-200/30',
  'bg-red-200/30',
  'bg-pink-200/30',
  'bg-orange-200/30',
  'bg-gray-200/30',
  'bg-teal-200/30'
]
</script>

<template>
  <template v-for="(account, i) in accounts" :key="i">
    <div
      class="rounded-md p-4 text-secondary-950 dark:text-secondary-300"
      :class="bgColors[[parseInt(i.toString()[0])]]"
    >
      <div class="flex h-20 justify-between">
        <div class="flex flex-col justify-between">
          <p class="text-md uppercase">Wallet Balance</p>
          <div class="mt-2">
            <p class="text-xl">
              {{ formatCurrency(+account.balance) }}
            </p>
          </div>
        </div>

        <img class="w-20 object-contain opacity-20 dark:opacity-40 dark:invert dark:filter" alt="preview" lazy="loaded" src="/demo/tsA7ssiQNJq7v3J0QCwq.svg">
      </div>
      <template v-if="actionButtons">
        <div class="mt-6 flex gap-3" v-if="account.balance">
          <Link
            :href="route('user.top-up.create')"
            class="btn flex-1 bg-secondary-50 py-3 text-secondary-950 hover:bg-secondary-200"
          >
            <span>{{ trans('Topup Wallet Balance') }}</span>
          </Link>
        </div>
      </template>
    </div>
  </template>
</template>
