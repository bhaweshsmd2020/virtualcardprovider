<script setup>
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import UserLayout from '@/Layouts/User/UserLayout.vue'
import { ref, computed } from 'vue'
import toast from '@/Composables/toastComposable'
import { router } from '@inertiajs/vue3'
import sharedComposable from '@/Composables/sharedComposable'

defineOptions({ layout: UserLayout })
const form = ref({
  currency: '',
  type: 'balance',
  amount: 50,
  pay_type: '',
})
const props = defineProps(['gateways', 'bank_gateway'])

//credits
const { formatCurrency, authUser } = sharedComposable()
const activeGateway = ref(props.gateways[0]?.id || 0)
const convertedAmount = computed(() => {
  return form.value.amount
})
const phone = ref(null)
const isProcessing = ref(false)
const manualPayment = ref({
  image: null,
  comment: ''
})
const findGateway = computed(() => {
  return props.gateways.find((gateway) => gateway.id === activeGateway.value)
})
const submit = (gateway_id) => {
  if (findGateway.value.min_amount > convertedAmount.value) {
    toast.danger(
      'The minimum transaction amount is ' + formatCurrency(findGateway.value.min_amount)
    )
    return
  }
  if (findGateway.value.max_amount != -1) {
    if (findGateway.value.max_amount < convertedAmount.value) {
      toast.danger(
        'The maximum transaction amount is ' + formatCurrency(findGateway.value.max_amount)
      )
      return
    }
  }
  isProcessing.value = true
  form.value.pay_type = selectedPaymentMethod.value;
  router.post(
    route('user.top-up.store'),
    {
      gateway_id: gateway_id,
      total: convertedAmount.value,
      requested_amount: form.value.amount,
      manualPayment: findGateway.value.is_auto == 0 ? manualPayment.value : null,
      rate: 1,
      phone: findGateway.value.phone_required == 1 ? phone.value : null,
      pay_type: form.value.pay_type,      
      image: form.value.image,
      comment: form.value.comment
    },
    {
      onFinish: () => {
        isProcessing.value = false
      }
    }
  )
}

const selectedPaymentMethod = ref('card')

const showBankDetailsModal = ref(false);

const activeBankTab = ref('domestic');

const openBankDetailsModal = () => { showBankDetailsModal.value = true; };
const closeBankDetailsModal = () => { showBankDetailsModal.value = false; };

const selectBankTab = (tab) => { activeBankTab.value = tab; };

const modalContainer = ref(null);
const closeModalOnOutsideClick = (event) => {
  if (event.target.classList.contains('modal-overlay')) {
    closeBankDetailsModal();
  }
};

const copyToClipboard = (text) => {
  navigator.clipboard.writeText(text).then(() => {
    toast.success("Copied to clipboard!");
  }).catch(() => {
    toast.danger("Failed to copy!");
  });
};
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader title="Topup Balance" />

    <div class="mx-auto max-w-lg space-y-5 grid grid-cols-2 gap-2">
      <button 
        class="relative rounded border p-3"
        :class="{ 'bg-primary-600 text-white': selectedPaymentMethod === 'card' }"
        @click="selectedPaymentMethod = 'card'"
      >         
        <i v-if="selectedPaymentMethod === 'card'" class="bx bx-check absolute right-[-10px] top-[-10px] rounded-full bg-primary-600 p-0.5 text-white"></i>
        <span class="text-lg font-bold">{{ trans('Credit/Debit Card') }}</span>
      </button>

      <button 
        class="relative rounded border p-3" style="margin-top: 0px;"
        :class="{ 'bg-primary-600 text-white': selectedPaymentMethod === 'bank' }"
        @click="selectedPaymentMethod = 'bank'"
      >         
        <i v-if="selectedPaymentMethod === 'bank'" class="bx bx-check absolute right-[-10px] top-[-10px] rounded-full bg-primary-600 p-0.5 text-white"></i>
        <span class="text-lg font-bold">{{ trans('Bank Transfer') }}</span>
      </button>
    </div>

    <div class="mx-auto max-w-lg space-y-5" v-if="selectedPaymentMethod === 'card'">
      <p class="text-md font-semibold mt-5">{{ trans('Enter credit amount') }}</p>

      <input
        class="input"
        step="5"
        type="number"
        v-model.number="form.amount"
        placeholder="Enter Credit amount" style="margin-top: 5px;"
      />
      <div class="flex items-center justify-between">
        <p class="label dark:text-gray-200">
          {{ trans('Total:') }}
        </p>
        <p class="text-sm dark:text-gray-200">{{ formatCurrency(form.amount) }}</p>
      </div>
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
          <template v-else>{{ trans('Not charged') }}</template>
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
    
    <div class="mx-auto max-w-lg space-y-5" v-if="selectedPaymentMethod === 'bank'">
      <p class="text-md font-semibold mt-5">{{ trans('Enter credit amount') }}</p>

      <input
        class="input"
        step="5"
        type="number"
        v-model.number="form.amount"
        placeholder="Enter Credit amount" style="margin-top: 5px;"
      />
      <div class="flex items-center justify-between">
        <p class="label dark:text-gray-200">
          {{ trans('Sub Total:') }}
        </p>
        <p class="text-sm dark:text-gray-200">{{ formatCurrency(form.amount) }}</p>
      </div>
      <div class="flex items-center justify-between" style="margin-top: 5px;">
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
          <template v-else>{{ trans('Not charged') }}</template>
        </p>
      </div>
      <div class="flex items-center justify-between" style="margin-top: 5px;">
        <p class="label dark:text-gray-200">
          Gateway Charge:
        </p>
        <p class="text-sm dark:text-gray-200">{{ formatCurrency(bank_gateway.charge) }}</p>
      </div>
      <div class="flex items-center justify-between" style="margin-top: 5px;">
        <p class="label dark:text-gray-200">
          Total:
        </p>
        <p class="text-sm dark:text-gray-200">
          {{
            formatCurrency(
              +form.amount +
              +bank_gateway.charge +
              (+form.amount * (+authUser.plan_data.transaction_fee.value || 0) / 100)
            )
          }}
        </p>
      </div>
      
      <div class="space-y-2 text-xs mb-3" style="margin-top: 0px;">              
        <p class="text-sm dark:text-gray-200">
          <p class="text-md font-semibold mt-5">{{ trans('Payment Instruction: ') }}</p>
          <strong>{{ bank_gateway.comment }}</strong>
          <a href="#" class="badge badge-primary mt-2 ml-2" @click="openBankDetailsModal">
            View Bank Details
          </a>
        </p>
      </div>
        
      <template v-for="gateway in gateways" :key="gateway.id">
        <div v-show="activeGateway === gateway.id" :id="'gateway-form' + gateway.id" style="margin-top: 10px;">
          <form method="post" @submit.prevent="submit(gateway.id)" enctype="multipart/form-data">
            <label class="label mb-1">{{ trans('Submit your payment proof') }}</label>
            <input type="file" @input="(e) => { form.image = e.target.files[0] }" class="input" accept="image/*" required />
            <p class="mb-2"><small><span class="text-warning">*</span>A proof of payment can be a receipt (either a scan, a photo or a PDF) or a screenshot from your online bank</small></p>

            <label class="label mb-1">{{ trans('Message') }}</label>
            <textarea v-model.comment="form.comment" class="textarea" required placeholder="Message" maxlength="500"></textarea>

            <button :disabled="isProcessing" type="submit" class="btn btn-primary mt-4 w-full py-2 text-lg">
              {{ trans('Request Now') }}
            </button>
          </form>
        </div>
      </template>
    </div>

    <!-- Bank Details Modal -->
    <div v-if="showBankDetailsModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 modal-overlay" @click="closeModalOnOutsideClick" style="z-index: 99; margin-top: 0px;">
      <div class="bg-white rounded-lg shadow-lg max-w-[100%] p-6 relative">
        <button @click="closeBankDetailsModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-900">✖</button>
        <div class="flex space-x-2 mt-4">
          <label class="flex items-center space-x-2 cursor-pointer border py-3 w-1/2 whitespace-nowrap">
            <input type="radio" v-model="activeBankTab" value="domestic" class="hidden" />
            <span class="w-4 h-4 border-2 border-black rounded-full flex items-center justify-center">
              <span v-if="activeBankTab === 'domestic'" class="w-2 h-2 bg-black rounded-full"></span>
            </span>
            <span class="text-lg font-medium">Domestic wire</span>
          </label>

          <label class="flex items-center space-x-2 cursor-pointer border py-3 w-1/2 whitespace-nowrap">
            <input type="radio" v-model="activeBankTab" value="international" class="hidden" />
            <span class="w-4 h-4 border-2 border-black rounded-full flex items-center justify-center">
              <span v-if="activeBankTab === 'international'" class="w-2 h-2 bg-black rounded-full"></span>
            </span>
            <span class="text-lg font-medium">International wire</span>
          </label>
        </div>

        <!-- Domestic Wire Details -->
        <div v-if="activeBankTab === 'domestic'" class="mt-4 border p-3 bg-gray-100">
          <p class="mb-3"><small>U.S. Bank typically transmits bank-to-bank wire transfers within the same day, <br>and funds are usually delivered to the recipient within 24 hours</small></p>
          <p class="text-sm font-semibold">Reference</p>
          <div class="pt-1">
            <span>Digital Services</span>
          </div>

          <p class="text-sm font-semibold mt-3">Beneficiary Name</p>
          <div class="pt-1">
            <span>Fibantech LLC</span>
          </div>

          <p class="text-sm font-semibold mt-3">Beneficiary Address</p>
          <div class="pt-1">
            <span>4500 Satellite Boulevard, Suite 2130, Duluth, GA 30096 USA</span>
          </div>

          <p class="text-sm font-semibold mt-3">Account number</p>
          <div class="pt-1">
            <span>202304585835</span>
            <button @click="copyToClipboard('202304585835')" class="text-gray-600 hover:text-gray-900 pl-2">📋</button>
          </div>            

          <p class="text-sm font-semibold mt-3">Routing number</p>
          <div class="pt-1">
            <span>091311229</span>
            <button @click="copyToClipboard('091311229')" class="text-gray-600 hover:text-gray-900 pl-2">📋</button>
          </div>

          <p class="text-sm font-semibold mt-3">Bank Name</p>
          <div class="pt-1">
            <span>Choice Financial Group Bank</span>
          </div>

          <p class="text-sm font-semibold mt-3">Bank Address</p>
          <div class="pt-1">
            <span>501 23rd Avenue S Fargo, ND 58104, USA</span>
          </div>            
        </div>

        <!-- International Wire Details -->
        <div v-if="activeBankTab === 'international'" class="mt-4 border p-3 bg-gray-100">
          <p class="mb-3"><small>For U.S. Bank international wire transfers, funds typically take 1 to 3 <br> business days to be available in the recipient's account</small></p>
          <p class="text-sm font-semibold">Reference</p>
          <div class="pt-1">
            <span>Digital Services</span>
          </div>

          <p class="text-sm font-semibold mt-3">Beneficiary Name</p>
          <div class="pt-1">
            <span>Fibantech LLC</span>
          </div>

          <p class="text-sm font-semibold mt-3">Beneficiary Address</p>
          <div class="pt-1">
            <span>4500 Satellite, Boulevard, Suite 2130 ,Duluth, GA 30096, USA</span>
          </div>

          <p class="text-sm font-semibold mt-3">IBAN / Account Number</p>
          <div class="pt-1">
            <span>202304585835</span>
            <button @click="copyToClipboard('202304585835')" class="text-gray-600 hover:text-gray-900 pl-2">📋</button>
          </div>  
          
          <p class="text-sm font-semibold mt-3">SWIFT / BIC Code</p>
          <div class="pt-1">
            <span>CHFGUS44021</span>
            <button @click="copyToClipboard('CHFGUS44021')" class="text-gray-600 hover:text-gray-900 pl-2">📋</button>
          </div>  

          <p class="text-sm font-semibold mt-3">ABA Routing number</p>
          <div class="pt-1">
            <span>091311229</span>
            <button @click="copyToClipboard('091311229')" class="text-gray-600 hover:text-gray-900 pl-2">📋</button>
          </div>

          <p class="text-sm font-semibold mt-3">Bank Name</p>
          <div class="pt-1">
            <span>Choice Financial Group Bank</span>
          </div>

          <p class="text-sm font-semibold mt-3">Bank Address</p>
          <div class="pt-1">
            <span>4501 23rd Avenue S Fargo, ND 58104, USA</span>
          </div>            
        </div>
      </div>
    </div>

  </main>
</template>
<style>
.text-warning {
  color: #ffc107 !important;
  font-weight: bold;
}

.bg-primary-600, .badge-primary {
    background-color: #026f5e !important;
}
</style>