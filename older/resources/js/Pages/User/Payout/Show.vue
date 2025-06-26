<script setup>
import { useForm } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/User/UserLayout.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import toast from '@/Composables/toastComposable'
import trans from '@/Composables/transComposable'
import Multiselect from '@vueform/multiselect'
import { computed, ref } from 'vue'
import sharedComposable from '@/Composables/sharedComposable'

defineOptions({ layout: UserLayout })
const props = defineProps(['method', 'fields', 'accounts'])
const { formatCurrency, authUser } = sharedComposable()

const currencyFrom = computed(() => {
  if (form.currency_id) {
    return props.accounts.find((cur) => cur.currency_id == form.currency_id)
  }
  return null
})
const amountEnrolled = computed(() => {
  if (form.payoutWith === 'balance') {
    return +form.amount
  }
  if (form.payoutWith === 'wallet') {
    if (currencyFrom.value && form.amount) {
      const amount = +form.amount * +props.method.rates[currencyFrom.value.currency_name]
      return amount.toFixed(2)
    }
  }
  return 0
})
const form = useForm({
  payoutWith: 'balance',
  amount: 0,
  inputs: {},
  currency_id: ''
})

function sendOtp() {
  if (parseFloat(form.amount) > currencyFrom?.balance) {
    toast.danger(trans('You cant sent payout request greater then ') + currencyFrom?.balance)
    return false
  } else if (form.amount < props.method.min_limit) {
    toast.danger(trans('You cant sent payout request less then ') + props.method.min_limit)
    return false
  } else if (form.amount > props.method.max_limit) {
    toast.danger(
      trans('You cant sent payout request greater then ') +
        props.method.min_limit +
        ' using this method'
    )
    return false
  }

  form.post(route('user.payout.otp', props.method.id), {
    onSuccess: () => {
      toast.danger(trans('A confirmation otp has sent to your email'))
    }
  })
}
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
</script>
<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader />
    <div class="mx-auto space-y-4 xl:w-9/12">
      <div class="card card-body">
        <h3 class="my-8 flex items-start gap-x-4">
          <img class="w-32" v-lazy="method.image" height="30" alt="" />
          {{ trans('Payout method information') }}
        </h3>
        <div class="table-responsive whitespace-nowrap rounded-primary">
          <table class="table">
            <tbody>
              <tr>
                <th>{{ trans('Method name') }}</th>
                <td>{{ method.name }}</td>
                <th>{{ trans('Currency') }}</th>
                <td class="uppercase">{{ method.currency_name }}</td>
              </tr>
              <tr>
                <th>{{ trans('Minimum limit') }}</th>
                <td>{{ method.min_limit }}</td>
                <th>{{ trans('Maximum limit') }}</th>
                <td>{{ method.max_limit }}</td>
              </tr>
              <tr>
                <th>{{ trans('Charge type') }}</th>
                <td>{{ method.charge_type }}</td>
                <th>{{ trans('Charge') }}</th>
                <td>
                  {{
                    method.charge_type == 'percentage'
                      ? method.percent_charge + '%'
                      : method.fixed_charge + ' ' + method.currency_name
                  }}
                </td>
              </tr>
              <tr>
                <th>{{ trans('Maximum Processing Time') }}</th>
                <td colspan="4">{{ method.delay }} {{ trans('Days') }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card card-body space-y-2">
        <p>{{ trans('Select Payout From') }}</p>
        <div class="flex items-center gap-x-2">
          <button
            class="btn"
            :class="
              form.payoutWith === 'balance' ? 'btn-primary' : 'bg-gray-200 dark:bg-secondary-700'
            "
            @click="
              () => {
                form.payoutWith = 'balance'
                form.amount = 0
                form.currency_id = ''
              }
            "
          >
            <Icon icon="fe:credit-card" />
            {{ trans('Account Balance') }}
          </button>
          <!-- <button
            :class="
              form.payoutWith === 'wallet' ? 'btn-primary' : 'bg-gray-200 dark:bg-secondary-700'
            "
            @click="form.payoutWith = 'wallet'"
            class="btn btn-primary"
          >
            <Icon icon="bx:dollar" /> {{ trans('Account') }}
          </button> -->
        </div>
        <div class="my-2 w-1/2" v-if="form.payoutWith === 'balance'">
          {{ trans('Balance') }} : {{ formatCurrency(authUser.balance) }}
        </div>
        <div class="my-2 w-1/2" v-if="form.payoutWith === 'wallet'">
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
                  <p class="flex w-40 translate-x-[10rem] flex-col items-end">
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
                <p class="flex w-40 translate-x-[11rem] flex-col items-end">
                  <span class="text-sm font-semibold uppercase">
                    {{ option.balance || '0.00' }} {{ option.currency_name }}
                  </span>
                  <small
                    class="text-xs"
                    v-if="defaultCurrencyRates && findRates(option.currency_id)"
                  >
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

        <form method="POST" @submit.prevent="sendOtp">
          <div class="mb-6 grid grid-cols-2 gap-2">
            <p class="col-span-full text-lg font-semibold">{{ trans('Payout Details') }}</p>
            <div class="pt-2" v-for="(field, index) in fields" :key="index">
              <label class="label mb-1 capitalize"> {{ field.label }} * </label>
              <input
                class="input"
                v-model="form.inputs[field.label]"
                v-if="field.type != 'textarea'"
                :type="field.type"
                required
              />
              <textarea
                v-else
                required
                class="textarea"
                v-model="form.inputs[field.label]"
              ></textarea>
            </div>
          </div>
          <div>
            <h5 class="mb-6 flex items-center">
              <img class="mr-1 w-8" src="/assets/images/money-bag.png" height="30" alt="money" />
              {{ trans('Enter Amount') }}
            </h5>
            <div class="flex items-end gap-x-2">
              <div class="flex-1">
                <label class="label mb-1">
                  {{ trans('Amount') }}
                  <span
                    class="text-xs uppercase"
                    v-if="form.payoutWith == 'wallet' && form.currency_id"
                  >
                    ( {{ currencyFrom?.currency_name }} )
                  </span>
                  <span class="text-xs uppercase" v-if="form.payoutWith == 'balance'">
                    ( {{ defaultCurrencyRates?.currency_name }} )
                  </span>
                </label>
                <input
                  class="input"
                  :disabled="form.processing"
                  type="number"
                  v-model="form.amount"
                  step="any"
                  :placeholder="trans('Enter Amount')"
                />
              </div>
              <div class="flex-1">
                <label class="label mb-1">{{ trans('Amount enrolled') }}</label>
                <input
                  disabled
                  readonly
                  :value="amountEnrolled"
                  type="number"
                  class="input"
                  placeholder="Amount enrolled"
                />
              </div>

              <button
                :disabled="form.processing || !form.amount"
                type="submit"
                class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-600 hover:bg-opacity-80 disabled:cursor-not-allowed"
              >
                <i class="bx bx-chevron-right text-white"></i>
              </button>
            </div>
            <div
              v-if="form.currency_id && form.payoutWith == 'wallet'"
              class="my-2 flex items-center justify-end gap-2 text-sm dark:text-gray-300"
            >
              <p>{{ trans('Exchange rate') }} :</p>
              <p>
                1 <span class="uppercase"> {{ currencyFrom.currency_name }}</span> =
                {{ method.rates[currencyFrom.currency_name] }}
                <span class="uppercase">{{ method.currency_name }}</span>
              </p>
              <p v-if="form.payoutWith == 'balance'">
                1 <span class="uppercase"> {{ defaultCurrencyRates.currency_name }}</span> = 1
                <span class="uppercase">{{ defaultCurrencyRates.currency_name }}</span>
              </p>
            </div>
            <div
              v-if="form.payoutWith == 'balance'"
              class="my-2 flex items-center justify-end gap-2 text-sm dark:text-gray-300"
            >
              <p>{{ trans('Exchange rate') }} :</p>

              <p>
                1 <span class="uppercase"> {{ defaultCurrencyRates.currency_name }}</span> = 1
                <span class="uppercase">{{ defaultCurrencyRates.currency_name }}</span>
              </p>
            </div>
            <small class="text-danger" v-if="form.errors.amount" :disabled="form.processing">{{
              form.errors.amount
            }}</small>
          </div>
        </form>
        <div
          class="my-8 flex items-center gap-x-4"
          v-if="parseFloat(form.amount) > currencyFrom?.balance"
        >
          <img class="w-16" src="/assets/images/sorry.png" alt="sorry" />
          <div>
            <h4>
              {{ trans('I am sorry') }}
            </h4>
            <p>
              {{ trans("You don't have enough balance.") }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>
