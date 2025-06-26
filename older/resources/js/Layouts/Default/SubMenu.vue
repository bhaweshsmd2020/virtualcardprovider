<script setup>
import SubMenu from "@/Layouts/Default/SubMenu.vue"
const props = defineProps({
  children: {
    type: [Array, Object],
  },
})
</script>

<template>
  <ul class="dropdown-menu">
    <li v-for="child in children" :key="child.id">
      
      <template v-if="child.target == '_top'">
        <a :href="child.href ?? '#'" class="dropdown-item">
          {{ child.text }}
        </a>
      </template>
      <template v-else>
        <Link :href="child.href ?? '#'" :target="child.target" class="dropdown-item">
        {{ child.text }}
        </Link>
      </template>

      <template v-if="child.children?.length > 0">
        <SubMenu :children="child.children" />
      </template>
    </li>
  </ul>
</template>
