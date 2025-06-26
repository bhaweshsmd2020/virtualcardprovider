<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue"
import PageHeader from "@/Layouts/Admin/PageHeader.vue"
import trans from "@/Composables/transComposable"
import Overview from "@/Components/Admin/OverviewGrid.vue"
import Pagination from "@/Components/Admin/Paginate.vue"
import drawer from "@/Plugins/Admin/drawer"
import { onMounted, ref } from "vue"
import NoDataFound from "@/Components/Admin/NoDataFound.vue"
import FilterDropdown from "@/Components/Admin/FilterDropdown.vue"
import sharedComposable from "@/Composables/sharedComposable"
import moment from "moment"
import { useForm } from "@inertiajs/vue3"
import SpinnerBtn from "@/Components/SpinnerBtn.vue"
import toast from "@/Composables/toastComposable"

defineOptions({ layout: AdminLayout })
onMounted(() => {
  drawer.init()
})
const { formatCurrency, badgeClass } = sharedComposable()
const props = defineProps([
  "orders",
  "request",
  "totalOrders",
  "totalPendingOrders",
  "totalApprovedOrders",
  "totalRejectedOrders",
  "type",
  "buttons",
  "cardholderData",
  "cards",
  "gateways",
  "findUser",
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
    value: props.totalApprovedOrders,
    title: trans("Completed Orders"),
    iconClass: "bx bx-check",
  },
  {
    value: props.totalRejectedOrders,
    title: trans("Declined Orders"),
    iconClass: "bx bx-x-circle",
  },
]

const cardHolderFrom = useForm({
  address_line: props.cardholderData?.address_line,
  city: props.cardholderData?.city,
  postal_code: props.cardholderData?.postal_code,
  state: props.cardholderData?.state,
  country: props.cardholderData?.country,
  phone: props.cardholderData?.phone || "+18888675309",
})

const manualOrderForm = useForm({
  user_id: null,
  card_id: null,
  gateway_id: null,
  payment_id: null,
  amount: 0,
})

const userData = useForm({
  user: "",
})
const isUserSearched = ref(false)
const getUserData = () => {
  isUserSearched.value = true
  userData.get(route("admin.card-orders.index"), {
    preserveState: true,
    onSuccess: () => {
      manualOrderForm.user_id = null
    },
  })
}
const submitOrder = () => {
  manualOrderForm.post(route("admin.card-orders.store"), {
    onSuccess: () => {
      drawer.of("#manualOrderDrawer").hide()
    },
  })
}
function updateOption(key, drawerId) {
  cardHolderFrom.put(route("admin.option.update", key), {
    onSuccess: () => {
      toast.success("Option Updated successfully")
      drawer.of(drawerId).hide()
    },
  })
}
const filterForm = [
  {
    label: "Order No",
    value: "invoice_no",
  },
  {
    label: "Card Title",
    value: "card_title",
  },
  {
    label: "User Email",
    value: "user_email",
  },
  {
    label: "Status",
    value: "status",
    options: [
      {
        label: "Pending",
        value: "pending",
      },
      {
        label: "Approved",
        value: "approved",
      },
      {
        label: "Rejected",
        value: "rejected",
      },
      {
        label: "Active",
        value: "active",
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
      <FilterDropdown :options="filterForm" />
      <div class="table-responsive whitespace-nowrap rounded-primary">
        <table class="table">
          <thead>
            <tr>
              <th>{{ trans("Order No") }}</th>
              <th>{{ trans("Card Name") }}</th>
              <th>{{ trans("Prepaid Card") }}</th>
              <th>{{ trans("User Name") }}</th>
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
                  :href="'/admin/card-orders/' + order.id"
                  class="text-sm font-medium text-primary-500 transition duration-150 ease-in-out hover:underline"
                >
                  {{ order.invoice_no }}
                </Link>
              </td>
              <td>{{ order?.card?.title }}</td>
              <td>
                {{ order?.credit_card?.card || trans("N/A") }}
              </td>
              <td>
                <Link
                  class="underline"
                  :href="route('admin.users.show', order?.user?.id)"
                >
                  {{ order?.user?.name }}</Link
                >
              </td>
              <td>{{ order.gateway.name }}</td>
              <td>{{ formatCurrency(order.amount) }}</td>
              <td>
                <div class="badge capitalize" :class="badgeClass(order.status)">
                  {{ order.status }}
                </div>
              </td>
              <td class="text-center">
                {{ moment(order.created_at).format("DD MMM, YYYY") }}
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
                          <Link
                            :href="'/admin/card-orders/' + order.id"
                            class="dropdown-link"
                          >
                            <Icon class="h-6" icon="fe:eye" />
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

  <div id="cardholderDataDrawer" class="drawer drawer-right">
    <form
      method="POST"
      @submit.prevent="updateOption('cardholder_data', '#cardholderDataDrawer')"
    >
      <div class="drawer-header">
        <h5>{{ trans("Edit Cardholder Info") }}</h5>
        <button
          type="button"
          class="btn btn-plain-secondary dark:text-slate-300 dark:hover:bg-slate-700 dark:focus:bg-slate-700"
          data-dismiss="drawer"
        >
          <i data-feather="x" width="1.5rem" height="1.5rem"></i>
        </button>
      </div>
      <div class="drawer-body space-y-2">
        <div>
          <label class="label mb-1">{{ trans("Phone Number") }}</label>
          <input type="tel" v-model="cardHolderFrom.phone" class="input" required />
        </div>
        <div>
          <label class="label mb-1">{{ trans("Address Line") }}</label>
          <input
            type="text"
            v-model="cardHolderFrom.address_line"
            class="input"
            required
          />
        </div>
        <div>
          <label class="label mb-1">{{ trans("Country") }}</label>
          <input type="text" v-model="cardHolderFrom.country" class="input" required />
        </div>
        <div>
          <label class="label mb-1">{{ trans("City") }}</label>
          <input type="text" v-model="cardHolderFrom.city" class="input" required />
        </div>
        <div>
          <label class="label mb-1">{{ trans("Postal Code") }}</label>
          <input type="text" v-model="cardHolderFrom.postal_code" class="input" required=
          />
        </div>
        <div>
          <label class="label mb-1">{{ trans("State") }}</label>
          <input type="text" v-model="cardHolderFrom.state" class="input" required />
        </div>
      </div>
      <div class="drawer-footer">
        <div class="flex justify-between gap-2">
          <button type="button" class="btn btn-secondary w-full" data-dismiss="drawer">
            <span> {{ trans("Close") }}</span>
          </button>

          <SpinnerBtn
            classes="btn btn-primary w-full"
            :processing="cardHolderFrom.processing"
            btn-text="Update"
          />
        </div>
      </div>
    </form>
  </div>
  <div id="manualOrderDrawer" class="drawer drawer-right">
    <form method="POST" @submit.prevent="submitOrder">
      <div class="drawer-header">
        <h5>{{ trans("Create Manual Card Order") }}</h5>
        <button type="button" class="btn btn-danger" data-dismiss="drawer">
          <i class="bx bx-x text-lg"></i>
        </button>
      </div>
      <div class="drawer-body space-y-2">
        <div class="flex items-end gap-2">
          <div>
            <label class="label mb-1">{{ trans("Email/Phone") }}</label>
            <input type="text" v-model="userData.user" class="input" required />
          </div>
          <button type="button" class="btn btn-primary" @click="getUserData">
            {{ trans("Find User") }}
          </button>
        </div>
        <p v-if="isUserSearched && !findUser" class="text-sm text-red-500">
          {{ trans("Nothing Found") }}...
        </p>
        <button
          type="button"
          @click="manualOrderForm.user_id = findUser.id"
          class="mt-1 w-full space-y-1 border border-transparent bg-gray-100 p-2 text-left text-xs shadow-md dark:bg-gray-900"
          v-if="findUser"
          :class="{ 'border-primary-600': manualOrderForm.user_id }"
        >
          <p>{{ trans("Name") }}: {{ findUser?.name }}</p>
          <p>{{ trans("Email") }}: {{ findUser?.email }}</p>
          <p>{{ trans("Phone") }}: {{ findUser?.phone }}</p>
        </button>
        <div>
          <label class="label mb-1">{{ trans("Select Gateway") }}</label>
          <select class="select capitalize" v-model="manualOrderForm.gateway_id">
            <option value="null" selected disabled>Select</option>
            <option :value="gateway.id" v-for="gateway in gateways" :key="gateway.id">
              {{ gateway.name }}
            </option>
          </select>
        </div>

        <div>
          <label class="label mb-1">{{ trans("Select Card") }}</label>
          <select class="select capitalize" v-model="manualOrderForm.card_id">
            <option value="null" selected disabled>{{ trans("Select") }}</option>
            <option :value="card.id" v-for="card in cards" :key="card.id">
              {{ card.title }} - {{ card.category.title }}
            </option>
          </select>
        </div>
        <div>
          <label class="label mb-1">{{ trans("Amount") }}</label>
          <input type="number" v-model="manualOrderForm.amount" class="input" required />
        </div>
        <div>
          <label class="label mb-1">{{ trans("Payment Id") }}</label>
          <input
            type="text"
            v-model="manualOrderForm.payment_id"
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
            :processing="cardHolderFrom.processing"
            btn-text="Order"
          />
        </div>
      </div>
    </form>
  </div>
</template>
