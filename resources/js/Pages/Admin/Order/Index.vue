<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue"
import PageHeader from "@/Layouts/Admin/PageHeader.vue"
import { useForm } from "@inertiajs/vue3"
import trans from "@/Composables/transComposable"
import Overview from "@/Components/Admin/OverviewGrid.vue"
import Pagination from "@/Components/Admin/Paginate.vue"
import drawer from "@/Plugins/Admin/drawer"
import toast from "@/Composables/toastComposable"
import { onMounted } from "vue"
import SpinnerBtn from "@/Components/SpinnerBtn.vue"
import NoDataFound from "@/Components/Admin/NoDataFound.vue"
import FilterDropdown from "@/Components/Admin/FilterDropdown.vue"
import sharedComposable from "@/Composables/sharedComposable"

defineOptions({ layout: AdminLayout })

onMounted(() => {
  drawer.init()
})
const { formatCurrency } = sharedComposable()
const props = defineProps([
  "buttons",
  "orders",
  "request",
  "totalOrders",
  "totalPendingOrders",
  "totalCompleteOrders",
  "totalDeclinedOrders",
  "type",
  "currency",
  "invoice",
  "tax",
])

const orderOverviews = [
  {
    value: props.totalOrders,
    title: trans("Total Orders"),
    iconClass: "bx bx-badge-check",
  },
  {
    value: props.totalPendingOrders,
    title: trans("Pending Orders"),
    iconClass: "bx bx-badge",
  },
  {
    value: props.totalCompleteOrders,
    title: trans("Approved Orders"),
    iconClass: "bx bx-check",
  },
  {
    value: props.totalDeclinedOrders,
    title: trans("Rejected Orders"),
    iconClass: "bx bx-x-circle",
  },
]

const invoiceFrom = useForm({
  company_name: props.invoice.company_name,
  address: props.invoice.address,
  city: props.invoice.city,
  post_code: props.invoice.post_code,
  country: props.invoice.country,
})

const taxFrom = useForm({
  tax: props.tax,
})

function updateOption(form, key, drawerId) {
  form.put(route("admin.option.update", key), {
    onSuccess: () => {
      toast.success("Option Updated successfully")
      drawer.of(drawerId).hide()
    },
  })
}

const currencyFrom = useForm({
  name: props.currency.name,
  icon: props.currency.icon,
  position: props.currency.position,
});

const filterOptions = [
  {
    value: "invoice_no",
    label: "Order no",
  },
  {
    value: "user_email",
    label: "User Email",
  },
  {
    label: "Status",
    value: "status",
    options: [
      {
        label: "Approved",
        value: 'approved',
      },
      {
        label: "Pending",
        value: 'pending',
      },
      {
        label: "Rejected",
        value: 'rejected',
      },
    ],
  },
]
</script>

<template>
  <!-- Main Content Starts -->
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader :title="trans('Order List')" :buttons="buttons" />

    <div class="space-y-4">
      <Overview :items="orderOverviews" />
      <FilterDropdown :options="filterOptions" />
      <div class="table-responsive whitespace-nowrap rounded-primary">
        <table class="table">
          <thead>
            <tr>
              <th>{{ trans("Order No") }}</th>
              <th>{{ trans("Plan Name") }}</th>
              <th>{{ trans("Payment Mode") }}</th>
              <th>{{ trans("Amount") }}</th>
              <th>{{ trans("Status") }}</th>
              <th>{{ trans("Created At") }}</th>
              <th class="!text-right">
                {{ trans("Actions") }}
              </th>
            </tr>
          </thead>
          <tbody v-if="orders.total">
            <tr v-for="order in orders.data" :key="order.id">
              <td>
                <Link
                  :href="'/admin/order/' + order.id"
                  class="text-sm font-medium text-primary-500 transition duration-150 ease-in-out hover:underline"
                >
                  {{ order.invoice_no }}
                </Link>
              </td>
              <td>{{ order.plan.title }}</td>
              <td>{{ order.gateway.name }}</td>
              <td>{{ formatCurrency(order.amount) }}</td>
              <td>
                <div class="badge badge-soft-primary capitalize">
                  {{
                    trans(
                      order.status
                    )
                  }}
                </div>
              </td>
              <td class="text-center">
                {{ order.created_at_diff }}
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
                          <Link :href="'/admin/order/' + order.id" class="dropdown-link">
                            <i
                              class="h-5 text-slate-400"
                              data-feather="external-link"
                            ></i>
                            <span>{{ trans("View") }}</span>
                          </Link>
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

      <Pagination v-if="orders.data.length != 0" :links="orders.links" />
    </div>
  </main>
  <!-- Main Content Ends -->

  <div id="invoiceSettingDrawer" class="drawer drawer-right">
    <form
      method="POST"
      @submit.prevent="updateOption(invoiceFrom, 'invoice_data', '#invoiceSettingDrawer')"
    >
      <div class="drawer-header">
        <h5>{{ trans("Edit Invoice Information") }}</h5>
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
          <label>{{ trans("Company Name") }}</label>
          <input
            type="text"
            v-model="invoiceFrom.company_name"
            class="input"
            required=""
          />
        </div>
        <div class="mb-2">
          <label>{{ trans("Company Address") }}</label>
          <input
            type="text"
            name="data[address]"
            v-model="invoiceFrom.address"
            class="input"
            required=""
          />
        </div>
        <div class="mb-2">
          <label>{{ trans("Company City") }}</label>
          <input
            type="text"
            name="data[city]"
            v-model="invoiceFrom.city"
            class="input"
            required=""
          />
        </div>
        <div class="mb-2">
          <label>{{ trans("Post Code") }}</label>
          <input
            type="text"
            name="data[post_code]"
            v-model="invoiceFrom.post_code"
            class="input"
            required=""
          />
        </div>
        <div class="mb-2">
          <label>{{ trans("Country") }}</label>
          <input
            type="text"
            name="data[country]"
            v-model="invoiceFrom.country"
            class="input"
            required=""
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
            :processing="invoiceFrom.processing"
            btn-text="Update"
          />
        </div>
      </div>
    </form>
  </div>

  <div id="taxSettingDrawer" class="drawer drawer-right">
    <form
      method="POST"
      @submit.prevent="updateOption(taxFrom, 'tax', '#taxSettingDrawer')"
    >
      <div class="drawer-header">
        <h5>{{ trans("Tax Settings") }}</h5>
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
          <label>{{ trans("Tax Amount") }}</label>
          <input
            type="number"
            step="any"
            name="data"
            v-model="taxFrom.tax"
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
            :processing="taxFrom.processing"
            :btn-text="trans('Save Changes')"
          />
        </div>
      </div>
    </form>
    <div class="drawer-backdrop"></div>
  </div>


  <div id="currencySettingDrawer" class="drawer drawer-right">
    <form
      method="POST"
      @submit.prevent="
        updateOption(currencyFrom, 'base_currency', '#currencySettingDrawer')
      "
    >
      <div class="drawer-header">
        <h5>{{ trans("Currency Settings") }}</h5>
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
          <label>{{ trans("Currency Name") }}</label>
          <input
            type="text"
            name="data[name]"
            v-model="currencyFrom.name"
            class="input"
            required=""
          />
        </div>
        <div class="mb-2">
          <label>{{ trans("Currency Icon") }}</label>
          <input
            type="text"
            name="data[icon]"
            v-model="currencyFrom.icon"
            class="input"
            required=""
          />
        </div>
        <div class="mb-2">
          <label>{{ trans("Currency Icon") }}</label>
          <select class="select" name="data[position]" v-model="currencyFrom.position">
            <option value="left">
              {{ trans("Left") }}
            </option>
            <option value="right">
              {{ trans("Right") }}
            </option>
          </select>
        </div>
      </div>
      <div class="drawer-footer">
        <div class="flex justify-between gap-2">
          <button type="button" class="w-full btn btn-secondary" data-dismiss="drawer">
            <span> {{ trans("Close") }}</span>
          </button>
          <SpinnerBtn
            classes="btn btn-primary w-full"
            :processing="currencyFrom.processing"
            :btn-text="trans('Save Changes')"
          />
        </div>
      </div>
    </form>
  </div>
</template>
