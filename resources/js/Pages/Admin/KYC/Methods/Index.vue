<script setup lang="ts">
import { ref } from "vue"
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue"
import { Link, useForm } from "@inertiajs/vue3"
import PageHeader from "@/Layouts/Admin/PageHeader.vue"
import moment from "moment"
import OverviewGrid from "@/Components/Admin/OverviewGrid.vue"
import trans from "@/Composables/transComposable"
import NoDataFound from "@/Components/Admin/NoDataFound.vue"
import sharedComposable from "@/Composables/sharedComposable"
const { deleteRow } = sharedComposable()
defineOptions({ layout: AdminLayout })

const props = defineProps([
  "kycMethods",
  "all",
  "active",
  "inactive",
  "KYC_VERIFICATION",
])

const overviewItems = [
  { value: props.all, title: trans("All"), iconClass: "bx bx-list-ul" },
  { value: props.active, title: trans("Active"), iconClass: "bx bx-check" },
  { value: props.inactive, title: trans("Inactive"), iconClass: "bx bx-x" },
]

const form = useForm({
  method: "",
  ids: [],
})

const methods = ref(props.kycMethods)

const submit = () => {
  form.post(route("admin.kyc-methods.mass-destroy"))
}

const selectAll = () => {
  let isAllSelected = form.ids.length == methods.value.data.length

  if (isAllSelected) {
    form.ids = []
  } else {
    form.ids = methods.value.data.map((item) => item.id)
  }
}
</script>

<template>
  <main class="container p-4 sm:p-6">
    <PageHeader />
    <div class="space-y-4">
      <OverviewGrid :items="overviewItems" grid="3" />

      <form @submit.prevent="submit">
        <div class="mb-3 w-72">
          <div class="input-group">
            <select class="select" v-model="form.method">
              <option value="">{{ trans("Select Action") }}</option>
              <option value="delete">{{ trans("Delete Permanently") }}</option>
            </select>
            <button class="btn btn-primary" type="submit" :disabled="!form.ids.length">
              {{ trans("Submit") }}
            </button>
          </div>
        </div>

        <div class="table-responsive whitespace-nowrap rounded-primary">
          <table class="table">
            <thead>
              <tr>
                <th><input type="checkbox" class="checkbox" @change="selectAll" /></th>
                <th>{{ trans("Sl") }}</th>
                <th>{{ trans("Title") }}</th>
                <th>{{ trans("Preview Image") }}</th>
                <th>{{ trans("Accept Attach.") }}</th>
                <th>{{ trans("Created At") }}</th>
                <th>{{ trans("Type") }}</th>
                <th>{{ trans("Status") }}</th>
                <th>{{ trans("Action") }}</th>
              </tr>
            </thead>
            <tbody v-if="kycMethods.data.length > 0">
              <tr v-for="(item, index) in kycMethods.data" :key="item.id">
                <td>
                  <input
                    type="checkbox"
                    v-model="form.ids"
                    class="checkbox"
                    :value="item.id"
                  />
                </td>
                <td>{{ index + 1 }}</td>
                <td>{{ item.title }}</td>
                <td><img v-lazy="item.image" class="h-16" /></td>
                <td>
                  <span v-if="item.image_accept" class="badge badge-success">
                    {{ trans("Yes") }}
                  </span>
                  <span v-else class="badge badge-danger">{{ trans("No") }} </span>
                </td>

                <td>{{ moment(item.created_at).format("DD MMM, YYYY") }}</td>

                <td>
                  <span v-if="item.type" class="badge badge-success"
                    >{{ trans("Identity") }}
                  </span>
                  <span v-else class="badge badge-danger">{{ trans("Address") }} </span>
                </td>

                <td>
                  <span v-if="item.status" class="badge badge-success"
                    >{{ trans("Active") }}
                  </span>
                  <span v-else class="badge badge-danger">{{ trans("Inactive") }} </span>
                </td>

                <td>
                  <div class="flex gap-3">
                    <Link
                      :href="route('admin.kyc-methods.edit', item)"
                      class="btn btn-primary"
                    >
                      <Icon icon="bx:bxs-edit"></Icon>
                    </Link>
                    
                  </div>
                </td>
              </tr>
            </tbody>
            <NoDataFound v-else for-table="true" />
          </table>
          <!-- pagination -->
        </div>
      </form>
    </div>
  </main>
</template>
