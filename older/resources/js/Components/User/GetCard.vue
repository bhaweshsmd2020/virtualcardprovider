<script setup>
defineProps({
  card_intro_details: Object,
  showImage: {
    type: Boolean,
    default: true
  },
  border: {
    type: String,
    default: 'border-white'
  }
})
</script>

<template>
  <div
    class="mx-auto flex flex-col md:flex-row max-w-4xl items-center gap-6 md:gap-8 rounded-xl border dark:border-gray-800 p-4 md:p-1"
    :class="border" style="background-color: #FFC58D;"
  >
    <!-- Image -->
    <img
      v-if="showImage"
      class="w-full md:w-80 object-cover rounded-md"
      v-lazy="card_intro_details.preview"
      alt="card-image"
    />

    <!-- Text + Button -->
    <div class="flex flex-col justify-between w-full md:h-40 space-y-3">
      <div class="space-y-2">
        <p class="text-xl md:text-xl font-bold text-center text-white" style="margin-top: 15px;">{{ card_intro_details.title }}</p>
        <p class="text-sm font-semibold text-gray-500 text-center text-white">
          {{ card_intro_details.overview }}
        </p>
      </div>
      <center>
        <Link
        :href="route('user.card-issue.create')"
        class="btn w-full md:w-auto bg-secondary-950 py-2 text-secondary-50 hover:bg-secondary-700" style="width: fit-content; background-color: rgb(2 111 94); margin-bottom: 10px;"
      >
        {{ card_intro_details.btn_text }}
      </Link>
      </center>
    </div>
  </div>
</template>
