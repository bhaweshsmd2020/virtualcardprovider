<script setup>
import UserLayout from '@/Layouts/User/UserLayout.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import { router } from '@inertiajs/vue3'
import moment from 'moment'
import { ref } from 'vue'
import sharedComposable from '@/Composables/sharedComposable'
import toast from '@/Composables/toastComposable'
import Swal from 'sweetalert2'

defineOptions({ layout: UserLayout })

const props = defineProps(['buttons', 'support'])
const { uiAvatar } = sharedComposable()
const form = ref({
  message: '',
  status: props.support.status
})

const updateSupport = () => {
  router.patch(
    route('user.supports.update', props.support.id),
    {
      message: form.value.message
    },
    {
      onSuccess: () => {
        form.value.message = ''

        function showAlert() {
          Swal.fire({
            icon: "success",
            title: "<h2 style='font-size: 20px;'>Message sent successfully</h2>",
            confirmButtonText: "Okay",
            allowOutsideClick: false,
            customClass: {
              popup: "custom-swal-popup",
            },
            didOpen: (toast) => {
              const closeButton = document.createElement("button")
              closeButton.innerHTML = "&times;"
              closeButton.classList.add("swal-close-btn")
              closeButton.onclick = () => Swal.close()

              toast.appendChild(closeButton)
            },
          });
        }
        showAlert();
      }
    }
  )
}
</script>

<template>
  <main class="container flex-grow p-4 sm:pr-4">
    <PageHeader title="Support" :buttons="buttons" />
    <div class="h-fit space-y-6">
      <div class="h-full">
        <div class="relative mx-auto rounded-primary bg-white shadow dark:bg-slate-800 2xl:w-8/12">
          <div
            class="flex items-center justify-between rounded-t-primary border-b border-b-slate-200 p-4 dark:border-b-slate-600"
          >
            <div class="flex items-center justify-start gap-x-3">
              <button
                id="chat-btn-show-sidebar"
                class="text-slate-500 hover:text-slate-700 focus:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300 dark:focus:text-slate-300 xl:hidden"
              >
                <i width="20" height="20" data-feather="menu"></i>
              </button>
              <div class="avatar avatar-circle avatar-indicator avatar-indicator-online">
                <img
                  class="avatar-img"
                  v-lazy="uiAvatar(support.user?.name, support.user?.avatar)"
                  alt="profile-img"
                />
              </div>

              <div>
                <h6
                  class="whitespace-nowrap text-sm font-medium text-slate-700 dark:text-slate-100"
                >
                  {{ support.user?.name }}
                </h6>
                <p class="whitespace-normal text-sm font-normal text-slate-500 dark:text-slate-400">
                  {{ trans('Subject :') }} {{ support.subject ?? '' }}
                </p>
              </div>
            </div>
            <!-- Avatar and Menu Button End -->
          </div>

          <div
            class="relative max-h-[calc(100vh-18rem)] overflow-auto px-4 pb-28 md:max-h-[calc(100vh-17rem)]"
            data-simplebar
          >
            <ul class="space-y-3">
              <!-- Friend Chat -->
              <li
                class="group mt-5"
                :class="reply.is_admin === 0 ? 'pr' : ''"
                v-for="reply in support.conversations"
                :key="reply.id"
              >
                <div class="flex gap-x-3 group-[.pr]:flex-row-reverse">
                  <div class="avatar avatar-circle avatar-sm shrink-0">
                    <img
                      class="avatar-img"
                      v-lazy="uiAvatar(reply.user.name, reply.user.avatar)"
                      alt="profile-img"
                    />
                  </div>
                  <div class="flex max-w-sm flex-col items-start gap-y-2 group-[.pr]:items-end">
                    <p
                      class="rounded-primary rounded-tl-none bg-slate-100 p-2 text-sm text-slate-600 group-[.pr]:rounded-tl-primary group-[.pr]:rounded-tr-none group-[.pr]:bg-primary-500 group-[.pr]:text-slate-100 dark:bg-slate-700 dark:text-slate-300"
                    >
                      {{ reply.comment }}  
                    </p>
                    <span class="text-xs font-normal text-slate-400">
                      {{ moment(reply.created_at).format('D MMM, YYYY') }}</span
                    >
                  </div>
                 <a  v-if="reply.image" :href="reply.image" target="_blank" title="View attachment">
                   <img
                      class="support_image"
                      :src="reply.image"
                      alt="profile-img"
                    />
                   </a> 
                </div>
              </li>
            </ul>
            <!-- For Div: Scroll To Bottom  -->
            <div id="chat-scroll-view"></div>
          </div>

          <div
            class="absolute bottom-[-0.5px] left-0 right-0 z-10 rounded-b-primary bg-white py-4 dark:bg-slate-800"
          >
            <form
              v-if="support.status == 1"
              @submit.prevent="updateSupport"
              class="mx-4 flex h-[4.5rem] items-center rounded-primary border border-slate-200 shadow dark:border-slate-600"
            >
              <input
                v-model="form.message"
                class="h-full w-full border-transparent bg-transparent px-4 text-sm text-slate-700 placeholder:text-slate-500 focus:border-transparent focus:ring-0 dark:text-slate-300 dark:placeholder:text-slate-400"
                type="text"
                placeholder="Type your message here"
              />
              <div class="flex items-center justify-end gap-x-4 px-4">
                <button type="submit" :disabled="form.processing" class="btn btn-sm btn-primary">
                  <i width="18" height="18" data-feather="send"></i>
                  <span class="hidden md:inline-block">{{ trans('Send') }}</span>
                </button>
              </div>
            </form>
            <div v-else class="text-center font-bold text-red-500">
              {{ trans("You can't not send reply") }}
            </div>
          </div>
          <!-- Chat Wrapper Footer Ends -->
        </div>

        <div
          id="chat-overlay"
          class="absolute inset-0 z-10 hidden h-full w-full bg-black bg-opacity-0 transition-colors duration-300 ease-in-out xl:hidden"
        ></div>
      </div>
    </div>
  </main>
</template>
