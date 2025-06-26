<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue"
import PageHeader from "@/Layouts/Admin/PageHeader.vue"
import { Link, useForm } from "@inertiajs/vue3"
import sharedComposable from "@/Composables/sharedComposable"
import Paginate from "@/Components/Admin/Paginate.vue"
import trans from "@/Composables/transComposable"
import moment from "moment"
import Overview from "@/Components/Admin/OverviewGrid.vue"
import SpinnerBtn from "@/Components/SpinnerBtn.vue"
import drawer from "@/Plugins/Admin/drawer"
import toast from "@/Composables/toastComposable"
import { onMounted } from "vue"
import NoDataFound from "@/Components/Admin/NoDataFound.vue"
import FilterDropdown from "@/Components/Admin/FilterDropdown.vue"

defineOptions({ layout: AdminLayout })
const { textExcerpt, deleteRow } = sharedComposable()

onMounted(() => {
  drawer.init()
})

const props = defineProps([
  "segments",
  "buttons",
  "request",
  "notifications",
  "totalNotifications",
  "readNotifications",
  "unreadNotifications",
  "type",
])

const notificatoinOverviews = [
  {
    value: props.totalNotifications,
    title: trans("Total Notifications"),
    iconClass: "bx bx-user-voice",
  },
  {
    value: props.readNotifications,
    title: trans("Read Notifications"),
    iconClass: "bx bx-envelope-open",
  },
  {
    value: props.unreadNotifications,
    title: trans("Unread Notifications"),
    iconClass: "bx bx-envelope",
  },
]

const filterOptions = [
  {
    label: "Title",
    value: "title",
  },
  {
    label: "Comment",
    value: "comment",
  },
  {
    label: "User Email",
    value: "user_email",
  },
  {
    label: "Status",
    value: "seen",
    options: [
      {
        label: "Read",
        value: 1,
      },
      {
        label: "Unread",
        value: 0,
      },
    ],
  },
]
const form = useForm({
  email: "",
  title: "",
  description: "",
  url: "",
})

const createNotification = () => {
  form.post(route("admin.notification.store"), {
    onSuccess: () => {
      form.reset()
      toast.success(trans("Notification created successfully"))
      drawer.of("#addNewNotificationDrawer").hide()
    },
  })
}
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader title="Notifications" :buttons="buttons" />
    <div class="space-y-6">
      <Overview :items="notificatoinOverviews" grid="3" />

      <FilterDropdown :options="filterOptions" />

      <!-- Order Table Starts -->
      <div class="table-responsive whitespace-nowrap rounded-primary">
        <table class="table">
          <thead>
            <tr>
              <th class="w-[5%] uppercase">{{ trans("Title") }}</th>
              <th class="w-[15%] uppercase">{{ trans("Comment") }}</th>
              <th class="w-[10%] uppercase">{{ trans("User") }}</th>
              <th class="w-[10%] uppercase">{{ trans("Seen") }}</th>
              <th class="w-[10%] uppercase">{{ trans("Created At") }}</th>
              <th class="w-[5%] !text-right uppercase">
                {{ trans("Actions") }}
              </th>
            </tr>
          </thead>
          <tbody v-if="notifications.total">
            <tr v-for="notification in notifications.data" :key="notification.id">
              <td class="text-left">
                {{ textExcerpt(notification.title, 80) }}
              </td>
              <td>
                {{ textExcerpt(notification.comment, 50) }}
              </td>
              <td>
                <Link
                  v-if="notification.user_id"
                  class="text-dark"
                  :href="route('admin.users.show', notification.user_id)"
                >
                  {{ textExcerpt(notification.user.name, 15) }}
                </Link>
                <span v-else>{{ trans("None") }}</span>
              </td>

              <td>
                <span
                  class="badge"
                  :class="notification.seen == 1 ? 'badge-success' : 'badge-danger'"
                >
                  {{ notification.seen == 1 ? "Read" : "Unread" }}
                </span>
              </td>

              <td class="text-center">
                {{ moment(notification.created_at).format("DD-MMM-YYYY") }}
              </td>
              <td>
                <div class="flex justify-end">
                  <div class="dropdown" data-placement="bottom-start">
                    <div class="dropdown-toggle">
                      <Icon class="w-30 text-lg" icon="bi:three-dots-vertical" />
                    </div>
                    <div class="dropdown-content w-40">
                      <ul class="dropdown-list">
                        <li class="dropdown-list-item">
                          <a
                            class="dropdown-link delete-confirm"
                            href="javascript:void(0)"
                            @click="
                              deleteRow(
                                route('admin.notification.destroy', notification.id)
                              )
                            "
                          >
                            <Icon class="w-30 text-lg" icon="bx:trash" />
                            <span>{{ trans("Delete") }}</span>
                          </a>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
          <NoDataFound v-else for-table="true" />
        </table>
      </div>

      <Paginate :links="notifications.links" />
    </div>
  </main>

  <div id="addNewNotificationDrawer" class="drawer drawer-right">
    <form @submit.prevent="createNotification">
      <div class="drawer-header">
        <h5>{{ trans("Send Notification") }}</h5>
        <button
          type="button"
          class="btn btn-plain-secondary dark:text-slate-300 dark:hover:bg-slate-700 dark:focus:bg-slate-700"
          data-dismiss="drawer"
        >
          <i data-feather="x" width="1.5rem" height="1.5rem"></i>
        </button>
      </div>
      <div class="drawer-body">
        <div class="form-group">
          <label>{{ trans("Receive Email") }}</label>
          <input v-model="form.email" type="email" name="email" class="input" required />
        </div>
        <div class="form-group">
          <label>{{ trans("Title") }}</label>
          <input
            v-model="form.title"
            type="text"
            name="title"
            class="input"
            required
            maxlength="100"
          />
        </div>
        <div class="form-group">
          <label>{{ trans("Description") }}</label>
          <textarea
            v-model="form.description"
            class="textarea"
            required
            name="description"
            maxlength="200"
          ></textarea>
        </div>
        <div class="form-group">
          <label>{{ trans("Action Link") }}</label>
          <input
            v-model="form.url"
            type="url"
            name="url"
            class="input"
            required
            maxlength="100"
          />
        </div>
      </div>
      <div class="drawer-footer">
        <div class="flex justify-between gap-2">
          <button type="button" class="btn btn-secondary w-full" data-dismiss="drawer">
            <span> {{ trans("Close") }}</span>
          </button>
          <SpinnerBtn
            classes="btn btn-primary w-full"
            :processing="form.processing"
            :btn-text="trans('Create Notification')"
          />
        </div>
      </div>
    </form>
  </div>
</template>
