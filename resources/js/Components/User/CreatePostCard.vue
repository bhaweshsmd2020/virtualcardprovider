<script setup>
defineProps({
  classes: {
    type: String,
    default: 'h-88 min-w-[18rem]'
  },
  text: {
    type: String,
    default: 'Create Post'
  }
})
const emit = defineEmits(['action'])
</script>

<template>
  <button
    @click="emit('action')"
    :class="classes"
    class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-primary-200 bg-primary-400/10 dark:bg-primary-700/10"
  >
    <div
      class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full bg-primary-400 bg-opacity-10 text-primary-500"
    >
      <i class="bx bx-plus text-2xl"></i>
    </div>
    <p class="mt-2 text-lg font-semibold">{{ text }}</p>
  </button>
</template>
