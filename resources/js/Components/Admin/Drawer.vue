<script setup>
import drawer from '@/Plugins/Admin/drawer'
import { onMounted } from 'vue'
import SpinnerBtn from '../SpinnerBtn.vue'

const props = defineProps({
  id: {
    type: String,
    required: true
  },

  title: {
    type: String,
    required: true
  },

  handleSubmit: {
    type: Function,
    default: () => {}
  },

  processing: {
    type: Boolean,
    default: false
  }
})

onMounted(() => {
  drawer.init()
})
</script>
<template>
  <form @submit.prevent="handleSubmit">
    <div :id="id" class="drawer drawer-right">
      <div class="drawer-header">
        <h5 v-html="title"></h5>
        <button
          type="button"
          class="btn btn-plain-secondary dark:text-slate-300 dark:hover:bg-slate-700 dark:focus:bg-slate-700"
          data-dismiss="drawer"
        >
          <i data-feather="x" width="1.5rem" height="1.5rem"></i>
        </button>
      </div>
      <div class="drawer-body">
        <slot />
      </div>
      <div class="drawer-footer">
        <div class="flex justify-between gap-2">
          <button type="button" class="btn btn-secondary w-full" data-dismiss="drawer">
            <span>{{ trans('Cancel') }}</span>
          </button>

          <SpinnerBtn
            :processing="processing"
            classes="w-full btn btn-primary flex items-center"
            :btn-text="trans('Submit')"
          />
        </div>
      </div>
    </div>
  </form>
</template>
