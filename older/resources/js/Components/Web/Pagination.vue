<script setup>
const props = defineProps({
  links: [Object, Array],
  preserveState: false,
  preserveScroll: false,
})
</script>

<template>
  <div v-if="links.length > 3" class="pagination-one mt-20">
    <ul class="style-none d-flex align-items-center justify-content-center">
      <li v-for="(link, key) in links">
        <a
          v-if="link.url === null"
          :key="key"
          :class="{ active: link.active }"
          v-html="link.label"
        ></a>
        <Link
          v-else
          :preserve-state="preserveState"
          :preserve-scroll="preserveScroll"
          :key="`link-${key}`"
          :class="{ active: link.active }"
          :href="link.url"
        >
          <span class="text-nowrap" v-html="link.label"></span>
        </Link>
      </li>
    </ul>
  </div>
</template>
