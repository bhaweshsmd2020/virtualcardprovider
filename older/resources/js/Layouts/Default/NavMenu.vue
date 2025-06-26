<script setup>
import { usePage } from '@inertiajs/vue3'
import SubMenu from '@/Layouts/Default/SubMenu.vue'
import { ref } from 'vue'
import sharedComposable from '@/Composables/sharedComposable'
import LanguageSwitch from '@/Components/Web/LanguageSwitch.vue'
const { authUser } = sharedComposable()
defineProps({
  layoutName: {
    type: String,
    default: 'Default'
  }
})
const page = usePage()
const mainMenu = page.props.menus.filter((item) => item.position === 'main-menu')[0]
const primaryData = page.props?.primaryData ?? {}

const collapse = ref(false)
</script>

<template>
  <nav class="navbar navbar-expand-lg p0 order-lg-2" v-if="mainMenu">
    <button @click="collapse = !collapse" class="navbar-toggler d-block d-lg-none" type="button"
      aria-label="Toggle navigation" :aria-expanded="collapse ? 'true' : 'false'">
      <span></span>
    </button>

    <div class="navbar-collapse collapse" :class="{ show: collapse }" id="navbarNav">
      <ul class="navbar-nav align-items-lg-center">
        <li class="d-block d-lg-none">
          <div class="logo">
            <Link href="/" v-if="layoutName === 'Default'" class="d-block">
            <img v-lazy="primaryData.deep_logo" alt="logo" />
            </Link>
            <Link href="/" v-if="layoutName === 'Web'" class="d-block">
            <img v-lazy="primaryData.deep_logo" alt="logo" />
            </Link>
          </div>
        </li>

        <li v-for="item in mainMenu.data ?? []" :key="item.id" class="nav-item"
          :class="{ dropdown: item.children?.length > 0 }">
          <!-- dropdown -->
          <template v-if="item.children?.length > 0">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
              data-bs-auto-close="outside" aria-expanded="false">
              {{ item.text }}
            </a>
            <SubMenu :children="item.children" />
          </template>
          <template v-else>
            <Link v-if="item.href" class="nav-link" :href="item.href" :target="item.target" role="button">
            {{ item.text }}
            </Link>

            <Link v-else href="#" class="nav-link dropdown"> {{ item.text }} </Link>
          </template>
        </li>

        <li class="d-md-none pe-2 ps-2">
          <LanguageSwitch classes="mt-10" />
        </li>

        <li class="d-md-none pe-2 ps-2">
          <Link href="/register" class="signup-btn-one icon-link w-100 mt-20" v-if="!authUser">
          <span class="flex-fill text-center">{{ trans('Signup') }}</span>
          <div class="icon rounded-circle d-flex align-items-center justify-content-center">
            <i class="bi bi-arrow-right"></i>
          </div>
          </Link>
          <a href="/login" class="signup-btn-one icon-link w-100 mt-20" v-else>
            <span class="flex-fill text-center">{{ trans('Dashboard') }}</span>
            <div class="icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-arrow-right"></i>
            </div>
          </a>
        </li>
      </ul>
    </div>
    <transition name="fade">
      <div @click.self="collapse = false" v-if="collapse" class="navbar-underlay" />
    </transition>
  </nav>
</template>
