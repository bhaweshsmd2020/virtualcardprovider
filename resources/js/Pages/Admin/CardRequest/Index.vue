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
  "limits"
])

const emailForm = useForm({
  status: 1,
})

const editForm = ref({})

const openEditLimitDrawer = (limits) => {
  editForm.value = { ...limits }
  drawer.of("#editEmailDrawer").show()
}

const updateEmail = () => {
  router.post(route("admin.card.limit-requests.update", editForm.value.id), editForm.value, {
    onSuccess: () => {
      editForm.value = {}
      drawer.of("#editEmailDrawer").hide()
    },
  })
}

const filterOptions = [
  {
    label: 'Status',
    value: 'status',
    options: [
      {
        label: 'Pending',
        value: '0'
      },
      {
        label: 'Approved',
        value: '1'
      },
      {
        label: 'Rejected',
        value: '2'
      }
    ]
  }
]
</script>

<template>
  <main class="container p-4 sm:p-6">
    <PageHeader title="Card Limit Requests" :buttons="buttons" />
    <div class="space-y-6">
      <FilterDropdown :options="filterOptions" />

      <div class="table-responsive whitespace-nowrap rounded-primary">
        <table class="table">
          <thead>
            <tr>
              <th>{{ trans('User') }}</th>
              <th>{{ trans('Card') }}</th>
              <th>{{ trans('Limit') }}</th>
              <th>{{ trans('Status') }}</th>
              <th>{{ trans('Created At') }}</th>
              <th class="flex justify-end">{{ trans('Action') }}</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="limits in limits.data" :key="limits.id">
              <td>
                <Link
                  class="underline"
                  :href="route('admin.users.show', limits.user.id)"
                >
                  {{ limits.user.name }}</Link
                >
              </td>
              <td>
                <Link
                  class="underline"
                  :href="'/admin/credit-cards/' + limits.credit_card.id"
                >
                  {{ limits.credit_card.last4 }}</Link
                >
              </td>
              <td>
                {{ limits.subtotal }}
              </td>
              <td>
                <span 
                  v-if="limits.status == '0'" 
                  class="badge badge-primary">
                  {{ trans('Pending') }}
                </span>
                <span 
                  v-else-if="limits.status == '1'" 
                  class="badge badge-success">
                  {{ trans('Approved') }}
                </span>
                <span 
                  v-else-if="limits.status == '2'" 
                  class="badge badge-danger">
                  {{ trans('Rejected') }}
                </span>
              </td>
              <td>
                {{ moment(limits.created_at).format('D-MMM-Y') }}
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
                            @click="openEditLimitDrawer(limits)"
                            class="dropdown-link"
                          >
                            <Icon
                              class="h-6 text-slate-400"
                              icon="material-symbols:edit-outline"
                            />
                            <span>{{ trans("Edit") }}</span>
                          </button>
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
      <Paginate :links="limits.links" />
    </div>
  </main>

  <div id="editEmailDrawer" class="drawer drawer-right">
    <form @submit.prevent="updateEmail()">
      <div class="drawer-header">
        <h5>{{ trans("Edit Card Limit Requests") }}</h5>
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
          <label>{{ trans("Status") }}</label>
          <select v-model="editForm.status" class="select" name="status">
            <option value="0">{{ trans("Pending") }}</option>
            <option value="1">{{ trans("Approve") }}</option>
            <option value="2">{{ trans("Reject") }}</option>
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
