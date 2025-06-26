<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
defineProps({
  classes: {
    type: String,
    // default: 'mt-30'
  }
})
const isOpen = ref(false)

const selectContainer = ref(null)
const switchBtn = ref(null)
const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const outsideClick = (event) => {
  if (isOpen.value && !switchBtn.value.contains(event.target) && event.target !== switchBtn.value) {
    isOpen.value = false
  }
}
onMounted(() => {
  document.addEventListener('click', outsideClick)
})

onUnmounted(() => {
  document.removeEventListener('click', outsideClick)
})
</script>

<template>
  <div class="position-relative w-100" :class="classes">
    <button
      ref="switchBtn"
      type="button"
      class="lang-toggler w-100 me-2 rounded"
      @click="toggleDropdown"
    >
      <span>{{ $page.props.languages[$page.props.locale] }}</span>

      <Transition name="rotate" mode="out-in">
        <Icon v-if="isOpen" icon="carbon:chevron-up" key="up" />
        <Icon v-else icon="carbon:chevron-down" key="down" />
      </Transition>
    </button>

    <Transition name="slide-bottom">
      <ul class="lang-list position-absolute w-100 z-3" v-if="isOpen" ref="selectContainer">
        <li
          class="list-option"
          :class="{
            selected: key === $page.props.locale
          }"
          v-for="(lang, key) in $page.props.languages"
          :key="key"
        >
          <Link
            as="button"
            :href="route('set-locale', key)"
            method="patch"
            class="dropdown-btn w-100 text-start"
          >
            {{ trans(lang) }}
          </Link>
        </li>
      </ul>
    </Transition>
  </div>
</template>
