<script setup>
import { router } from '@inertiajs/vue3'

defineProps(['has_prev', 'has_more', 'prev', 'next'])
</script>
<template>
  <div class="flex justify-end gap-3" v-if="has_prev || has_more">
    <button class="btn btn-soft-primary" :disabled="!has_prev" @click="() => router.visit(prev)">
      <Icon icon="fe:arrow-left" />
    </button>
    <button class="btn btn-soft-primary" :disabled="!has_more" @click="() => router.visit(next)">
      <Icon icon="fe:arrow-right" />
    </button>
  </div>
</template>
