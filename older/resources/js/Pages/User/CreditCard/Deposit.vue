<script setup>
import { ref, computed } from 'vue';
import toast from '@/Composables/toastComposable';
import { router } from '@inertiajs/vue3';
import sharedComposable from '@/Composables/sharedComposable';
import Modal from '@/Components/Admin/Modal.vue';
import { useModalStore } from '@/Store/modalStore';
import Swal from 'sweetalert2'

const form = ref({
  currency: '',
  type: 'deposit',
  amount: 100,
});

const modal = useModalStore();
const props = defineProps(['gateways', 'credit_card_id', 'virtual_card', 'prepaidcard', 'card_details']);

const { formatCurrency, authUser, calculatePercent } = sharedComposable();
const activeGateway = ref(props.gateways[0]?.id || 0);
const phone = ref(null);
const isProcessing = ref(false);
const isWallet = ref(false);
const manualPayment = ref({
  image: null,
  comment: '',
});

const findGateway = computed(() => {
  return props.gateways.find((gateway) => gateway.id === activeGateway.value);
});

const topupLimit = computed(() => {
  return props.prepaidcard.topup_limit
})

const spandingLimit = computed(() => {
  return props.prepaidcard.spending_limit
})

const limitFee = computed(() => {
  return props.virtual_card.increase_limit_fee
})

const totalAmount = computed(() => {
  const depositAmount = (form.value.amount * limitFee.value) / 100;
  return form.value.amount + depositAmount;
});

const totalSpend = computed(() => {
  return form.value.amount + props.card_details.spending_restrictions.amount;
});

const submit = () => {

  if (authUser.value.balance < totalAmount.value) {
    modal.close();
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

  if (topupLimit.value < form.value.amount) {
    modal.close();
    function showAlert() {
      Swal.fire({
        icon: "warning",
        title: `<h2 style='font-size: 20px;'>Maximum card topup is ${formatCurrency(+topupLimit.value)}</h2>`,
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

  if (spandingLimit.value < totalSpend.value) {
    modal.close();
    function showAlert() {
      Swal.fire({
        icon: "warning",
        title: `<h2 style='font-size: 20px;'>Total card spending limit is ${formatCurrency(+spandingLimit.value)}</h2>`,
        confirmButtonText: "Request to increase limit",
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
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = route('user.supports.index');
        }
      });
    }
    showAlert();
    return
  }  

  isProcessing.value = true;
  router.post(
    route('user.credit-card.increase-limit'),
    {
      total: totalAmount.value,
      limit_fee: limitFee.value,
      sub_total: +form.value.amount,
      credit_card_id: props.credit_card_id,
    },
    {
      onFinish: () => {
        isProcessing.value = false;
        modal.close();
      },
    }
  );
};
</script>

<template>
  <Modal :header-state="true" header-title="Card Topup" :state="modal.states['deposit']">
    <div class="mx-auto mt-6 space-y-5 p-3">
      <input class="input mb-2" v-model.number="form.amount" name="amount">

      <span class="text-sm font-semibold">
        {{ trans('Sub Total:') }} {{ formatCurrency(form.amount) }}
      </span>

      <div class="my-4 grid grid-cols-4 gap-2">
        <button class="relative rounded border dark:border-gray-600">
          <i class="bx bx-check absolute right-[-10px] top-[-10px] rounded-full bg-primary-600 p-0.5 text-white"></i>
          <span class="text-lg font-bold">{{ trans('Account') }}</span>
        </button>
      </div>
      <template v-for="gateway in gateways" :key="gateway.id">
        <div v-show="activeGateway === gateway.id" :id="'gateway-form' + gateway.id">
          <div v-if="isProcessing" class="flex justify-center my-4">
            <svg class="animate-spin h-6 w-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
          </div>

          <form method="post" @submit.prevent="submit" enctype="multipart/form-data">
            <div class="space-y-2 text-xs">
              <div class="flex items-center justify-between">
                <span class="dark:text-gray-200">
                  {{ trans('Increase Limit Fee: ') }}
                </span>
                <span class="dark:text-gray-200">
                  <template v-if="limitFee > 0">{{ limitFee }} %</template>
                  <template v-else>{{ trans('Not charged') }}</template>
                </span>
              </div>
              <div class="flex items-center justify-between">
                <span class="dark:text-gray-200">
                  {{ trans('Total: ') }}
                </span>
                <span class="dark:text-gray-200">{{ formatCurrency(totalAmount) }}</span>
              </div>
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
                  required
                  v-model="phone"
                />
              </div>
            </template>
            <template v-if="gateway.is_auto == 0 && findGateway?.is_crypto != 1">
              <div class="mt-2">
                <label class="label mb-1">{{ trans('Submit your payment proof') }}</label>
                <input
                  @input="(e) => { manualPayment.image = e.target.files[0] }"
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

            <button :disabled="isProcessing" type="submit" class="btn btn-primary mt-4 w-full py-2 text-lg">
              {{ trans('Pay Now') }}
            </button>
          </form>
        </div>
      </template>
    </div>
  </Modal>
</template>
