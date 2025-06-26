<script setup>
import InputFieldError from '@/Components/InputFieldError.vue'
import sharedComposable from '@/Composables/sharedComposable'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import UserLayout from '@/Layouts/User/UserLayout.vue'
import { useForm } from '@inertiajs/vue3'
import Multiselect from '@vueform/multiselect'
import { computed } from 'vue'
defineOptions({ layout: UserLayout })

const props = defineProps(['accounts', 'request', 'exchange_details'])
const { formatCurrency, authUser, calculatePercent } = sharedComposable()
const form = useForm({
  from: '',
  to: '',
  amount: 0
})

const currencyFrom = computed(() => {
  if (form.from) {
    return props.accounts.find((cur) => cur.currency_id == form.from)
  }
  return null
})
const currencyTo = computed(() => {
  if (form.to) {
    return props.accounts.find((cur) => cur.currency_id == form.to)
  }
  return null
})
const amountEnrolled = computed(() => {
  if (currencyFrom.value && currencyTo.value && form.amount) {
    const findRate = currencyFrom.value.rates.find(
      (rate) => rate.virtual_currency_exchange_id == currencyTo.value.currency_id
    )
    const amount = +form.amount * +findRate.rate

    return amount
  }
  return 0
})
const afterServiceFee = computed(() => {
  return (
    +amountEnrolled.value -
    calculatePercent(+amountEnrolled.value, +authUser.value.plan_data['service_fee']['value'])
  )
})
const defaultCurrencyRates = computed(() => {
  const account = props.accounts.find((ac) => ac.is_default === 1)
  return account ? account : null
})
const findRates = (id) => {
  const account = props.accounts.find((ac) => ac.currency_id == id)
  const rates = account.rates.find(
    (rate) => rate.virtual_currency_exchange_id === defaultCurrencyRates.value.currency_id
  )
  return rates
}
const submit = () => {
  form.post(route('user.exchange.store'), {
    onSuccess: () => {
      form.reset()
    }
  })
}
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader title="Exchange Currency" />
    <div class="mx-auto max-w-lg space-y-6">
      <div>
        <label class="label text-xs">{{ trans('From') }}</label>
        <Multiselect
          @change="() => form.reset('amount', 'to')"
          class="multiselect-dark"
          :searchable="true"
          v-model="form.from"
          mode="single"
          label="currency_name"
          value-prop="currency_id"
          :options="accounts"
          placeholder="Currency"
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
        <InputFieldError
          class="text-xs font-semibold"
          v-if="parseFloat(+form.amount) > parseFloat(+currencyFrom?.balance)"
          message="Not enough funds"
        />
      </div>
      <div>
        <label class="label text-xs">{{ trans('To') }}</label>
        <Multiselect
          @change="() => form.reset('amount')"
          class="multiselect-dark"
          :searchable="true"
          v-model="form.to"
          mode="single"
          label="currency_name"
          value-prop="currency_id"
          :options="accounts.filter((a) => a.currency_id != form.from)"
          placeholder="Currency"
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

      <template v-if="form.from && form.to">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="label text-xs">
              {{ trans('Transfer amount') }}
              <span class="text-xs uppercase" v-if="currencyFrom?.currency_name">
                ( {{ currencyFrom?.currency_name }} )
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
            <label class="label text-xs">
              {{ trans('Amount enrolled') }}
              <span class="text-xs uppercase" v-if="currencyTo?.currency_name">
                ( {{ currencyTo?.currency_name }} )
              </span>
            </label>
            <input
              :value="amountEnrolled"
              disabled
              type="number"
              class="input"
              placeholder="Amount enrolled"
            />
          </div>
        </div>
        <div class="rounded-md bg-gray-100 p-4 dark:bg-slate-800">
          <div class="space-y-5 text-sm">
            <div class="flex items-center justify-between">
              <p class="text-gray-400">{{ trans('Exchange rate') }}</p>
              <p class="text-xs">
                1 <span class="uppercase">{{ currencyFrom.currency_name }}</span> =
                {{
                  currencyFrom.rates.find(
                    (rate) => rate.virtual_currency_exchange_id == currencyTo.currency_id
                  )?.rate
                }}
                <span class="uppercase">{{ currencyTo.currency_name }}</span>
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
                  <span
                    class="text-xs uppercase"
                    v-if="currencyTo?.currency_name && amountEnrolled > 0"
                  >
                    {{ currencyTo?.currency_name }}
                  </span>
                  {{ afterServiceFee }}
                </template>
                <template v-else>{{ formatCurrency(+amountEnrolled) }}</template>
              </p>
            </div>
          </div>
        </div>
        <div v-if="exchange_details" class="rounded-md bg-gray-100 p-4 dark:bg-slate-800">
          <p>{{ exchange_details.title }}</p>
          <ul class="ml-6 mt-4 list-disc space-y-2">
            <li v-for="(detail, index) in exchange_details.items" :key="index">
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
