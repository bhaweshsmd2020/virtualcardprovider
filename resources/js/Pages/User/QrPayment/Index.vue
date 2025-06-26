<script setup>
import { useForm, router, Head } from '@inertiajs/vue3'
import { ref } from 'vue'
import toast from '@/Composables/toastComposable'
import BlankLayout from '@/Layouts/BlankLayout.vue'
import sharedComposable from '@/Composables/sharedComposable'
defineOptions({ layout: BlankLayout })
const { formatCurrency, uiAvatar } = sharedComposable()
const props = defineProps(['gateways', 'invoice_data', 'user'])
const activeGateway = ref(props.gateways[0]?.id || 0)

const amount = ref(0)
const senderInfo = ref({
  note: '',
  name: '',
  email: '',
  phone: ''
})

const form = useForm({})
const submit = (gateway_id) => {
  const findGateway = props.gateways.find((gateway) => gateway.id === gateway_id)
  if (findGateway.min_amount > amount.value) {
    toast.danger(
      'The minimum transaction amount is ' + formatCurrency(findGateway.value.min_amount)
    )
    return
  }
  router.post(route('qr-top-up.store'), {
    gateway_id: gateway_id,
    total: amount.value,
    uuid: props.user.uuid,
    senderInfo: senderInfo.value,
    phone: findGateway.phone_required == 1 ? senderInfo.value.phone : null
  })
}
const setActiveGateway = (id) => {
  activeGateway.value = id
}
</script>

<template>
  <Head :title="trans('Payment')" />
  <div class="payment-container">
    <div class="payment-content">
      <div class="payment-header">
        <img class="rounded-full" v-lazy="uiAvatar(user.first_name, user.avatar)" alt="logo" />
      </div>

      <div class="gateways">
        <template v-for="gateway in gateways" :key="gateway.id">
          <button
            @click="setActiveGateway(gateway.id)"
            :class="{ 'payment-border': activeGateway == gateway.id }"
          >
            <div v-show="activeGateway == gateway.id">
              <svg
                class="active-gateway"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="currentColor"
                width="24"
                height="24"
              >
                <path
                  fill-rule="evenodd"
                  d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>

            <img v-lazy="gateway.logo" />
          </button>
        </template>
      </div>
      <div class="mb-3 text-gray-900">
        <label class="text-sm text-gray-900">{{ trans('Amount') }}</label>
        <input
          type="number"
          required
          v-model="amount"
          maxlength="10"
          class="block w-full rounded-md px-2 py-1.5 focus:border-primary-600"
        />
      </div>
      <!-- gateways table -->
      <template v-for="gateway in gateways" :key="gateway.id">
        <div
          v-show="activeGateway === gateway.id"
          class="gateway-form"
          :id="'gateway-form' + gateway.id"
        >
          <form method="post" @submit.prevent="submit(gateway.id)" enctype="multipart/form-data">
            <table class="payment-table">
              <tr>
                <td>
                  {{ trans('Method Name: ') }}
                </td>
                <td class="text-center">
                  {{ gateway.name }}
                </td>
              </tr>
              <template v-if="gateway.currency != null">
                <tr>
                  <td>
                    {{ trans('Gateway Currency: ') }}
                  </td>
                  <td class="text-center">
                    {{ gateway.currency }}
                  </td>
                </tr>
              </template>
              <template v-if="gateway.charge != 0">
                <tr>
                  <td>
                    {{ trans('Gateway Charge: ') }}
                  </td>
                  <td class="text-center">
                    {{ formatCurrency(gateway.charge) }}
                  </td>
                </tr>
              </template>
              <tr>
                <td>
                  {{ trans('Payable Amount: ') }}
                </td>
                <td class="text-center">
                  {{ formatCurrency(amount * gateway.multiply + gateway.charge) }}
                </td>
              </tr>
            </table>

            <template v-if="gateway.comment != null">
              <p class="payment-label">
                <b>{{ trans('Payment Instruction: ') }}</b>
              </p>
              <p>{{ gateway.comment }}</p>
            </template>
            <template v-if="gateway.phone_required == 1">
              <div class="text-gray-900">
                <label class="payment-label">
                  {{ trans('Your phone number') }}
                </label>
                <input
                  type="tel"
                  name="phone"
                  placeholder="Your phone number"
                  class="payment-input rounded-md py-1.5"
                  v-model="senderInfo.phone"
                />
              </div>
            </template>
            <div class="text-gray-900">
              <label class="payment-label">
                {{ trans('Name') }}
              </label>
              <input
                class="block w-full rounded-md px-2 py-1.5 focus:border-primary-600"
                v-model="senderInfo.name"
                required
                placeholder="Name"
              />
            </div>
            <div class="my-2 text-gray-900">
              <label class="payment-label">
                {{ trans('Email') }}
              </label>
              <input
                class="block w-full rounded-md px-2 py-1.5 focus:border-primary-600"
                type="email"
                v-model="senderInfo.email"
                required
                placeholder="Email"
              />
            </div>
            <div class="text-gray-900">
              <label class="payment-label">
                {{ trans('Note') }}
              </label>
              <textarea
                class="payment-textarea border border-gray-400 focus:border-primary-600"
                v-model="senderInfo.note"
                required
                name="Note"
                placeholder="Note"
                maxlength="500"
              ></textarea>
            </div>

            <button :disabled="form.processing" type="submit" class="payment-pay-btn mt-6">
              {{ trans('Pay Now') }}
            </button>
          </form>
        </div>
      </template>

      <br />

      <div class="payment-invoice">
        <div class="payment-border">
          <b>{{ trans('Invoiced To') }}</b>
          <br />
          {{ user.name }}<br />
          {{ user.address }}
        </div>
        <div class="payment-border">
          <b>{{ trans('Pay To') }}</b>
          <br />
          {{ invoice_data.company_name }} <br />
          {{ invoice_data.address }}, {{ invoice_data.city }} <br />
          {{ invoice_data.post_code }},
          {{ invoice_data.country }}
        </div>
      </div>

      <div class="payment-details">
        <table class="payment-table">
          <tr>
            <td>
              <b>{{ trans('Name') }}</b>
            </td>
          </tr>

          <tr>
            <td>{{ user.name }}</td>
          </tr>

          <tr>
            <td>{{ trans('Total') }} :</td>
            <td class="text-center">
              {{ formatCurrency(+amount) }}
            </td>
          </tr>
        </table>
      </div>

      <Link :href="`user/subscription`" class="payment-cancel-btn">
        {{ trans('Cancel Payment') }}
      </Link>
    </div>
  </div>
</template>
