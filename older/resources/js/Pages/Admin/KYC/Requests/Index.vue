<script setup lang="ts">
import { Link, useForm } from "@inertiajs/vue3"
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue"
import Pagination from "@/Components/Admin/Paginate.vue"
import PageHeader from "@/Layouts/Admin/PageHeader.vue"
import OverviewGrid from "@/Components/Admin/OverviewGrid.vue"
import trans from "@/Composables/transComposable"
import { modal } from "@/Composables/actionModalComposable"
import { ref } from "vue"
import NoDataFound from "@/Components/Admin/NoDataFound.vue"
defineOptions({ layout: AdminLayout })

const props = defineProps([
  "requests",
  "all",
  "approved",
  "pending",
  "rejected",
  "reSubmitted",
])

const orderOverviews = [
  { value: props.all, title: trans("All"), iconClass: "bx bx-box" },
  { value: props.pending, title: trans("Pending"), iconClass: "bx bx-hourglass" },
  { value: props.approved, title: trans("Approved"), iconClass: "bx bx-check" },
  { value: props.rejected, title: trans("Rejected"), iconClass: "bx bx-x" },
  { value: props.reSubmitted, title: trans("reSubmitted"), iconClass: "bx bx-repeat" },
]

const filterForm = useForm({})

const massDeleteForm = useForm({})

const filterFormSubmit = () => {
  filterForm.post(window.location.href, {
    onFinish() {},
  })
}

const form = ref({
  request: "",
  ids: [],
})

const reqItems = ref(props.requests)

const submit = () => {
  modal.init(route("admin.kyc-requests.destroy.mass"), "post", form.value, {
    confirm_text: "Are you sure want to do this action ?",
    message: "",
    accept_btn_text: "Yes, Confirm",
    reject_btn_text: "No, Cancel",
    success_message: "Requests has been deleted successfully",
  })
}

const selectAll = () => {
  let isAllSelected = form.value.ids.length == reqItems.value.data.length

  if (isAllSelected) {
    form.value.ids = []
  } else {
    form.value.ids = reqItems.value.data.map((item) => item.id)
  }
}
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader />

    <div class="space-y-4">
      <OverviewGrid
        :items="orderOverviews"
        class="grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5"
      />

      <h4 class="mb-2">{{ trans("KYC Requests") }}</h4>
      <form @submit.prevent="filterFormSubmit">
        <div class="flex justify-between items-center">
          <div class="mb-3 w-72">
            <div class="input-group">
              <select class="select" v-model="form.request">
                <option value="">{{ trans("Select Action") }}</option>
                <option value="delete">{{ trans("Delete Permanently") }}</option>
              </select>
              <button class="btn btn-primary" type="submit" :disabled="!form.ids.length">
                {{ trans("Submit") }}
              </button>
            </div>
          </div>
          <div class="input-group w-72">
            <input
              type="text"
              name="src"
              class="input"
              :placeholder="trans('Search by invoice or user')"
            />
            <button type="submit" class="btn btn-primary btn-icon">
              <Icon icon="bx:search" />
            </button>
          </div>
        </div>
      </form>
    </div>

    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th class="pt-2 text-center">
              <input
                type="checkbox"
                id="select-all"
                class="checkbox"
                @change="selectAll"
              />
            </th>
            <th>{{ trans("Method") }}</th>
            <th>{{ trans("User Name") }}</th>
            <!-- <th>{{ trans("Note") }}</th>
            <th>{{ trans("Documents") }}</th> -->
            <th>{{ trans("Type") }}</th>
            <th>{{ trans("Status") }}</th>
            <th class="text-right">{{ trans("Action") }}</th>
          </tr>
        </thead>
        <tbody v-if="requests.data">
          <tr v-for="item in requests.data" :key="item">
            <td class="text-center">
              <input
                type="checkbox"
                v-model="form.ids"
                :id="`request-${item.id}`"
                class="checkbox"
                :value="item.id"
              />
            </td>
            <td>{{ item.method?.title }}</td>
            <td>
              {{ item.user?.name }}
            </td>            
            <!-- <td>{{ item.note }}</td>
            <td>{{ item.data.length }}</td> -->
            <td>{{ item.type.charAt(0).toUpperCase() + item.type.slice(1) }}</td>
            <td>
              <span v-if="item.status == 0" class="badge badge-warning">{{
                trans("Pending")
              }}</span>
              <span v-else-if="item.status == 1" class="badge badge-primary">{{
                trans("Approved")
              }}</span>
              <span v-else-if="item.status == 2" class="badge badge-danger">{{
                trans("Rejected")
              }}</span>
              <span v-else-if="item.status == 3" class="badge badge-dark">{{
                trans("Re-Submitted")
              }}</span>
            </td>
            <td class="flex gap-x-2">
              <Link
                class="btn btn-primary"
                :href="route('admin.kyc-requests.show', item.id)"
              >
                <Icon icon="bx:show" />
                {{ trans("View") }}
              </Link>
            </td>
          </tr>
        </tbody>
        <NoDataFound v-if="requests.data.length == 0" for-table="true" />
      </table>
      <Pagination :links="requests.links" />
    </div>
  </main>
</template>
