<script setup>
const props = defineProps(['items', 'grid'])

// Apply grid columns based on props (defaults to 4)
const gridClass = `lg:grid-cols-${props.grid ?? 4}`

// Colors to rotate every 4 cards
const bgColors = ['#ff000017', '#00ff0017', '#0000ff17', '#ffa50017']

// Function to get background color based on index
const getBackgroundColor = (index) => {
  return bgColors[index % 4]
}
</script>

<template>
  <section class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3" :class="gridClass">
    <!-- Loop through items -->
    <div
      class="card"
      v-for="(item, index) in props.items"
      :key="index"
      :style="{ background: getBackgroundColor(index) }"
    >
      <div class="card-body flex items-center gap-4">
        <div
          class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-primary-500 bg-opacity-20 text-primary-500"
        >
          <i class="text-3xl" :class="item.iconClass ?? 'bx bx-box'"></i>
        </div>
        <div class="flex flex-1 flex-col gap-1">
          <p class="text-sm tracking-wide text-slate-500">{{ item.title }}</p>
          <div class="flex flex-wrap items-baseline justify-between gap-2">
            <h4>{{ item.value }}</h4>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
