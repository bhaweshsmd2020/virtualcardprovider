<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue"
import PageHeader from "@/Layouts/Admin/PageHeader.vue"
import { Link, useForm, router } from "@inertiajs/vue3"
import Paginate from "@/Components/Admin/Paginate.vue"
import moment from "moment"
import { ref } from "vue"
import trans from "@/Composables/transComposable"
import sharedComposable from "@/Composables/sharedComposable"
import Overview from "@/Components/Admin/OverviewGrid.vue"
import SpinnerBtn from "@/Components/SpinnerBtn.vue"
import drawer from "@/Plugins/Admin/drawer"
import { onMounted } from "vue"
import toast from "@/Composables/toastComposable"
import NoDataFound from "@/Components/Admin/NoDataFound.vue"
import FilterDropdown from "@/Components/Admin/FilterDropdown.vue"

defineOptions({ layout: AdminLayout })
const { deleteRow } = sharedComposable()

onMounted(() => {
  drawer.init()
})

const props = defineProps([
  "emails",
  "buttons",
])

const emailForm = useForm({
  email: "",
  status: 1,
})

const editForm = ref({})

const storeEmail = () => {
  emailForm.post(route("admin.card.emails.store"), {
    onSuccess: () => {
      emailForm.reset()
      toast.success(trans("Email has been added successfully"))
      drawer.of("#addNewEmailDrawer").hide()
    },
  })
}

const openEditEmailDrawer = (emails) => {
  editForm.value = { ...emails }
  drawer.of("#editEmailDrawer").show()
}

const updateEmail = () => {
  router.post(route("admin.card.emails.update", editForm.value.id), editForm.value, {
    onSuccess: () => {
      editForm.value = {}
      toast.success(trans("Email has been updated successfully"))
      drawer.of("#editEmailDrawer").hide()
    },
  })
}

const filterOptions = [
  {
    label: 'Email',
    value: 'email'
  },
  {
    label: 'Status',
    value: 'status',
    options: [
      {
        label: 'Assigned',
        value: '1'
      },
      {
        label: 'Not Assigned',
        value: '0'
      }
    ]
  }
]
</script>

<template>
  <main class="container p-4 sm:p-6">
    <PageHeader title="Card Emails" :buttons="buttons" />
    <div class="space-y-6">
      <FilterDropdown :options="filterOptions" />

      <div class="table-responsive whitespace-nowrap rounded-primary">
        <table class="table">
          <thead>
            <tr>
              <th>{{ trans('Email') }}</th>
              <th>{{ trans('Assigned To') }}</th>
              <th>{{ trans('Status') }}</th>
              <th>{{ trans('Created At') }}</th>
              <th class="flex justify-end">{{ trans('Action') }}</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="emails in emails.data" :key="emails.id">
              <td>
                {{ emails.email }}
              </td>
              <td>
                <span v-if="emails.user_id == null">
                  Not Assigned
                </span>
                <span v-else>
                  <Link class="underline" :href="route('admin.users.show', emails.user.id)">
                    {{ emails.user.email }}
                  </Link>
                </span>                
              </td>
              <td>
                <span
                  class="badge"
                  :class="emails.status == '0' ? 'badge-success' : 'badge-danger'"
                >
                  {{ emails.status == '0' ? trans('Not Assigned') : trans('Assigned') }}
                </span>
              </td>
              <td>
                {{ moment(emails.created_at).format('D-MMM-Y') }}
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
                          <button
                            @click="openEditEmailDrawer(emails)"
                            class="dropdown-link"
                          >
                            <Icon
                              class="h-6 text-slate-400"
                              icon="material-symbols:edit-outline"
                            />
                            <span>{{ trans("Edit") }}</span>
                          </button>
                        </li>

                        <li class="dropdown-list-item">
                          <Link
                            as="button"
                            class="dropdown-link"
                            @click="deleteRow('/admin/card-emails/delete/' + emails.id)"
                          >
                            <Icon class="h-6" icon="material-symbols:delete-outline" />
                            <span>{{ trans("Delete") }}</span>
                          </Link>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <Paginate :links="emails.links" />
    </div>
  </main>

  <div id="addNewEmailDrawer" class="drawer drawer-right">
    <form @submit.prevent="storeEmail()">
      <div class="drawer-header">
        <h5>{{ trans("Add New Email") }}</h5>
        <button
          type="button"
          class="btn btn-plain-secondary dark:text-slate-300 dark:hover:bg-slate-700 dark:focus:bg-slate-700"
          data-dismiss="drawer"
        >
          <i data-feather="x" width="1.5rem" height="1.5rem"></i>
        </button>
      </div>
      <div class="drawer-body">
        <div class="mb-2">
          <label>{{ trans("Email") }}</label>
          <input
            v-model="emailForm.email"
            type="text"
            name="email"
            class="input"
            required
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
            :processing="emailForm.processing"
            :btn-text="trans('Save Changes')"
          />
        </div>
      </div>
    </form>
  </div>

  <div id="editEmailDrawer" class="drawer drawer-right">
    <form @submit.prevent="updateEmail()">
      <div class="drawer-header">
        <h5>{{ trans("Edit Email") }}</h5>
        <button
          type="button"
          class="btn btn-plain-secondary dark:text-slate-300 dark:hover:bg-slate-700 dark:focus:bg-slate-700"
          data-dismiss="drawer"
        >
          <i data-feather="x" width="1.5rem" height="1.5rem"></i>
        </button>
      </div>
      <div class="drawer-body">
        <div class="mb-2">
          <label>{{ trans("Email") }}</label>
          <input
            v-model="editForm.email"
            type="text"
            name="email"
            class="input"
            required
          />
        </div>

        <div class="mb-2">
          <label>{{ trans("Status") }}</label>
          <select v-model="editForm.status" class="select" name="status">
            <option value="1">{{ trans("Assigned") }}</option>
            <option value="0">{{ trans("Not Assigned") }}</option>
          </select>
        </div>
      </div>
      <div class="drawer-footer">
        <div class="flex justify-between gap-2">
          <button type="button" class="btn btn-secondary w-full" data-dismiss="drawer">
            <span> {{ trans("Close") }}</span>
          </button>
          <SpinnerBtn
            classes="btn btn-primary w-full"
            :processing="editForm.processing"
            :btn-text="trans('Save Changes')"
          />
        </div>
      </div>
    </form>
  </div>
</template>
