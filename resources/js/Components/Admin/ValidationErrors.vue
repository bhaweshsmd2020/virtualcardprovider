<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const errors = computed(() => usePage().props.errors)
const close = () => (usePage().props.errors = [])
const hasErrors = computed(() => Object.keys(errors.value).length > 0)
</script>

<template>
  <transition name="slide-right">
    <div v-if="hasErrors" class="validation-errors">
      <div class="d-flex justify-content-end flex justify-end">
        <button @click="close" type="button">
          <Icon icon="fe:close" />
        </button>
      </div>
      <div class="flex flex-col">
        <p v-for="(error, key) in errors" :key="key">* {{ error.replace(/[.,]/g, ' ') }}</p>
      </div>
    </div>
  </transition>
</template>

<style scoped>
.validation-errors {
  position: fixed;
  right: 1rem;
  top: 1rem;
  border: rgba(255, 0, 0, 0.2) solid 1px;
  padding: 0.8rem;
  background-color: white;
  z-index: 999999;
  border-radius: 5px;
  width: 25rem;
  box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
}
.dark .validation-errors {
  background-color: rgb(31, 41, 55);
}
.validation-errors div p {
  margin-bottom: 0.1rem;
  color: rgb(255, 0, 0);
  font-size: 0.8rem;
}
.validation-errors div button {
  padding: 0.3rem;
  border-radius: 5px;
  border: 1px solid rgb(255, 0, 0);
  color: rgb(255, 0, 0);
  transition: all 0.3s ease;
}
.validation-errors div button svg {
  margin-bottom: 0.1rem;
}
.validation-errors div button:hover {
  background-color: rgb(255, 0, 0);
  color: white;
}
.validation-errors div button svg {
  width: 1rem;
  height: 1rem;
}

.slide-right-enter-active,
.slide-right-leave-active {
  transition-duration: 0.4s;
  transition-property: opacity, transform;
  transition-timing-function: ease;
}

.slide-right-enter-from,
.slide-right-leave-to {
  transform: translateX(100%);
  opacity: 0;
}
</style>
