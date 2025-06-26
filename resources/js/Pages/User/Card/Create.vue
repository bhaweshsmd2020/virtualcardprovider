<script setup>
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import UserLayout from '@/Layouts/User/UserLayout.vue'
import VirtualCard from '@/Components/User/VirtualCard.vue'
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import sharedComposable from '@/Composables/sharedComposable'
import toast from '@/Composables/toastComposable'
import Swal from 'sweetalert2'

defineOptions({ layout: UserLayout })

const props = defineProps(['gateways', 'cards', 'creditcards'])

const { formatCurrency, authUser } = sharedComposable()
const activeGateway = ref(props.gateways[0]?.id || 0)
const phone = ref(null)
const isProcessing = ref(false)
const manualPayment = ref({
  image: null,
  comment: ''
})
const selectedPaymentMethod = ref('account')

const findGateway = computed(() => {
  return props.gateways.find((gateway) => gateway.id === activeGateway.value)
})
const form = ref({
  type: 'card',
  amount: 0,
  card_id: 1,
  name_on_card: '',
  pay_type: '',
  phone: '',
  image: null,
  comment: ''
})

const activeTab = ref('card')
const selectedCard = ref({})

const selectCard = (card) => {
  selectedCard.value = card;
  form.value.amount = card.min_deposit;

  const matchedCards = props.creditcards.filter((creditCard) => creditCard.card_id === card.id);

  if (matchedCards.length >= card.card_limit) {
    function showAlert() {
      Swal.fire({
        icon: "warning",
        title: `<h2 style='font-size: 20px;'>Maximum card limit of ${card.card_limit} reached for this card type. No more cards can be created.</h2>`,
        confirmButtonText: "Okay",
        allowOutsideClick: false,
        customClass: {
          popup: "custom-swal-popup",
        },
        didOpen: (toast) => {
          const closeButton = document.createElement("button")
          closeButton.innerHTML = "&times;"
          closeButton.classList.add("swal-close-btn")
          closeButton.onclick = () => Swal.close()

          toast.appendChild(closeButton)
        },
      });
    }
    showAlert();
    return;
  }

  activeTab.value = 'order';
};

const totalAmount = computed(() => {
  if (authUser.value.plan_data) {
    const depositAmount = (form.value.amount * selectedCard.value.card_fee)/100
    return form.value.amount + depositAmount
  }
})

const goToPayment = () => {
  if (selectedCard.value.min_deposit > form.value.amount) {
    function showAlert() {
      Swal.fire({
        icon: "warning",
        title: `<h2 style='font-size: 20px;'>Minimum card deposit is ${formatCurrency(+selectedCard.value.min_deposit)}</h2>`,
        confirmButtonText: "Okay",
        allowOutsideClick: false,
        customClass: {
          popup: "custom-swal-popup",
        },
        didOpen: (toast) => {
          const closeButton = document.createElement("button")
          closeButton.innerHTML = "&times;"
          closeButton.classList.add("swal-close-btn")
          closeButton.onclick = () => Swal.close()

          toast.appendChild(closeButton)
        },
      });
    }
    showAlert();
    return
  }

  if (selectedCard.value.max_deposit < form.value.amount) {
    function showAlert() {
      Swal.fire({
        icon: "warning",
        title: `<h2 style='font-size: 20px;'>Maximum card deposit is ${formatCurrency(+selectedCard.value.max_deposit)}</h2>`,
        confirmButtonText: "Okay",
        allowOutsideClick: false,
        customClass: {
          popup: "custom-swal-popup",
        },
        didOpen: (toast) => {
          const closeButton = document.createElement("button")
          closeButton.innerHTML = "&times;"
          closeButton.classList.add("swal-close-btn")
          closeButton.onclick = () => Swal.close()

          toast.appendChild(closeButton)
        },
      });
    }
    showAlert();
    return
  }
  
  if (authUser.value.balance < form.value.amount) {
    function showAlert() {
      Swal.fire({
        icon: "warning",
        title: `<h2 style='font-size: 20px;'>Insufficient Balance</h2>`,
        confirmButtonText: "Okay",
        allowOutsideClick: false,
        customClass: {
          popup: "custom-swal-popup",
        },
        didOpen: (toast) => {
          const closeButton = document.createElement("button")
          closeButton.innerHTML = "&times;"
          closeButton.classList.add("swal-close-btn")
          closeButton.onclick = () => Swal.close()

          toast.appendChild(closeButton)
        },
      });
    }
    showAlert();
    return
  }
  activeTab.value = 'payment'
}

const submit = () => {
  form.value.pay_type = selectedPaymentMethod.value;

  if (selectedCard.value.min_deposit > form.value.amount) {
    function showAlert() {
      Swal.fire({
        icon: "warning",
        title: `<h2 style='font-size: 20px;'>Minimum card deposit is ${formatCurrency(+selectedCard.value.min_deposit)}</h2>`,
        confirmButtonText: "Okay",
        allowOutsideClick: false,
        customClass: {
          popup: "custom-swal-popup",
        },
        didOpen: (toast) => {
          const closeButton = document.createElement("button")
          closeButton.innerHTML = "&times;"
          closeButton.classList.add("swal-close-btn")
          closeButton.onclick = () => Swal.close()

          toast.appendChild(closeButton)
        },
      });
    }
    showAlert();
    return
  }

  if (selectedCard.value.max_deposit < form.value.amount) {
    function showAlert() {
      Swal.fire({
        icon: "warning",
        title: `<h2 style='font-size: 20px;'>Maximum card deposit is ${formatCurrency(+selectedCard.value.max_deposit)}</h2>`,
        confirmButtonText: "Okay",
        allowOutsideClick: false,
        customClass: {
          popup: "custom-swal-popup",
        },
        didOpen: (toast) => {
          const closeButton = document.createElement("button")
          closeButton.innerHTML = "&times;"
          closeButton.classList.add("swal-close-btn")
          closeButton.onclick = () => Swal.close()

          toast.appendChild(closeButton)
        },
      });
    }
    showAlert();
    return
  }

  if (authUser.value.balance < form.value.amount) {
    function showAlert() {
      Swal.fire({
        icon: "warning",
        title: `<h2 style='font-size: 20px;'>Insufficient Balance</h2>`,
        confirmButtonText: "Okay",
        allowOutsideClick: false,
        customClass: {
          popup: "custom-swal-popup",
        },
        didOpen: (toast) => {
          const closeButton = document.createElement("button")
          closeButton.innerHTML = "&times;"
          closeButton.classList.add("swal-close-btn")
          closeButton.onclick = () => Swal.close()

          toast.appendChild(closeButton)
        },
      });
    }
    showAlert();
    return
  }

  isProcessing.value = true
  router.post(
    route('user.card.pay'),
    {
      pay_type: form.value.pay_type,
      card_id: selectedCard.value.id,
      subtotal: form.value.amount,
      name_on_card: form.value.name_on_card,
      phone: form.value.phone,
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
    <PageHeader title="Create Card" />
    <div v-if="activeTab == 'card'" class="mx-auto max-w-5xl space-y-5">
      <h3 class="text-4xl font-bold">{{ trans('Choose your card') }}</h3>
      <template v-for="card in cards" :key="card.id">
        <VirtualCard :card="card" @select-card="selectCard(card)" />
      </template>
    </div>

    <!-- order -->
    <div v-if="activeTab == 'order'" class="mx-auto max-w-md">
      <!-- prepaid card -->
      <div
        class="mx-auto h-[17rem] rounded-lg border border-gray-100 bg-gradient-to-r from-neutral-100 to-neutral-300 dark:border-gray-600 dark:from-gray-700 dark:to-gray-800"
      >
        <div
          class="mt-7 h-14 bg-gradient-to-l from-neutral-600 to-neutral-400 dark:from-gray-700 dark:to-gray-900"
        ></div>
        <div class="p-4">
          <p class="text-lg font-semibold">{{ selectedCard.title }}</p>
          <div class="mb-8 mt-2 space-x-2 text-xs">
            <span class="rounded bg-gray-50 px-2 py-px dark:bg-gray-800">
              {{ selectedCard.category?.title }}
            </span>
          </div>
          <div class="space-y-1 text-sm text-gray-600 dark:text-gray-300">
            <div class="flex items-center justify-between">
              <p>{{ trans('Card fee') }}</p>
              <p>{{ selectedCard.card_fee }} %</p>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-7 space-y-6">
        <!-- input -->
        <div>
          <label class="label text-xs">
            {{ trans('Required card balance') }} 
            ( {{ trans('min:') }} {{ formatCurrency(selectedCard.min_deposit) }}, {{ trans('max:') }} {{ formatCurrency(selectedCard.max_deposit) }} )
          </label>
          <input type="text" v-model.number="form.amount" class="input" />
        </div>
        <div>
          <label class="label text-xs">
            {{ trans('Name on Card') }}
          </label>
          <input type="text" v-model.name_on_card="form.name_on_card" class="input" placeholder="Name on Card"/>
        </div>
        <div>
          <div class="card rounded-md bg-gray-100 p-4">
            <div class="my-1 flex items-center justify-between font-semibold">
              <p>{{ trans('Total Deposit') }}</p>
              <p>{{ formatCurrency(+totalAmount) }}</p>
            </div>
            <svg
              width="408"
              height="2"
              viewBox="0 0 408 2"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
              class="my-4 w-full"
            >
              <path d="M0 1H408" stroke="#838689" stroke-dasharray="2 6"></path>
            </svg>
            <div class="space-y-4 text-sm text-gray-400">
              <div class="flex items-center justify-between">
                <p>{{ trans('Card fee') }} {{ selectedCard.card_fee }}%</p>
                <p>+ {{ formatCurrency(totalAmount - form.amount) }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="flex items-start gap-x-2">
          <input id="terms" class="checkbox mt-1.5" type="checkbox" checked />
          <label for="terms" class="text-[15px] font-medium">
            {{ trans('By creating a new card, you agreeing to our') }}
            <a class="underline" target="_blank" href="/prepaid-card-privacy-policy">{{
              trans('Privacy Policy,')
            }}</a>
            {{ trans('and') }}
            <a class="underline" target="_blank" href="/prepaid-card-terms-condition">{{
              trans('Terms of Use.')
            }}</a>
          </label>
        </div>
        <button
          type="button"
          @click="goToPayment"
          class="btn w-full bg-secondary-950 py-3 text-secondary-50 hover:bg-secondary-800"
        >
          {{ trans('Continue') }}
        </button>
      </div>
    </div>

    <!-- payment -->
    <div class="mx-auto max-w-lg space-y-6" v-if="activeTab == 'payment'">
      <p class="text-lg">{{ trans('Method of payment') }}</p>

      <!-- Payment Method Selection -->
      <div class="my-4 grid grid-cols-2 gap-2">
        <button 
          class="relative rounded border p-3"
          :class="{ 'bg-primary-600 text-white': selectedPaymentMethod === 'account' }"
          @click="selectedPaymentMethod = 'account'"
        >         
          <i v-if="selectedPaymentMethod === 'account'" class="bx bx-check absolute right-[-10px] top-[-10px] rounded-full bg-primary-600 p-0.5 text-white"></i>
          <span class="text-lg font-bold">{{ trans('Account') }}</span>
        </button>

        <button 
          class="relative rounded border p-3"
          :class="{ 'bg-primary-600 text-white': selectedPaymentMethod === 'bank' }"
          @click="selectedPaymentMethod = 'bank'"
        >         
          <i v-if="selectedPaymentMethod === 'bank'" class="bx bx-check absolute right-[-10px] top-[-10px] rounded-full bg-primary-600 p-0.5 text-white"></i>
          <span class="text-lg font-bold">{{ trans('Bank Transfer') }}</span>
        </button>
      </div>

      <div class="space-y-2 text-xs">
        <div class="flex items-center justify-between">
          <span class="dark:text-gray-200">
            {{ trans('Deposit: ') }}
          </span>
          <span class="dark:text-gray-200">{{ formatCurrency(form.amount) }}</span>
        </div>
        <div class="flex items-center justify-between">
          <span class="dark:text-gray-200">
            {{ trans('Card Fee: ') }}
          </span>
          <span class="dark:text-gray-200">
            <template
              v-if="
                selectedCard.card_fee &&
                selectedCard.card_fee > 1
              "
            >
              {{ selectedCard.card_fee }} %
            </template>
            <template v-else>{{ trans('Not charged') }}</template>
          </span>
        </div>
        <div class="flex items-center justify-between">
          <span class="dark:text-gray-200">
            {{ trans('Total: ') }}
          </span>
          <span class="dark:text-gray-200">{{ formatCurrency(totalAmount) }}</span>
        </div>

        <h5 class="mt-4 font-semibold dark:text-gray-200">
          {{ trans('Payment Instruction: ') }}
        </h5>
      </div>

      <div v-if="selectedPaymentMethod === 'account'" style="margin-top: 10px;">
        <template v-for="gateway in gateways" :key="gateway.id">
          <div v-show="activeGateway === gateway.id" :id="'gateway-form' + gateway.id">
            <form method="post" @submit.prevent="submit(gateway.id)" enctype="multipart/form-data">
              <div class="space-y-2 text-xs">              
                <p class="text-sm dark:text-gray-200">{{ trans('Fibantech LLC') }}</p>
              </div>

              <button :disabled="isProcessing" type="submit" class="btn btn-primary mt-4 w-full py-2 text-lg">
                {{ trans('Pay Now') }}
              </button>
            </form>
          </div>
        </template>
      </div>

      <div v-if="selectedPaymentMethod === 'bank'" style="margin-top: 0px;">
        <div class="space-y-2 text-xs mb-3">              
          <p class="text-sm dark:text-gray-200">
            <strong>{{ trans('To upload payment proof after a bank transfer') }}</strong>
            <a href="#" class="badge badge-success mt-2 ml-2" @click="openBankDetailsModal">
              View Bank Details
            </a>
          </p>
        </div>
        
        <template v-for="gateway in gateways" :key="gateway.id">
          <div v-show="activeGateway === gateway.id" :id="'gateway-form' + gateway.id">
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
    </div>
  </main>
</template>
<style>
.text-warning {
  color: #ffc107 !important;
  font-weight: bold;
}

.bg-primary-600 {
    background-color: #026f5e !important;
}
</style>