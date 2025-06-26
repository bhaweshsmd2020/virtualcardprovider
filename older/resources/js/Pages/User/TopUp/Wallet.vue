<script setup>
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import UserLayout from '@/Layouts/User/UserLayout.vue'
import { ref, computed } from 'vue'
import toast from '@/Composables/toastComposable'
import { router } from '@inertiajs/vue3'
import sharedComposable from '@/Composables/sharedComposable'
import Multiselect from '@vueform/multiselect'

defineOptions({ layout: UserLayout })
const form = ref({
  currency: '',
  type: 'balance',
  wallet_id: '',
  amount: 50
})
const props = defineProps(['gateways', 'accounts'])

//credits
const { formatCurrency, authUser } = sharedComposable()
const activeGateway = ref(props.gateways[0]?.id || 0)
const phone = ref(null)
const isProcessing = ref(false)
const manualPayment = ref({
  image: null,
  comment: ''
})

const findGateway = computed(() => {
  return props.gateways.find((gateway) => gateway.id === activeGateway.value)
})
const findAccount = computed(() => {
  if (form.value.wallet_id) {
    return props.accounts.find((ac) => ac.wallet_id === form.value.wallet_id)
  }
  return []
})
const defaultCurrencyRates = computed(() => {
  const account = props.accounts.find((ac) => ac.is_default === 1)
  return account ? account : null
})
const findAccountRate = computed(() => {
  if (defaultCurrencyRates.value) {
    return defaultCurrencyRates.value.rates.find(
      (cur) => cur.virtual_currency_exchange_id == findAccount.value.currency_id
    )?.rate
  }
})
const submit = (gateway_id) => {
  if (findGateway.value.min_amount > amountEnrolled.value) {
    toast.danger(
      'The minimum transaction amount is ' + formatCurrency(findGateway.value.min_amount)
    )
    return
  }
  if (findGateway.value.max_amount != -1) {
    if (findGateway.value.max_amount < amountEnrolled.value) {
      toast.danger(
        'The maximum transaction amount is ' + formatCurrency(findGateway.value.max_amount)
      )
      return
    }
  }
  isProcessing.value = true
  router.post(
    route('user.top-up.store'),
    {
      gateway_id: gateway_id,
      total: amountEnrolled.value,
      requested_amount: form.value.amount,
      manualPayment: findGateway.value.is_auto == 0 ? manualPayment.value : null,
      wallet_id: form.value.wallet_id,
      rate: findAccountRate.value || 1,
      phone: findGateway.value.phone_required == 1 ? phone.value : null
    },
    {
      onFinish: () => {
        isProcessing.value = false
      }
    }
  )
}

const findRates = (id) => {
  const account = props.accounts.find((ac) => ac.currency_id == id)
  const rates = account.rates.find(
    (rate) => rate.virtual_currency_exchange_id === defaultCurrencyRates.value.currency_id
  )

  return rates
}

const amountEnrolled = computed(() => {
  if (form.value.wallet_id) {
    if (defaultCurrencyRates.value && findRates(findAccount.value.currency_id)) {
      const amount = +form.value.amount * +findRates(findAccount.value.currency_id)?.rate
      return amount.toFixed(2)
    } else {
      const amount = +form.value.amount
      return amount
    }
  }
  return 0
})
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader title="Topup" />
    <div class="mx-auto max-w-lg">
      <div class="mb-2">
        <label class="label text-xs"> {{ trans('Select Account') }}</label>
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
      <div
        v-if="form.wallet_id"
        class="my-2 flex items-center justify-end gap-2 dark:text-gray-300"
      >
        <p class="text-sm">{{ trans('Exchange rate') }} :</p>
        <p class="text-xs">
          1 <span class="uppercase"> {{ findAccount?.currency_name }}</span> =
          {{
            defaultCurrencyRates && findRates(findAccount.currency_id)
              ? findRates(findAccount.currency_id)?.rate
              : 1
          }}
          <span class="uppercase">{{ defaultCurrencyRates.currency_name }}</span>
        </p>
      </div>
    </div>

    <div class="mx-auto max-w-lg space-y-5">
      <p class="text-xl font-semibold">{{ trans('Method of payment') }}</p>

      <div>
        <label class="label mb-1">
          {{ trans('Amount') }}
          <span class="text-xs uppercase" v-if="findAccount?.currency_name">
            ( {{ findAccount?.currency_name }} )
          </span>
        </label>
        <input
          class="input"
          step="5"
          type="number"
          v-model.number="form.amount"
          placeholder="Enter Credit amount"
        />
      </div>
      <div>
        <label class="label mb-1">
          {{ trans('Amount enrolled') }}
          <span class="text-xs uppercase" v-if="defaultCurrencyRates?.currency_name">
            ( {{ defaultCurrencyRates?.currency_name }} )
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
      <p class="mt-2 font-semibold">{{ trans('Total:') }} {{ formatCurrency(amountEnrolled) }}</p>
      <div class="flex items-center justify-between">
        <p class="label dark:text-gray-200">
          {{ trans('Transaction Fee:') }}
        </p>
        <p class="text-sm dark:text-gray-200">
          <template
            v-if="
              authUser.plan_data['transaction_fee'] &&
              authUser.plan_data['transaction_fee']['value'] > 0
            "
          >
            {{ authUser.plan_data['transaction_fee']['value'] }} %
          </template>
          <template v-else> {{ trans('Not charged') }}</template>
        </p>
      </div>
      <div class="my-4 grid grid-cols-4">
        <template v-for="gateway in gateways" :key="gateway.id">
          <button
            class="relative rounded dark:border-gray-600"
            @click="activeGateway = gateway.id"
            :class="{ border: activeGateway == gateway.id }"
          >
            <div v-show="activeGateway == gateway.id">
              <i
                class="bx bx-check absolute right-[-10px] top-[-10px] rounded-full bg-primary-600 p-0.5 text-white"
              ></i>
            </div>
            <img v-lazy="gateway.logo" class="h-8 w-full rounded object-contain p-1" />
          </button>
        </template>
      </div>
      <template v-for="gateway in gateways" :key="gateway.id">
        <div v-show="activeGateway === gateway.id" :id="'gateway-form' + gateway.id">
          <form method="post" @submit.prevent="submit(gateway.id)" enctype="multipart/form-data">
            <div class="flex items-center justify-between">
              <p class="label dark:text-gray-200">
                {{ trans('Gateway Charge: ') }}
              </p>
              <p class="text-sm dark:text-gray-200">{{ formatCurrency(gateway.charge) }}</p>
            </div>
            <template v-if="gateway.comment != null">
              <label class="label mt-4 font-semibold dark:text-gray-200">
                {{ trans('Payment Instruction: ') }}
              </label>
              <p class="mt-1 text-sm dark:text-gray-200">{{ gateway.comment }}</p>
            </template>

            <template v-if="gateway.phone_required == 1">
              <div class="mt-4">
                <label class="label mb-1">{{ trans('Your phone number') }}</label>
                <input
                  type="tel"
                  name="phone"
                  placeholder="Your phone number"
                  class="input"
                  v-model="phone"
                />
              </div>
            </template>
            <template v-if="gateway.is_auto == 0 && findGateway?.is_crypto != 1">
              <div class="mt-2">
                <label class="label mb-1">{{ trans('Submit your payment proof') }}</label>
                <input
                  @input="
                    (e) => {
                      manualPayment.image = e.target.files[0]
                    }
                  "
                  type="file"
                  name="image"
                  class="input"
                  required
                  accept="image/*"
                />
              </div>
              <div class="mt-3">
                <label class="label mb-1">{{ trans('Comment') }}</label>
                <textarea
                  v-model="manualPayment.comment"
                  class="textarea"
                  required
                  name="comment"
                  placeholder="comment"
                  maxlength="500"
                ></textarea>
              </div>
            </template>

            <button
              :disabled="isProcessing"
              type="submit"
              class="btn btn-primary mt-4 w-full py-2 text-lg"
            >
              {{ trans('Pay Now') }}
            </button>
          </form>
        </div>
      </template>
    </div>
  </main>
</template>
