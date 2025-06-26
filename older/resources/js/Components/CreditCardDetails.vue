<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import sharedComposable from '@/Composables/sharedComposable'
import { modal } from '@/Composables/actionModalComposable'
import moment from 'moment'
import axios from 'axios'
import CreditCard from '@/Components/CreditCard.vue'
import toast from '@/Composables/toastComposable'
import Swal from 'sweetalert2'

const props = defineProps({
  creditCard: Object,
  canEdit: {
    type: Boolean,
    default: false
  },
  revealBtn: {
    type: Boolean,
    default: false
  },
  stripe_pk_key: String,
  cardDetails: Object,
  otherCity: String,
  otherState: String
})

const { formatCurrency, authUser } = sharedComposable()
const spendingForm = useForm({
  amount: props.creditCard.spending_limits
})

const form = useForm({
  status: props.creditCard.status
})

const availableBalance = ref(props.creditCard.available_limits)
const loading = ref(false)  // Track loading state for balance refresh

// Modal state for update card form
const showModal = ref(false);
const cardForm = ref({
  card_id: props.creditCard.id,
  name_on_card: '',
});

// Open modal
const openModal = () => {
  cardForm.value.name_on_card = props.creditCard.name_on_card;
  showModal.value = true;
}

// Close modal
const closeModal = () => {
  showModal.value = false;
}

// Handle form submission
const handleSubmit = async () => {
  try {
    // Send the form data to the backend
    await axios.post(route('admin.credit-card.update'), cardForm.value);

    // Close the modal after successful submission
    closeModal();
    toast.success("Card updated successfully!");
  } catch (error) {
    // Handle error (e.g., validation errors or server errors)
    toast.error("There was an error updating the card.");
    console.error(error);
  }
}

const updateStatus = () => {
  const options = {
    method: 'patch',
    data: { status: form.status },
    options: {
      confirm_text: 'Are you sure want to do this action ?',
      message: 'Change card status',
      accept_btn_text: 'Yes, Confirm',
      reject_btn_text: 'No, Cancel'
    }
  }

  if (authUser.value.role === 'user') {
    const status = props.creditCard.status;
    modal.init(route('user.credit-card.update-status', {
      id: props.creditCard.id,
      status: status
    }), options);
  }
  
  if (authUser.value.role === 'admin') {
    const status = props.creditCard.status;
    modal.init(route('admin.credit-card.update-status', {
      id: props.creditCard.id,
      status: status
    }), options);
  }
}

const terminateCard = () => {
  const options = {
    method: 'patch',
    data: { status: form.status },
    options: {
      confirm_text: 'Are you sure want to do this action ?',
      message: 'Change card status',
      accept_btn_text: 'Yes, Confirm',
      reject_btn_text: 'No, Cancel'
    }
  }

  if (authUser.value.role === 'admin') {
    modal.init(route('admin.credit-card.terminate', {
      id: props.creditCard.id,
    }), options);
  }
}

const updateSpending = () => {
  if (props.canEdit) {
    spendingForm.patch(route('admin.credit-card.update-spending', props.creditCard.id))
  }
}

// Refresh balance function with loading state
const refreshBalance = async () => {
  loading.value = true;  // Set loading to true when balance update starts
  
  try {
    if (authUser.value.role === 'user') {
      const response = await axios.get(route('user.credit-card.balance', { id: props.creditCard.id }));
      availableBalance.value = response.data.updated_balance;
    }else{
      const response = await axios.get(route('admin.credit-card.balance', { id: props.creditCard.id }));
      availableBalance.value = response.data.updated_balance;
    }    

    // Show success alert
    Swal.fire({
      icon: "success",
      title: `<h2 style='font-size: 20px;'>Balance updated successfully</h2>`,
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

  } catch (error) {
    toast.error("There was an error updating the balance.");
    console.error(error);
  } finally {
    loading.value = false;  // Set loading to false after the request is complete
  }
}
</script>

<template>
  <div class="card-body card space-y-12">
    <div class="grid grid-cols-1 gap-10 lg:grid-cols-8 2xl:grid-cols-12">
      <CreditCard :stripe_pk_key="stripe_pk_key" :revealBtn="revealBtn" :credit-card="creditCard" :card-details="cardDetails" :other-state="otherState" :other-city="otherCity" />
      <!-- spend -->
      <div class="lg:col-span-4 2xl:col-span-3">
        <div class="mb-10 flex items-center justify-between">
          <div>
            <p class="text-sm font-semibold text-slate-500">{{ trans('Available Balance') }}</p>
            <p class="text-3xl font-semibold">
              {{ availableBalance }}
              <span class="text-sm uppercase">{{ creditCard.currency }} </span>
              
              <span v-if="loading" class="text-xl ml-2">
                <i class="bx bx-loader bx-spin"></i>
              </span>
              <i v-else class="bx bx-refresh text-xl ml-2 cursor-pointer" @click="refreshBalance"></i>
            </p>
          </div>
        </div>
        <div class="mb-10 flex items-start justify-between">
          <div>
            <p class="text-sm font-semibold text-slate-500">{{ trans('Total Balance') }}</p>
            <p class="text-3xl font-semibold">
              {{ formatCurrency(+props.cardDetails.spending_restrictions.amount || 0).slice(1) }}
              <span class="text-sm uppercase">{{ creditCard.currency }} </span>
            </p>
          </div>
        </div>
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-semibold text-slate-500">{{ trans('Spending Interval') }}</p>
            <p class="text-1xl font-semibold">
              {{ props.cardDetails.spending_restrictions.interval }}
            </p>
          </div>
        </div>
      </div>
      <!-- details -->
      <div class="lg:col-span-8 2xl:col-span-5">
        <div class="grid grid-cols-3">
          <div>
            <p class="text-sm font-semibold">{{ trans('Card Type') }}</p>
            <p class="text-xs text-slate-500">{{ trans('Virtual') }}</p>
          </div>
          <div>
            <p class="text-sm font-semibold">{{ trans('Status') }}</p>
            <p class="badge capitalize" :class="creditCard.status === 'active' ? 'badge-success' : 'badge-danger'">
              {{ creditCard.status }}
            </p>
          </div>
          <button @click="updateStatus" type="button" class="btn btn-primary">
            {{ creditCard.status === 'active' ? trans('Deactivate') : trans('Activate') }}
            {{ trans('Card') }}
          </button>
        </div>
        <div class="my-7 grid grid-cols-3">
          <div>
            <p class="text-sm font-semibold">{{ trans('Card Currency') }}</p>
            <p class="text-xs uppercase text-slate-500">{{ creditCard.currency }}</p>
          </div>
          <div>
            <p class="text-sm font-semibold">{{ trans('Card Created') }}</p>
            <p class="text-xs text-slate-500">
              {{ moment(creditCard.created_at).format('MMM DD, YYYY') }}
            </p>
          </div>
          <div v-if="authUser.role == 'admin'">
            <button class="btn btn-warning" @click="openModal">
              <span>{{ trans('Update Card') }}</span>
            </button>
          </div>
        </div>
        <div class="grid grid-cols-3">
          <div>
            <p class="text-sm font-semibold">{{ trans('Country') }}</p>
            <p class="text-xs text-slate-400">
              {{ creditCard.user?.country?.name }}
            </p>
          </div>
          <div>
            <p class="text-sm font-semibold">{{ trans('State') }}</p>
            <p class="text-xs text-slate-400">
              {{
                creditCard.user?.state_id === null
                  ? 'N/A'
                  : creditCard.user?.state_id === 1000
                  ? (otherState?.name || 'N/A')
                  : (creditCard.user?.state?.name || 'N/A')
              }}
            </p>
          </div> 
          <div v-if="authUser.role == 'admin'">
            <button @click="terminateCard" type="button" class="btn btn-danger">
              {{ trans('Terminate Card') }}
            </button>
          </div>
        </div>
        <div class="mt-7 grid grid-cols-3">
          <div>
            <p class="text-sm font-semibold">{{ trans('City') }} / {{ trans('Postal Code') }}</p>
            <p class="text-xs text-slate-400">
              {{
                creditCard.user?.city_id === null
                  ? 'N/A'
                  : creditCard.user?.city_id === 1000
                  ? (otherCity?.name || 'N/A')
                  : (creditCard.user?.city?.name || 'N/A')
              }} - {{ creditCard.user?.city?.postal_code }}
            </p>
          </div>        
          <div>
            <p class="text-sm font-semibold">{{ trans('Card Postal Code') }}</p>
            <p class="text-xs text-slate-400">
              30096
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Update Card</h2>
        <button @click="closeModal" class="close-btn">&times;</button>
      </div>
      <form @submit.prevent="handleSubmit">
        <div class="form-group">
          <label for="name_on_card">Name on Card:</label>
          <input v-model="cardForm.name_on_card" id="name_on_card" type="text" placeholder="Name on Card" required />
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
      </form>
    </div>
  </div>
</template>

<style scoped>
/* Add styles for the modal and other components here */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 99;
}

.modal-content {
  background: white;
  padding: 20px;
  border-radius: 5px;
  width: 400px;
  height: 250px;
  position: relative;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #ccc;
  padding: 0px 0px 10px;
}

.modal-title {
  font-size: 1.5rem;
  font-weight: bold;
}

.close-btn {
  background: none;
  border: none;
  font-size: 25px;
  font-weight: bold;
  cursor: pointer;
}

.form-group {
  margin: 15px 0px;
}

input {
  width: 100%;
  padding: 10px;
  margin-top: 5px;
  border: 1px solid #ccc;
  border-radius: 5px;
}
</style>
