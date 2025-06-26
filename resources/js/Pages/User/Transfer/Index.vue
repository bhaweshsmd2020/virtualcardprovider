<script setup>
import sharedComposable from '@/Composables/sharedComposable'
import toast from '@/Composables/toastComposable'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import UserLayout from '@/Layouts/User/UserLayout.vue'
import { useForm } from '@inertiajs/vue3'
import Multiselect from '@vueform/multiselect'
import { computed } from 'vue'
defineOptions({ layout: UserLayout })

const props = defineProps(['accounts', 'transfer_details'])
const { formatCurrency, authUser, calculatePercent } = sharedComposable()
const form = useForm({
  transfer_to: 'balance',
  wallet_id: '',
  amount: 0
})

const findAccount = computed(() => {
  return form.wallet_id ? props.accounts.find((ac) => ac.wallet_id === form.wallet_id) : []
})

const defaultCurrencyRates = computed(() => {
  const account = props.accounts.find((ac) => ac.is_default === 1)
  return account ? account : null
})
const findRates = (id) => {
  const account = props.accounts.find((ac) => ac.currency_id == id)
  if (form.transfer_to == 'balance') {
    const rates = account.rates.find(
      (rate) => rate.virtual_currency_exchange_id === defaultCurrencyRates.value.currency_id
    )
    return rates
  }
  if (form.transfer_to == 'wallet' && findAccount.value) {
    const rates = defaultCurrencyRates.value.rates.find(
      (rate) => rate.virtual_currency_exchange_id === account.currency_id
    )
    return rates
  }
}
const amountEnrolled = computed(() => {
  if (!form.wallet_id) return 0
  if (findAccount.value.currency_id == defaultCurrencyRates.value.currency_id) {
    return +form.amount
  }
  if (findRates(findAccount.value.currency_id)) {
    return +findRates(findAccount.value.currency_id)?.rate * form.amount
  }

  return 0
})
const afterServiceFee = computed(() => {
  return (
    +amountEnrolled.value -
    calculatePercent(+amountEnrolled.value, +authUser.value.plan_data['service_fee']['value'])
  )
})
const submit = () => {
  if (form.transfer_to === 'wallet' && form.amount > authUser.value.balance) {
    toast.danger('Insufficient balance')
    return
  }
  if (form.transfer_to === 'balance' && form.amount > findAccount.value.balance) {
    toast.danger('Insufficient wallet balance')
    return
  }
  form
    .transform((data) => ({
      ...data,
      requestedAmount: data.amount,
      amount: +amountEnrolled.value,
      default_currency: defaultCurrencyRates.value.currency_name,
      transfer_currency: findAccount.value.currency_name
    }))
    .post(route('user.transfer.store'), {
      onSuccess: () => {
        form.reset()
      }
    })
}
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader title="Transfer" />
    <div class="mx-auto max-w-lg space-y-6">
      <div class="flex items-center gap-3">
        <button
          class="btn flex-1"
          :class="
            form.transfer_to === 'balance' ? 'btn-primary' : 'bg-gray-200 dark:bg-secondary-700'
          "
          @click="
            () => {
              form.reset()
              form.transfer_to = 'balance'
            }
          "
        >
          {{ trans('Wallet') }} <Icon class="text-lg" icon="heroicons:arrow-long-right" />
          {{ trans('Account') }}
        </button>
        <button
          class="btn flex-1"
          :class="
            form.transfer_to === 'wallet' ? 'btn-primary' : 'bg-gray-200 dark:bg-secondary-700'
          "
          @click="
            () => {
              form.reset()
              form.transfer_to = 'wallet'
            }
          "
        >
          {{ trans('Account') }} <Icon class="text-lg" icon="heroicons:arrow-long-right" />
          {{ trans('Wallet') }}
        </button>
      </div>
      <div class="mb-2">
        <label class="label text-xs">{{ trans('Select Account') }}</label>
        <Multiselect
          @change="
            () => {
              form.amount = 0
            }
          "
          class="multiselect-dark"
          :searchable="true"
          v-model="form.wallet_id"
          mode="single"
          label="currency_name"
          value-prop="wallet_id"
          :options="accounts"
          placeholder="Account"
        >
          <template v-slot:singlelabel="{ value }">
            <div class="multiselect-single-label">
              <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-md border bg-white">
                <img class="h-5 w-5" v-lazy="value.preview" />
              </div>
              <div class="flex items-center justify-between">
                <p class="text-sm">
                  <span class="uppercase">{{ value.currency_name }}</span> {{ trans('account') }}
                </p>
                <p class="flex w-40 translate-x-[9rem] flex-col items-end">
                  <span class="text-sm font-semibold uppercase leading-4">
                    {{ value.balance || '0.00' }} {{ value.currency_name }}
                  </span>

                  <small
                    class="text-xs"
                    v-if="defaultCurrencyRates && findRates(value.currency_id)"
                  >
                    {{
                      formatCurrency(
                        findRates(+value.currency_id)?.rate * +value?.balance,
                        value.precision
                      )
                    }}
                  </small>
                  <small class="text-xs" v-else>
                    {{ formatCurrency(+value?.balance || 0) }}
                  </small>
                </p>
              </div>
            </div>
          </template>

          <template v-slot:option="{ option }">
            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-md border bg-white">
              <img class="h-5 w-5" v-lazy="option.preview" />
            </div>
            <div class="flex items-center justify-between">
              <p class="text-sm">
                <span class="uppercase">{{ option.currency_name }}</span> {{ trans('account') }}
              </p>
              <p class="flex w-40 translate-x-[10.8rem] flex-col items-end">
                <span class="text-sm font-semibold uppercase">
                  {{ option.balance || '0.00' }} {{ option.currency_name }}
                </span>
                <small class="text-xs" v-if="defaultCurrencyRates && findRates(option.currency_id)">
                  {{
                    formatCurrency(
                      findRates(+option.currency_id)?.rate * +option?.balance,
                      option.precision
                    )
                  }}
                </small>
                <small class="text-xs" v-else> {{ formatCurrency(+option?.balance || 0) }}</small>
              </p>
            </div>
          </template>
        </Multiselect>
      </div>
      <p class="text-sm">{{ trans('Current Balance') }} {{ formatCurrency(authUser.balance) }}</p>
      <template v-if="form.wallet_id && form.transfer_to">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="label mb-1">
              {{ trans('Transfer amount') }}
              <span
                class="text-xs uppercase"
                v-if="findAccount?.currency_name && form.transfer_to === 'balance'"
              >
                ( {{ findAccount?.currency_name }} )
              </span>
              <span class="text-xs uppercase" v-if="form.transfer_to === 'wallet'">
                ( {{ defaultCurrencyRates?.currency_name }} )
              </span>
            </label>
            <input
              v-model="form.amount"
              type="number"
              class="input"
              placeholder="Transfer amount"
            />
          </div>
          <div>
            <label class="label mb-1">
              {{ trans('Amount enrolled') }}
              <span class="text-xs uppercase" v-if="form.transfer_to === 'balance'">
                ( {{ defaultCurrencyRates?.currency_name }} )
              </span>
              <span
                class="text-xs uppercase"
                v-if="findAccount?.currency_name && form.transfer_to === 'wallet'"
              >
                ( {{ findAccount?.currency_name }} )
              </span>
            </label>
            <input
              disabled
              readonly
              :value="amountEnrolled"
              type="number"
              class="input"
              placeholder="Amount enrolled"
            />
          </div>
        </div>
        <div class="rounded-md bg-gray-100 p-4 dark:bg-slate-800">
          <div class="space-y-5 text-sm">
            <div class="flex items-center justify-between">
              <p class="text-sm">{{ trans('Exchange rate') }} :</p>
              <p class="text-xs" v-if="form.transfer_to === 'balance'">
                1 <span class="uppercase"> {{ findAccount.currency_name }}</span> =
                {{
                  defaultCurrencyRates && findRates(findAccount.currency_id)
                    ? findRates(findAccount.currency_id)?.rate
                    : 1
                }}
                <span class="uppercase">
                  {{ defaultCurrencyRates.currency_name }}
                </span>
              </p>
              <p class="text-xs" v-else>
                1 <span class="uppercase"> {{ defaultCurrencyRates.currency_name }}</span> =
                {{
                  defaultCurrencyRates && findRates(findAccount.currency_id)
                    ? findRates(findAccount.currency_id)?.rate
                    : 1
                }}
                <span class="uppercase">
                  {{ findAccount.currency_name }}
                </span>
              </p>
            </div>
            <div class="flex items-center justify-between text-gray-400">
              <p>{{ trans('Service fee') }}</p>
              <p class="text-sm">
                <template
                  v-if="
                    authUser.plan_data['service_fee'] &&
                    authUser.plan_data['service_fee']['value'] > 0
                  "
                >
                  {{ authUser.plan_data['service_fee']['value'] }} %
                </template>
                <template v-else>{{ trans('Not charged') }}</template>
              </p>
            </div>

            <div class="flex items-center justify-between text-gray-400">
              <p>{{ trans('Total') }}</p>
              <p class="text-sm">
                <template
                  v-if="
                    authUser.plan_data['service_fee'] &&
                    authUser.plan_data['service_fee']['value'] > 0
                  "
                >
                  {{ afterServiceFee }}
                  <span class="text-xs uppercase" v-if="form.transfer_to === 'balance'">
                    {{ defaultCurrencyRates.currency_name }}</span
                  >
                  <span class="text-xs uppercase" v-if="form.transfer_to === 'wallet'">
                    {{ findAccount.currency_name }}</span
                  >
                </template>
                <template v-else>{{ formatCurrency(+amountEnrolled) }}</template>
              </p>
            </div>
          </div>
        </div>
        <div v-if="transfer_details" class="rounded-md bg-gray-100 p-4 dark:bg-slate-800">
          <p>{{ transfer_details.title }}</p>
          <ul class="ml-6 mt-4 list-disc space-y-2">
            <li v-for="(detail, index) in transfer_details.items" :key="index">
              {{ detail.value }}
            </li>
          </ul>
        </div>
        <button
          @click="submit"
          type="button"
          class="btn w-full bg-secondary-950 py-3 text-secondary-50 hover:bg-secondary-800"
        >
          {{ trans('Continue') }}
        </button>
      </template>
    </div>
  </main>
</template>
