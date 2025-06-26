<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue"
import PageHeader from "@/Layouts/Admin/PageHeader.vue"
import trans from "@/Composables/transComposable"
import Overview from "@/Components/Admin/OverviewGrid.vue"
import Pagination from "@/Components/Admin/Paginate.vue"
import NoDataFound from "@/Components/Admin/NoDataFound.vue"
import FilterDropdown from "@/Components/Admin/FilterDropdown.vue"
import sharedComposable from "@/Composables/sharedComposable"
import moment from "moment"
import drawer from "@/Plugins/Admin/drawer"
import { useForm } from "@inertiajs/vue3"
import toast from "@/Composables/toastComposable"
import SpinnerBtn from "@/Components/SpinnerBtn.vue"
import { onMounted } from "vue"

defineOptions({ layout: AdminLayout })
onMounted(() => {
  drawer.init()
})
const { formatCurrency } = sharedComposable()
const props = defineProps([
  "segments",
  "buttons",
  "transferTransactions",
  "request",
  "totalTransferTransaction",
  "totalServiceFee",
  "todayTransfers",
  "transfer_details",
  "type",
])

const transferOverviews = [
  {
    value: props.totalTransferTransaction,
    title: trans("Total"),
    iconClass: "bx bx-badge-check",
  },
  {
    value: formatCurrency(props.totalServiceFee || 0),
    title: trans("Total Service Fee"),
    iconClass: "bx bx-dollar",
  },
  {
    value: props.todayTransfers,
    title: trans("Today's Transfers"),
    iconClass: "bx bx-calendar",
  },
]

const filterOptions = [
  {
    label: "ID",
    value: "id",
  },
  {
    label: "Amount",
    value: "from_amount",
  },
  {
    label: "User Email",
    value: "user_email",
  },
]

const form = useForm({
  title: props.transfer_details?.name,
  items: props.transfer_details?.items ?? [],
})
const defaultExtraData = {
  value: "",
}

const addItem = () => {
  form.items.push({ ...defaultExtraData })
}

const removeItem = (index) => {
  form.items.splice(index, 1)
}
function updateOption(form, key, drawerId) {
  form.put(route("admin.option.update", key), {
    onSuccess: () => {
      toast.success("Option Updated successfully")
      drawer.of(drawerId).hide()
    },
  })
}
</script>

<template>
  <!-- Main Content Starts -->
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader :title="trans('Transfer List')" :buttons="buttons" />

    <div class="space-y-4">
      <Overview :grid="3" :items="transferOverviews" />
      <FilterDropdown :options="filterOptions" />
      <div class="table-responsive whitespace-nowrap rounded-primary">
        <table class="table">
          <thead>
            <tr>
              <th>{{ trans("ID") }}</th>
              <th>{{ trans("User Name") }}</th>
              <th>{{ trans("Transfer To") }}</th>
              <th>{{ trans("Request Amount") }}</th>
              <th>{{ trans("Converted Amount") }}</th>

              <th>{{ trans("Service Fee") }}</th>
              <th class="!text-right">{{ trans("Created At") }}</th>
            </tr>
          </thead>
          <tbody v-if="transferTransactions.total">
            <tr v-for="transfer in transferTransactions.data" :key="transfer.id">
              <td>
                {{ transfer.id }}
              </td>
              <td>
                <Link
                  class="text-primary-500 hover:underline"
                  :href="route('admin.users.show', transfer?.user_id)"
                >
                  {{ transfer?.user?.name }}
                </Link>
              </td>
              <td>{{ transfer.transfer_to || "balance" }}</td>
              <td>
                {{ transfer.from_amount }}
                <span class="text-xs uppercase">{{ transfer.default_currency }}</span>
              </td>
              <td>
                {{ transfer.to_amount }}
                <span class="text-xs uppercase">{{ transfer.transfer_currency }}</span>
              </td>

              <td>{{ formatCurrency(+transfer.service_fee || 0) }}</td>
              <td class="!text-right">
                {{ moment(transfer.created_at).format("DD MMM, YYYY") }}
              </td>
            </tr>
          </tbody>
          <NoDataFound v-else for-table="true" />
        </table>
      </div>

      <Pagination :links="transferTransactions.links" />
    </div>
  </main>
  <div id="infoDrawer" class="drawer drawer-right">
    <form
      method="POST"
      @submit.prevent="updateOption(form, 'transfer_details', '#infoDrawer')"
    >
      <div class="drawer-header">
        <h5>{{ trans("Conversion Details") }}</h5>
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
          <label class="label mb-2 block font-semibold">{{ trans("Title") }}</label>
          <input type="text" class="input" placeholder="Title" v-model="form.title" />
          <label class="label my-2 block font-semibold">{{ trans("Add Details") }}</label>
          <div
            class="mb-2 flex items-center gap-x-2"
            v-for="(item, index) in form.items"
            :key="index"
          >
            <input type="text" class="input" placeholder="detail" v-model="item.value" />

            <button type="button" @click="removeItem(index)" class="btn btn-danger">
              <Icon icon="bx:x" class="text-lg" />
            </button>
          </div>
          <button type="button" @click="addItem" class="btn btn-primary">
            <Icon icon="bx:plus" class="text-lg" />
          </button>
        </div>
      </div>
      <div class="drawer-footer">
        <div class="flex justify-between gap-2">
          <button type="button" class="btn btn-secondary w-full" data-dismiss="drawer">
            <span>{{ trans("Cancel") }}</span>
          </button>

          <SpinnerBtn
            :processing="form.processing"
            classes="w-full btn btn-primary flex items-center"
            :btn-text="trans('Submit')"
          />
        </div>
      </div>
    </form>
    <div class="drawer-backdrop"></div>
  </div>
</template>
