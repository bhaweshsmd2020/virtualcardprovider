<script setup>
import { onMounted, ref, watch } from 'vue'
import 'summernote/dist/summernote-lite.css'
import 'summernote/dist/summernote-lite.js'
import $ from 'jquery'

window.$ = window.jQuery = $;

const modelValue = defineModel({ type: String })

const editorRef = ref(null)

onMounted(() => {
  // Initialize Summernote on the editor div
  $(editorRef.value).summernote({
    height: 200,
    callbacks: {
      onChange: (contents) => {
        modelValue.value = contents
      }
    }
  })

  // Set initial value if exists
  if (modelValue.value) {
    $(editorRef.value).summernote('code', modelValue.value)
  }
})

// Watch if v-model changes from outside
watch(modelValue, (newValue) => {
  if ($(editorRef.value).summernote('code') !== newValue) {
    $(editorRef.value).summernote('code', newValue)
  }
})
</script>

<template>
  <div>
    <div ref="editorRef"></div>
  </div>
</template>
