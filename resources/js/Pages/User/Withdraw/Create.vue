<script setup>
import sharedComposable from '@/Composables/sharedComposable'
import toast from '@/Composables/toastComposable'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import UserLayout from '@/Layouts/User/UserLayout.vue'
import { useForm } from '@inertiajs/vue3'
import Multiselect from '@vueform/multiselect'
import { computed } from 'vue'
defineOptions({ layout: UserLayout })

const props = defineProps(['accounts'])
const form = useForm({
  type: '',
  currency_id: null,
  amount: 0,
  receiver_email: '',
  meta: {}
})
const { formatCurrency, authUser, calculatePercent } = sharedComposable()
const currencyFrom = computed(() => {
  if (form.currency_id) {
    return props.accounts.find((cur) => cur.currency_id == form.currency_id)
  }
  return null
})
const afterServiceFee = computed(() => {
  return (
    +form.amount - calculatePercent(+form.amount, +authUser.value.plan_data['service_fee']['value'])
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
  if (currencyFrom.value.balance < form.amount) {
    return toast.danger('Insufficient balance')
  }
  form.post(route('user.withdraw.store'))
}
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <div class="flex items-center justify-between">
      <PageHeader title="Transfer" />
      <button v-if="form.type" type="button" @click="form.type = ''" class="btn btn-sm btn-primary">
        <Icon class="text-xl" icon="bx:chevron-left" /> {{ trans('Back') }}
      </button>
    </div>

    <div class="mx-auto mt-6 max-w-lg space-y-5">
      <template v-if="!form.type">
        <p class="text-center text-2xl font-semibold">
          {{ trans('Choose where you want to withdraw to') }}
        </p>
        <button
          @click="form.type = 'user'"
          class="flex w-full items-center gap-3 rounded-md bg-gray-100 p-3 hover:bg-gray-200 dark:bg-secondary-800"
        >
          <div class="rounded-md bg-white p-2 dark:bg-secondary-900">
            <Icon class="text-2xl" icon="bx:user" />
          </div>
          <span>{{ trans(`To ${$page.props.app_name} user account`) }}</span>
        </button>

        <!-- <Link
          :href="route('user.payout.index')"
          class="flex w-full items-center gap-3 rounded-md bg-gray-100 p-3 hover:bg-gray-200 dark:bg-secondary-800"
        >
          <div class="rounded-md bg-white p-2 dark:bg-secondary-900">
            <Icon class="text-2xl" icon="bx:dollar" />
          </div>
          <span>{{ trans('To local payment method') }}</span>
        </Link> -->
      </template>

      <template v-if="form.type === 'user'">
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
            v-model="form.currency_id"
            mode="single"
            label="currency_name"
            value-prop="currency_id"
            :options="accounts"
            placeholder="Account"
          >
            <template v-slot:singlelabel="{ value }">
              <div class="multiselect-single-label">
                <div
                  class="mr-3 flex h-8 w-8 items-center justify-center rounded-md border bg-white"
                >
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
                  <small
                    class="text-xs"
                    v-if="defaultCurrencyRates && findRates(option.currency_id)"
                  >
                    {{ formatCurrency(findRates(+option.currency_id)?.rate * +option?.balance) }}
                  </small>
                  <small class="text-xs" v-else> {{ formatCurrency(+option?.balance || 0) }}</small>
                </p>
              </div>
            </template>
          </Multiselect>
        </div>
        <div>
          <label class="label text-xs">{{ trans("To (Enter the recipient's Email)") }}</label>
          <input type="email" v-model="form.receiver_email" class="input" placeholder="Email" />
        </div>
        <div>
          <label class="label text-xs">
            {{ trans('Transfer amount') }}
            <span class="uppercase" v-if="currencyFrom?.currency_name">
              ( {{ currencyFrom?.currency_name }} )
            </span>
          </label>
          <input
            type="number"
            step="any"
            class="input"
            v-model="form.amount"
            placeholder="Transfer amount"
          />
        </div>
        <div class="rounded-md bg-gray-100 p-4 dark:bg-secondary-800">
          <div class="space-y-5 text-sm">
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
              <p class="text-xs">
                <template
                  v-if="
                    authUser.plan_data['service_fee'] &&
                    authUser.plan_data['service_fee']['value'] > 0
                  "
                >
                  {{ afterServiceFee }}
                  <span class="uppercase" v-if="currencyFrom?.currency_name">
                    {{ currencyFrom.currency_name }}
                  </span>
                </template>
                <template v-else>{{ formatCurrency(+form.amount) }}</template>
              </p>
            </div>
          </div>
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
