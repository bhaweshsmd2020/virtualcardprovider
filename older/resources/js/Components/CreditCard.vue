<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import toast from '@/Composables/toastComposable'

const props = defineProps({
  creditCard: Object,
  revealBtn: {
    type: Boolean,
    default: false
  },
  stripe_pk_key: String,
  classes: {
    type: String,
    default: 'max-w-[28rem]'
  }
})

const cardVariant = {
  basic: {
    bgColor: 'bg-neutral-950/70',
    color: 'text-dark'
  },
  pro: {
    bgColor: 'bg-gradient-to-r',
    color: 'text-dark'
  },
  gold: {
    bgColor: 'bg-gradient-to-r',
    color: 'text-dark'
  },
  silver: {
    bgColor: 'bg-gradient-to-silver',
    color: 'text-white'
  },
  premium: {
    bgColor: 'bg-gradient-to-premium',
    color: 'tex-white'
  }
}

const activeVariant = computed(() => {
  return (
    cardVariant[
      props.creditCard?.virtual_card?.card_variant ||
      props.creditCard?.card?.card_variant ||
      'basic'
    ] || cardVariant['basic']
  )
})

let stripe
const elements = ref(null)
onMounted(() => {
  if (props.stripe_pk_key && props.revealBtn) {
    stripe = Stripe(props.stripe_pk_key || '')
    elements.value = stripe.elements()
  }
})

const cardId = ref(props.creditCard.card)
const fullCardNumber = ref(null)
const fullCardCvv = ref(null)
const showFullCard = ref(false)
const showFullCvv = ref(false)

const handleClick = async () => {
  try {
    if (showFullCard.value) {
      showFullCard.value = false
      showFullCvv.value = false
      return
    }

    const response = await axios.get(`/user/credit-card/${props.creditCard.id}/full-details`)
    fullCardNumber.value = response.data.full_card_number.replace(/\d{4}(?=\d)/g, '$& ');
    fullCardCvv.value = response.data.card_cvv

    showFullCard.value = true
    showFullCvv.value = true

    const cardNumberElement = elements.value.create('issuingCardNumberDisplay', {
      issuingCard: cardId.value,
      nonce: nonce.value,
      ephemeralKeySecret: ephemeralKeySecret.value,
      style: {
        base: {
          color: '#fff',
          fontSize: '24px',
          fontWeight: 600
        }
      }
    })
    cardNumberElement.mount('#card-number')
    
  } catch (error) {
    console.error('Error fetching card details:', error)
  }
}

// Function to copy the full card number to clipboard
const copyCardNumber = () => {
  if (fullCardNumber.value) {
    navigator.clipboard.writeText(fullCardNumber.value).then(() => {
      toast.success("Card number copied to clipboard!");
    })
  }
}
</script>

<template>
 <!-- <div
    class="relative h-64 rounded-xl p-6 shadow-lg backdrop-blur-md lg:col-span-4"
    :class="[activeVariant.bgColor, classes]" style="background: url(/assets/images/cardback.png) center bottom / 100% no-repeat rgb(255, 255, 255); -moz-transform: scale(1,1);
    -webkit-transform: scale(1,1);
    transform: scale(1,1);"
  > -->

    <div
    class="relative h-64 rounded-xl p-6 shadow-lg backdrop-blur-md lg:col-span-4"
    :class="[activeVariant.bgColor, classes]" 
  >
    <div class="flex items-center justify-between">
      <p class="text-white text-xl"><strong>Prepaid Virtual Card</strong></p>
      <img src="/assets/images/logo.png" class="text-xl" alt="Copy Icon" style="width: 90px;" />
    </div>

    <div id="card-number" class="text-2xl font-semibold mt-3 flex" :class="activeVariant.color">
      <span v-if="showFullCard && fullCardNumber">{{ fullCardNumber }}</span>
      <span v-else>**** **** **** {{ creditCard.last4 }}</span>
      <span v-if="showFullCard && fullCardNumber" @click="copyCardNumber" class="mt-2 ml-1 cursor-pointer">
        <img src="/assets/images/copy.png" class="text-xl" alt="Copy Icon" style="width: 15px;" />
      </span>
      <span v-if="revealBtn" class="mt-2 ml-1 cursor-pointer" @click="handleClick">
        <Icon class="text-xl" icon="fe:eye" />
      </span>
    </div>  

    <div class="mt-10 grid grid-cols-5 gap-2" :class="activeVariant.color">
      <div class="col-span-2">
        <p class="text-sm font-semibold lg:text-xs xl:text-sm">{{ trans('Cardholder name') }}</p>
        <p class="mt-2 text-xs">{{ creditCard.name_on_card }}</p>
      </div>
      <div>
        <p class="whitespace-nowrap text-sm font-semibold lg:text-xs xl:text-sm">
          {{ trans('Expiry date') }}
        </p>
        <p class="mt-2 text-xs">{{ creditCard.exp_month }}/{{ creditCard.exp_year }}</p>
      </div>
      <div>
        <p class="mb-2 text-center text-sm font-semibold lg:text-xs xl:text-sm">
          {{ trans('CVC') }}
        </p>
        
        <div id="cvc-number" class="ml-6">
          <span v-if="showFullCvv && fullCardCvv">{{ fullCardCvv }}</span>
          <span v-else>***</span>
        </div>
      </div>
      <div class="flex items-center justify-end">
        <Icon class="text-7xl" icon="bx:bxl-visa" :class="activeVariant.color" />
      </div>
    </div>
  </div>
</template>

<style scoped>
.IssuingDisplayElement {
  font-size: large;
  color: white;
}
</style>
