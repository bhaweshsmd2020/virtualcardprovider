<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue"
import PageHeader from "@/Layouts/Admin/PageHeader.vue"
import { useForm, router } from "@inertiajs/vue3"
import Pagination from "@/Components/Admin/Paginate.vue"
import moment from "moment"
import trans from "@/Composables/transComposable"
import sharedComposable from "@/Composables/sharedComposable"
import Overview from "@/Components/Admin/OverviewGrid.vue"
import SpinnerBtn from "@/Components/SpinnerBtn.vue"
import drawer from "@/Plugins/Admin/drawer"
import { onMounted, computed } from "vue"
import NoDataFound from "@/Components/Admin/NoDataFound.vue"
import FilterDropdown from "@/Components/Admin/FilterDropdown.vue"

defineOptions({ layout: AdminLayout })
const { deleteRow, formatCurrency, uiAvatar } = sharedComposable()

onMounted(() => {
  drawer.init()
})

const props = defineProps(["users", "plans","totalUsers","activeUsers","inactiveUsers"])
const userStats = computed(() => {
  return [
    {
      value: props.totalUsers,
      title: trans("Total Users"),
      iconClass: "bx bx-badge",
    },
    {
      value: props.activeUsers,
      title: trans("Active Users"),
      iconClass: "bx bx-badge-check",
    },
    {
      value: props.inactiveUsers,
      title: trans("Inactive Users"),
      iconClass: "bx bx-x-circle",
    },
  ]
})

const editForm = useForm({
  user_id: null,
  name: "",
  email: "",
  plan_id: "",
  will_expire: "",
  assign_plan: true,
})

const openEditUserDrawer = (user) => {
  editForm.reset()

  editForm.user_id = user.id
  editForm.name = user.name
  editForm.email = user.email
  editForm.plan_id = user.plan_id ?? ""
  editForm.will_expire = user.will_expire ?? ""

  drawer.of("#editUserDrawer").show()
}

const updateUser = () => {
  editForm.put(route("admin.users.assign.plan", editForm.user_id), {
    onSuccess: () => {
      editForm.reset()
      drawer.of("#editUserDrawer").hide()
    },
  })
}

const filterOptions = [
  {
    label: "Name",
    value: "name",
  },
  {
    label: "Email",
    value: "email",
  },
  {
    label: "Status",
    value: "status",
    options: [
      {
        label: "Active",
        value: 1,
      },
      {
        label: "Inactive",
        value: 0,
      },
    ],
  },
]
</script>

<template>
  <main class="container p-4 sm:p-6">
    <PageHeader title="Users" />
    <div class="space-y-6">
      <Overview :items="userStats" grid="3" />
      <FilterDropdown :options="filterOptions" />

      <div class="table-responsive whitespace-nowrap rounded-primary">
        <table class="table">
          <thead>
            <tr>
              <th>{{ trans("User") }}</th>
              <th>{{ trans("Assigned To") }}</th>
              <th>{{ trans("Cardholder ID") }}</th>
              <th>{{ trans("Plan") }}</th>
              <th>{{ trans("Expire At") }}</th>
              <th>{{ trans("Status") }}</th>
              <th>{{ trans("Created At") }}</th>
              <th>
                <div class="text-end">{{ trans("Action") }}</div>
              </th>
            </tr>
          </thead>

          <tbody v-if="users.total">
            <tr v-for="user in users.data" :key="user.id">
              <td class="text-left">
                <div class="flex gap-2">
                  <img
                    class="mr-1 inline w-12 rounded-full"
                    v-lazy="uiAvatar(user.name, user.avatar)"
                    alt="preview"
                  />
                  <a :href="route('admin.users.show', user.id)">
                    <p class="font-bold">
                      {{ user.name }}
                    </p>
                    <p>
                      {{ user.email }}
                    </p>
                  </a>
                </div>
              </td>
              <td>{{ user.assignedemails?.email || trans("N/A") }}</td>
              <td>{{ user.cardholder?.cardholder || trans("N/A") }}</td>
              <td>
                <span v-if="user.plan" class="badge badge-primary">
                  {{ user.plan.title }}</span
                >
                <template v-else> N/A </template>
              </td>
              <td>
                {{
                  user.will_expire
                    ? moment(user.will_expire).format("D-MMM-Y")
                    : trans("N/A")
                }}
              </td>

              <td class="text-left">
                <span
                  class="badge"
                  :class="user.status == 1 ? 'badge-success' : 'badge-danger'"
                >
                  {{ user.status == 1 ? trans("Active") : trans("Inactive") }}
                </span>
              </td>

              <td>
                {{ moment(user.created_at).format("D-MMM-Y") }}
              </td>
              <td class="">
                <div class="dropdown" data-placement="bottom-start">
                  <div class="dropdown-toggle">
                    <Icon class="w-30 text-lg" icon="bi:three-dots-vertical" />
                  </div>
                  <div class="dropdown-content w-40">
                    <ul class="dropdown-list">
                      <li class="dropdown-list-item">
                        <Link
                          :href="route('admin.users.show', user)"
                          class="dropdown-link"
                        >
                          <Icon icon="bx:show"></Icon>
                          <span>{{ trans("View") }}</span>
                        </Link>
                      </li>

                      <li class="dropdown-list-item">
                        <button @click="openEditUserDrawer(user)" class="dropdown-link">
                          <Icon icon="bx:user-pin"></Icon>
                          <span>{{ trans("Assign Plan") }}</span>
                        </button>
                      </li>

                      <li class="dropdown-list-item">
                        <Link
                          :href="route('admin.users.edit', user)"
                          class="dropdown-link"
                        >
                          <Icon icon="bx:edit"></Icon>
                          <span>{{ trans("Edit") }}</span>
                        </Link>
                      </li>

                      <li class="dropdown-list-item">
                        <button
                          as="button"
                          class="dropdown-link"
                          @click="deleteRow(route('admin.users.destroy', user.id))"
                        >
                          <Icon class="h-6" icon="bx:trash" />
                          <span>{{ trans("Delete") }}</span>
                        </button>
                      </li>
                    </ul>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
          <NoDataFound v-else for-table="true" />
        </table>

        <Pagination :links="users.links" />
      </div>
    </div>
  </main>

  <div id="editUserDrawer" class="drawer drawer-right">
    <form @submit.prevent="updateUser()">
      <div class="drawer-header">
        <h5>{{ trans("Edit User Information") }}</h5>
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
          <label> {{ trans("Name") }}</label>
          <input type="text" class="input" disabled v-model="editForm.name" />
        </div>
        <div class="mb-2">
          <label>{{ trans("Email") }}</label>
          <input type="email" class="input" disabled v-model="editForm.email" />
        </div>

        <div class="mb-2">
          <label class="label mb-2">{{ trans("Plan") }}</label>
          <select v-model="editForm.plan_id" class="select">
            <option value="">{{ trans("Select Plan") }}</option>
            <option v-for="plan in plans" :value="plan.id" :key="plan.id">
              {{ plan.title }} {{ formatCurrency(plan.price) }}
            </option>
          </select>
        </div>

        <div class="mb-2">
          <label>{{ trans("Will Expire") }}</label>
          <input type="date" class="input" v-model="editForm.will_expire" />
        </div>
      </div>

      <div class="drawer-footer">
        <div class="flex gap-2">
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
