<script setup>
// Theme default logic
if (!localStorage.getItem('theme')) {
  localStorage.setItem('theme', 'light');
}

// Apply theme before Vue renders
const theme = localStorage.getItem('theme');

if (
  theme === 'dark' ||
  (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)
) {
  document.documentElement.classList.add('dark');
} else {
  document.documentElement.classList.remove('dark');
}

import sharedComposable from '@/Composables/sharedComposable'
import Modal from '@/Components/Admin/Modal.vue'
import trans from '@/Composables/transComposable'
import themeSwitcher from '@/Plugins/Admin/themeSwitcher'
import { onMounted, ref } from 'vue'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useModalStore } from '@/Store/modalStore'
import { Icon } from '@iconify/vue'
import FloatingDropdown from '@/Components/Admin/FloatingDropdown.vue'
import axios from 'axios'

const { authUser, logout, uiAvatar } = sharedComposable()

const props = defineProps({
  navMenuItems: { type: Array, required: true },
  panel: { type: String, default: 'admin' }
})

const modalStore = useModalStore()

const searchInput = ref()
const openSearchModal = () => {
  modalStore.open('searchModal')
  setTimeout(() => {
    searchInput.value?.focus()
  }, 50)
}

// notification
const userNotifications = ref(usePage().props.userNotifications ?? [])
const unreadNotifications = computed(() => {
  return userNotifications.value?.filter((item) => item.seen == 0).length ?? 0
})

const maskAsRead = (item) => {
  axios.post(route(props.panel + '.notifications.read', item.id)).then((res) => {
    if (res.data?.redirect_to) {
      window.location.href = res.data?.redirect_to
    }
  })
}

const clearAllNotifications = () => {
  window.location.href = route(props.panel + '.notifications.clear')
}

// search modal
const search = ref('')
const filteredMenuItems = computed(() => {
  let menus = []

  props.navMenuItems.forEach((item) => {
    if (item.type == 'dropdown') {
      if (item.subs.length > 0) {
        item.subs.forEach((sub) => menus.push(sub))
      }
    } else if (item.type != 'heading') {
      menus.push(item)
    }
  })

  if (search.value) {
    return menus.filter((page) => page.text?.toLowerCase().includes(search.value?.toLowerCase()))
  }

  return menus.slice(0, 100)
})

const lastLogin = ref(null)

onMounted(async () => {
  try {
    const response = await axios.get('/user/last-login');
    
    if (response.data && response.data.last_login_at) {
      let dateStr = response.data.last_login_at; // Access the correct field
      
      // Ensure it's a string and replace space with 'T' to make it a valid ISO string
      if (typeof dateStr === 'string') {
        dateStr = dateStr.replace(' ', 'T');
        
        const date = new Date(dateStr);

        // Check if the date is valid
        if (isNaN(date)) {
          console.error('Invalid date format:', dateStr);
          lastLogin.value = 'Invalid date format';
        } else {
          // Format the date as "1 April 2025, 12:13:13 PM"
          lastLogin.value = date.toLocaleString('en-GB', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true,  // This ensures 12-hour format with AM/PM
          }).replace(',', '');  // Remove comma after the date and replace with "at"
          
          // Replace the word 'at' with a comma, ensuring the format is correct
          lastLogin.value = lastLogin.value.replace(' at', ',');
        }
      } else {
        console.error('Expected string format for date, but got:', typeof dateStr);
        lastLogin.value = 'Invalid date format';
      }
    } else {
      lastLogin.value = 'No login data available';
    }
  } catch (error) {
    console.error('Error fetching last login:', error);
    lastLogin.value = 'Error fetching last login';
  }
});
</script>

<template>
  <!-- Top nav -->
  <header class="header">
    <div class="container-fluid flex items-center justify-between">
      <!-- Sidebar Toggle & Search Starts -->
      <div class="flex items-center space-x-6">
        <button class="sidebar-toggle">
          <span class="flex space-x-4">
            <svg
              stroke="currentColor"
              fill="none"
              stroke-width="0"
              viewBox="0 0 24 24"
              height="22"
              width="22"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h8m-8 6h16"
              ></path>
            </svg>
          </span>
        </button>

        <!-- Mobile Search Starts -->
        <div class="sm:hidden">
          <button
            type="button"
            @click="openSearchModal"
            class="flex items-center justify-center rounded-full text-slate-500 transition-colors duration-150 hover:text-primary-500 focus:text-primary-500 dark:text-slate-400 dark:hover:text-slate-300"
          >
            <Icon icon="fe:search" />
          </button>
        </div>
        <!-- Mobile Search Ends -->

        <!-- Searchbar Start -->
        <button
          type="button"
          @click="openSearchModal"
          class="group hidden h-10 w-72 items-center overflow-hidden rounded-primary bg-slate-100 px-3 shadow-sm dark:border-transparent dark:bg-slate-700 sm:flex"
        >
          <Icon icon="fe:search" />
          <span class="ml-2 text-sm text-slate-400">
            <code>Ctrl + K</code> {{ trans('search') }}, <code>Esc</code>
            {{ trans('to close') }}
          </span>
        </button>        
        <!-- Searchbar Ends -->
      </div>

      <!-- Header Options Starts -->
      <div class="flex items-center">

        <p class="hide-on-mobile" v-if="panel === 'user'">
          Last Login: {{ lastLogin }}
        </p>
        
        <!-- Language Dropdown Starts -->
        <template v-if="Object.keys($page.props?.languages ?? {}).length > 1">
          <FloatingDropdown :btn-text="$page.props?.languages[$page.props?.locale]">
            <ul class="dropdown-list">
              <li
                v-for="(language, key) in $page.props.languages"
                :key="key"
                class="dropdown-list-item"
              >
                <Link
                  as="button"
                  :href="route('set-locale', key)"
                  method="patch"
                  class="dropdown-btn"
                >
                  {{ language }}
                </Link>
              </li>
            </ul>
          </FloatingDropdown>
        </template>

        <!-- Dark Mood Toggle Starts -->
        <div class="dropdown" data-strategy="absolute" id="theme-switcher-dropdown">
          <button
            class="dropdown-toggle px-3 text-slate-500 hover:text-slate-700 focus:text-primary-500 dark:text-slate-400 dark:hover:text-slate-300 dark:focus:text-primary-500"
            type="button"
          >
            <Icon class="hidden dark:block" width="24" height="24" icon="fe:moon">
              {{ trans('Dark') }}
            </Icon>
            <Icon class="block dark:hidden" width="24" height="24" icon="fe:sunny-o">
              {{ trans('Light') }}
            </Icon>
          </button>

          <div class="dropdown-content mt-3 w-36">
            <ul class="dropdown-list">
              <li class="dropdown-list-item">
                <button type="button" class="dropdown-btn" @click="themeSwitcher.setTheme('light')">
                  <Icon width="16" height="16" icon="fe:sunny-o"></Icon>
                  <span> {{ trans('Light') }}</span>
                </button>
              </li>

              <li class="dropdown-list-item">
                <button type="button" class="dropdown-btn" @click="themeSwitcher.setTheme('dark')">
                  <Icon width="16" height="16" icon="fe:moon"></Icon>
                  <span>{{ trans('Dark') }}</span>
                </button>
              </li>

              <li class="dropdown-list-item">
                <button type="button" class="dropdown-btn" @click="themeSwitcher.setTheme('system')">
                  <Icon width="16" height="16" icon="tdesign:system-setting"></Icon>
                  <span>{{ trans('System') }}</span>
                </button>
              </li>
            </ul>
          </div>
        </div>

        <!-- Notification Dropdown Starts -->
        <FloatingDropdown btn-type="slot">
          <template #btnContent>
            <button
              class="relative flex items-center justify-center rounded-full text-slate-500 transition-colors duration-150 hover:text-slate-700 focus:text-primary-500 dark:text-slate-400 dark:hover:text-slate-300 dark:focus:text-primary-500"
            >
              <Icon width="24" height="24" icon="fe:bell"></Icon>
              <span
                v-if="unreadNotifications"
                class="absolute -right-1 -top-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-danger-500 text-[11px] text-slate-200"
              >
                {{ unreadNotifications }}
              </span>
            </button>
          </template>
          <div class="w-[17.5rem] divide-y dark:divide-slate-700 sm:w-80">
            <div class="flex items-center justify-between px-4 py-4">
              <h6 class="text-slate-800 dark:text-slate-300">
                {{ trans('Notifications') }}
              </h6>
              <button
                @click="clearAllNotifications()"
                class="text-xs font-medium text-slate-600 hover:text-primary-500 dark:text-slate-300"
                v-if="userNotifications.length"
              >
                {{ trans('Clear All') }}
              </button>
            </div>

            <div class="h-80 w-full" data-simplebar>
              <ul>
                <template v-if="userNotifications.length">
                  <li
                    v-for="(item, index) in userNotifications"
                    :key="index"
                    @click="maskAsRead(item)"
                    class="flex cursor-pointer gap-4 border-b-2 border-white px-4 py-3 transition-colors duration-150 hover:bg-slate-200/70 dark:border-secondary-600 dark:hover:bg-slate-700"
                    :class="{ 'bg-slate-100/70 dark:bg-slate-700': !item.seen }"
                  >
                    <div
                      class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-500"
                    >
                      <i class="bx bx-user-voice" width="20" height="20"></i>
                    </div>
                    <a :href="item.url" @click="($event) => $event.preventDefault()">
                      <h6 class="text-sm font-normal">{{ item.title }}</h6>
                      <p class="text-xs text-slate-400" :title="item.comment">
                        {{ item.comment_short }}
                      </p>
                      <p class="mt-1 flex items-center gap-1 text-xs text-slate-400">
                        <Icon icon="fe:clock" width="1em" height="1em"></Icon>
                        <span>{{ item.created_at_human_date }}</span>
                      </p>
                    </a>
                  </li>
                </template>
                <li v-else class="mt-5 text-center">
                  {{ trans('No notifications') }}
                </li>
              </ul>
            </div>

            <div class="px-4 py-2" v-if="userNotifications.length > 5">
              <Link
                v-if="panel === 'admin'"
                :href="route('admin.notification.index')"
                class="btn btn-primary-plain btn-sm w-full"
              >
                <span>{{ trans('View More') }}</span>
                <Icon icon="arrow-right" width="1rem" height="1rem"></Icon>
              </Link>
            </div>
          </div>
        </FloatingDropdown>

        <!-- Profile Dropdown Starts -->
        <FloatingDropdown btn-type="slot">
          <template #btnContent>
            <button class="group relative flex items-center gap-x-1.5" type="button">
              <div class="avatar avatar-circle avatar-indicator avatar-indicator-online">
                <img
                  class="avatar-img group-focus-within:ring group-focus-within:ring-primary-500"
                  v-lazy="uiAvatar(authUser.name, authUser.avatar)"
                  :alt="authUser.name"
                />
              </div>
            </button>
          </template>
          <div class="w-56 divide-y dark:divide-slate-600">
            <div class="px-4 py-3">
              <p class="text-sm">
                {{ trans('Welcome') }} <strong>{{ authUser.name }}</strong
                >!
              </p>
              <p class="truncate text-sm font-medium">({{ authUser.email }})</p>
            </div>

            <div class="dropdown-btn">
              <Icon icon="fe:user" />
              <Link :href="`/${panel}/profile`">{{ trans('Edit Profile') }}</Link>
            </div>
            <div class="dropdown-btn" v-if="panel === 'admin'">
              <Icon icon="fe:lock" />
              <a href="/admin/clear-cache">{{ trans('Clear Cache') }}</a>
            </div>

            <div class="py-1">
              <form method="POST" action="#">
                <button type="button" @click="logout()" class="dropdown-btn">
                  <Icon icon="fe:logout"></Icon>
                  <span>{{ trans('Logout') }}</span>
                </button>
              </form>
            </div>
          </div>
        </FloatingDropdown>
      </div>
    </div>
  </header>
  <!-- Header -->

  <Modal :state="modalStore.states['searchModal']" :closeBtn="false">
    <h4 class="text-center">{{ trans('Menu Search') }}</h4>
    <div class="group my-5 flex items-center rounded-lg px-3 py-1 dark:bg-slate-600">
      <input
        ref="searchInput"
        type="text"
        v-model="search"
        class="w-full rounded bg-transparent p-3 text-sm text-slate-700 placeholder:text-slate-400 focus:ring-0 dark:text-slate-200"
        placeholder="Search"
      />
    </div>

    <div
      v-if="filteredMenuItems.length > 0"
      class="styled-scrollbar max-h-[560px] space-y-2 overflow-y-auto"
    >
      <Link
        v-for="(page, index) in filteredMenuItems"
        :key="index"
        @click="modalStore.close"
        :href="page.uri || '#'"
        class="flex items-center justify-between gap-2 rounded-primary bg-slate-50 px-4 py-3 text-sm shadow-sm hover:bg-slate-100 dark:bg-slate-700 dark:hover:bg-slate-600"
      >
        <div class="flex items-center gap-2">
          <Icon :icon="page.icon ?? 'bx:subdirectory-right'" />
          <span>{{ page.text }}</span>
        </div>
        <Icon :icon="'bx:chevron-right'" />
      </Link>
    </div>
    <!-- no result found -->
    <div v-else class="text-center text-slate-500 dark:text-slate-400">
      {{ trans('No result found') }}
    </div>
  </Modal>
</template>
<style>

/* Hide on mobile (screens less than 768px) */
@media (max-width: 880px) {
  .hide-on-mobile {
    display: none;
  }

  .left-1 {
    left: -9.75rem !important;
  }
}

/* Show on desktop (768px and above) */
@media (min-width: 881px) {
  .hide-on-mobile {
    display: block;
  }
}
</style>