<script setup>
defineProps(['title', 'card'])
defineEmits(['selectCard'])
</script>

<template>
  <div class="flex flex-col md:flex-row h-auto md:h-64 items-start md:items-center justify-between rounded-md bg-gray-100 p-4 md:p-5 dark:bg-slate-800 space-y-4 md:space-y-0 md:space-x-6">
    <!-- Left Content: Title + Description -->
    <div class="flex flex-col justify-between flex-1 space-y-4">
      <div class="space-y-2">
        <div class="flex flex-wrap items-center gap-2">
          <p class="text-2xl md:text-3xl font-bold">{{ card.title }}</p>
          <div class="flex items-center gap-2 rounded-md bg-gray-200 px-2 py-1 dark:bg-slate-900">
            <Icon class="text-lg md:text-xl" icon="icon-park-outline:vegetable-basket" />
            <span class="text-sm">{{ card.category?.title }}</span>
          </div>
        </div>
        <p class="text-sm font-semibold text-secondary-500 dark:text-secondary-400">
          {{ card.description }}
        </p>
      </div>

      <!-- Button (desktop only) -->
      <div class="hidden md:block">
        <button
          @click="$emit('selectCard')"
          type="button"
          class="btn px-4 py-2 font-semibold text-secondary-50"
          :class="card.is_featured === 1
            ? 'bg-orange-500 hover:bg-orange-600'
            : 'bg-secondary-950 hover:bg-secondary-700'"
        >
          Get card <Icon class="ml-2 text-xl" icon="system-uicons:arrow-right" />
        </button>
      </div>
    </div>

    <!-- Image + Button (mobile only) -->
    <div class="w-full md:w-auto flex flex-col items-center space-y-4 md:space-y-0">
      <img
        class="w-full md:w-80 object-cover rounded-md"
        v-lazy="card.preview"
        alt="card-image"
      />
      <!-- Button (mobile only) -->
      <div class="w-full md:hidden">
        <button
          @click="$emit('selectCard')"
          type="button"
          class="btn w-full px-4 py-2 font-semibold text-secondary-50"
          :class="card.is_featured === 1
            ? 'bg-orange-500 hover:bg-orange-600'
            : 'bg-secondary-950 hover:bg-secondary-700'"
        >
          Get card <Icon class="ml-2 text-xl" icon="system-uicons:arrow-right" />
        </button>
      </div>
    </div>
  </div>
</template>
