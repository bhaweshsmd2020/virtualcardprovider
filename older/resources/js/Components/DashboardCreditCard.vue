<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import sharedComposable from '@/Composables/sharedComposable'
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
const { formatCurrency} = sharedComposable()

const cardVariant = {
  basic: {
    bgColor: '#026f5e',
    color: 'text-dark'
  },
  pro: {
    bgColor: 'bg-gradient-to-r from-gray-950/50 to-red-900',
    color: 'text-dark'
  },
  gold: {
    bgColor: 'bg-gradient-to-r from-amber-700 to-yellow-600',
    color: 'text-dark'
  },
 
  premium: {
    bgColor: 'bg-gradient-to-r from-neutral-950 via-neutral-900 to-neutral-950',
    color: 'text-amber-400'
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

<template >
  <div 
    :class="[activeVariant.bgColor, classes]" style="background:#026f5e,color:#fff; -moz-transform: scale(1,1);
    -webkit-transform: scale(1,1);
    transform: scale(1,1);"
  >
    <div class="flex items-center justify-between ">
      <p class="text-white text-lg mt-10"><strong>{{creditCard.name_on_card}}</strong></p>
     <!-- <img src="/assets/images/logo.png" class="text-xl" alt="Copy Icon" style="width:80px;" /> -->
    </div>
    <div id="card-number" class="text-xl text-white font-semibold mt-2 flex" :class="activeVariant.color">
      <span v-if="showFullCard && fullCardNumber">{{ fullCardNumber }}</span>
      <!--<span v-else>**** **** **** {{ creditCard.last4 }}</span>
      <span v-if="showFullCard && fullCardNumber" @click="copyCardNumber" class="mt-2 ml-1 cursor-pointer">
        <img src="/assets/images/copy.png" class="text-xl" alt="Copy Icon" style="width: 15px;" />
      </span> -->     
    </div>
     <div class="mt-0 grid grid-cols-5 gap-8 font-bold text-white" :class="activeVariant.color">
      <div class="items-center text-lg col-span-3 font-bold">
          <span>Balance</span><br>               
          <span v-if="creditCard.available_limits">{{formatCurrency(Number(creditCard.available_limits))}}</span>
         </div> 
      <div class="mt-1 "> 
        <span class="text-xs text-semibold">{{ trans('EXP') }}</span>      
        <p class="font-bold text-xs">{{ creditCard.exp_month }}/{{ creditCard.exp_year }}</p>
      </div>
      <div class="mt-1 text-white">
        <span class="text-xs text-semibold">
         {{ trans('CVV') }} </span>  <span v-if="showFullCvv && fullCardCvv">{{ fullCardCvv }}</span>
          <p v-else>***</p>             
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
