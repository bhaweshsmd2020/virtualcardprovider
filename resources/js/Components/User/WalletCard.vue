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
</script>

<template>
  <template v-for="(account, i) in accounts" :key="i">
    <div
      class="rounded-md p-4 text-secondary-950 dark:text-secondary-300"
      :class="bgColors[[parseInt(i.toString()[0])]]"
    >
      <div class="flex h-20 justify-between">
        <div class="flex flex-col justify-between">
          <p class="text-xl uppercase">{{ account.currency.currency }}</p>
          <div class="mt-2">
            <p class="text-xl">
              <template
                v-if="
                  defaultCurrencyRates && findRates(account.currency.id) && account?.wallet?.balance
                "
              >
                {{
                  formatCurrency(
                    +findRates(account.currency.id)?.rate * parseFloat(+account?.wallet?.balance),
                    +account.currency.precision
                  )
                }}
              </template>
              <template v-else>
                {{ formatCurrency(+account?.wallet?.balance || 0, +account.currency.precision) }}
              </template>
            </p>
            <p class="uppercase text-gray-500 dark:text-gray-400">
              {{ formatAmount(+account?.wallet?.balance || 0, +account.currency.precision) }}
              {{ account.currency.currency }}
            </p>
          </div>
        </div>

        <img
          class="w-20 object-contain opacity-20 dark:opacity-40 dark:invert dark:filter"
          v-lazy="account.currency.preview"
          alt="preview"
        />
      </div>
      <template v-if="actionButtons">
        <div class="mt-6 flex gap-3" v-if="account.wallet">
          <Link
            :href="route('user.top-up.create-wallet')"
            class="btn flex-1 bg-secondary-50 py-3 text-secondary-950 hover:bg-secondary-200"
          >
            <Icon icon="material-symbols-light:add-rounded" class="text-2xl" />
          </Link>
          <Link
            :href="route('user.exchange.index')"
            class="btn flex-1 bg-secondary-50 py-2 text-secondary-950 hover:bg-secondary-200"
          >
            <Icon icon="basil:exchange-solid" class="text-2xl" />
          </Link>
        </div>
        <div class="mt-6 gap-3" v-else>
          <button
            type="button"
            @click="submit(account.currency.id)"
            class="btn w-full bg-secondary-50 py-2 text-secondary-950 hover:bg-secondary-200"
          >
            <Icon icon="bx:cog" class="text-2xl text-secondary-700" />
            <span>{{ trans('Setup') }}</span>
          </button>
        </div>
      </template>
    </div>
  </template>
</template>
