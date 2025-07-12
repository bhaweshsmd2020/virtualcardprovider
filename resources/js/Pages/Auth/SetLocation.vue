<script setup>
import { ref, onMounted } from 'vue'
import { auth } from '@/firebase'
import { signInWithPhoneNumber } from 'firebase/auth'

const RECAPTCHA_SITE_KEY = '6LflO4ArAAAAALLrKRKI4YeH1DN45CmDfdPbL6qx'

const phone = ref('+918950291871')
const otp = ref('')
const otpSent = ref(false)
const confirmationResult = ref(null)
const loading = ref(false)
const verifying = ref(false)

const sendOtp = async () => {
  loading.value = true
  try {
    if (!window.grecaptcha || !window.grecaptcha.enterprise) {
      alert('⚠️ reCAPTCHA not loaded. Try reloading the page.')
      return
    }

    // Wait until grecaptcha is fully loaded
    await new Promise((resolve) => grecaptcha.enterprise.ready(resolve))

    // Execute reCAPTCHA to get a token
    const token = await grecaptcha.enterprise.execute(RECAPTCHA_SITE_KEY, {
      action: 'submit',
    })
    console.log('✅ reCAPTCHA token:', token)

    // Build a custom appVerifier using the token
    const appVerifier = {
      type: 'recaptcha',
      verify: () => Promise.resolve(token),
    }

    // Firebase phone auth
    confirmationResult.value = await signInWithPhoneNumber(auth, phone.value, appVerifier)
    otpSent.value = true
    console.log('✅ OTP sent')
  } catch (error) {
    console.error('❌ Error sending OTP:', error.message)
    alert('❌ Failed to send OTP: ' + error.message)
  } finally {
    loading.value = false
  }
}

const verifyOtp = async () => {
  verifying.value = true
  try {
    if (!confirmationResult.value) {
      alert('Please send OTP first.')
      return
    }

    const result = await confirmationResult.value.confirm(otp.value)
    console.log('✅ OTP verified, user:', result.user)
    alert('✅ OTP verified successfully')
  } catch (error) {
    console.error('❌ Invalid OTP:', error.message)
    alert('❌ Invalid OTP')
  } finally {
    verifying.value = false
  }
}
</script>

<template>
  <div style="max-width: 400px; margin: auto; padding-top: 50px;">
    <h3>📱 Firebase Phone Auth (Enterprise reCAPTCHA)</h3>

    <input v-model="phone" placeholder="Phone (e.g. +91...)" class="inputText" />
    <button :disabled="loading" @click="sendOtp" style="margin-top: 10px;">
      {{ loading ? 'Sending...' : 'Send OTP' }}
    </button>

    <div v-if="otpSent" style="margin-top: 20px;">
      <input v-model="otp" placeholder="Enter OTP" />
      <button :disabled="verifying" @click="verifyOtp" style="margin-top: 10px;">
        {{ verifying ? 'Verifying...' : 'Verify OTP' }}
      </button>
    </div>
  </div>
</template>

<style scoped>
.inputText {
  width: 100%;
  padding: 10px;
  font-size: 16px;
  margin-top: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
}
button {
  padding: 10px;
  font-size: 16px;
  width: 100%;
  background-color: #007bff;
  color: white;
  border: none;
  margin-top: 10px;
  border-radius: 4px;
  cursor: pointer;
}
button:disabled {
  background-color: #999;
  cursor: not-allowed;
}
</style>
