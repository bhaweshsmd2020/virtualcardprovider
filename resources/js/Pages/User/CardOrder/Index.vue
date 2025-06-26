<script setup>
import UserLayout from '@/Layouts/User/UserLayout.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import trans from '@/Composables/transComposable'
import Overview from '@/Components/Admin/OverviewGrid.vue'
import Pagination from '@/Components/Admin/Paginate.vue'
import drawer from '@/Plugins/Admin/drawer'
import { onMounted } from 'vue'
import NoDataFound from '@/Components/Admin/NoDataFound.vue'
import FilterDropdown from '@/Components/Admin/FilterDropdown.vue'
import sharedComposable from '@/Composables/sharedComposable'
import moment from 'moment'
import Swal from 'sweetalert2'

defineOptions({ layout: UserLayout })

onMounted(() => {
  drawer.init()

  // Get success message & redirect URL from URL parameters
  const urlParams = new URLSearchParams(window.location.search)
  const successMessage = urlParams.get('success')

  if (successMessage) {
    Swal.fire({
      icon: 'success',
      title: `<h2 style='font-size: 20px;'>${successMessage}</h2>`,      
      html: "<p style='font-size: 15px;'>You will be notify once card will be ready to use with 1-2 days</p>",
      confirmButtonText: 'Okay',
      allowOutsideClick: false,
      customClass: {
        popup: "custom-swal-popup",
      },
      didOpen: (toast) => {
        const closeButton = document.createElement("button");
        closeButton.innerHTML = "&times;";
        closeButton.classList.add("swal-close-btn");
        closeButton.onclick = () => Swal.close();
        toast.appendChild(closeButton);
      },
    });

    // Remove 'success' & 'redirect' parameters from URL after displaying
    const url = new URL(window.location.href)
    url.searchParams.delete('success')
    window.history.replaceState({}, document.title, url)
  }
});

onMounted(() => {
  drawer.init()
})
const { formatCurrency, badgeClass } = sharedComposable()
const props = defineProps([
  'orders',
  'request',
  'totalOrders',
  'totalPendingOrders',
  'totalCompleteOrders',
  'totalDeclinedOrders',
  'type'
])

const orderOverviews = [
  {
    value: props.totalOrders,
    title: trans('Total Orders'),
    iconClass: 'bx bx-badge-check'
  },
  {
    value: props.totalPendingOrders || 0,
    title: trans('Pending Orders'),
    iconClass: 'bx bx-badge'
  },
  {
    value: props.totalCompleteOrders || 0,
    title: trans('Completed Orders'),
    iconClass: 'bx bx-check'
  },
  {
    value: props.totalDeclinedOrders || 0,
    title: trans('Declined Orders'),
    iconClass: 'bx bx-x-circle'
  }
]

const filterForm = [
  {
    value: 'invoice_no',
    label: 'Invoice'
  },
  {
    value: 'amount',
    label: 'Amount'
  },
  {
    label: 'Status',
    options: [
      {
        label: 'Pending',
        value: 'pending'
      },
      {
        label: 'Approved',
        value: 'approved'
      },
      {
        label: 'Rejected',
        value: 'rejected'
      }
    ]
  }
]
</script>

<template>
  <!-- Main Content Starts -->
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader :title="trans('Card Orders')" />

    <div class="space-y-4">
      <Overview :items="orderOverviews" />
      <FilterDropdown :options="filterForm" />
      <div class="table-responsive whitespace-nowrap rounded-primary">
        <!-- <p v-if="props.totalPendingOrders > 0">
          <div class="alert alert-warning mb-4" role="alert">
            Your cards are activated now.<Link :href="'/user/credit-card/activate-cards'" class="alert-link"><strong>Click Here</strong></Link>to update card status.
          </div>
        </p> -->
        <table class="table">
          <thead>
            <tr>
              <th>{{ trans('Order No') }}</th>
              <th>{{ trans('Card Name') }}</th>
              <th>{{ trans('Payment Mode') }}</th>
              <th>{{ trans('Amount') }}</th>
              <th>{{ trans('Status') }}</th>
              <th>{{ trans('Created At') }}</th>
              <th class="!text-right">
                {{ trans('Actions') }}
              </th>
            </tr>
          </thead>
          <tbody v-if="orders.total">
            <tr v-for="order in orders.data" :key="order.id">
              <td>
                <Link
                  :href="'/user/card-orders/' + order.id"
                  class="text-sm font-medium text-primary-500 transition duration-150 ease-in-out hover:underline"
                >
                  {{ order.invoice_no }}
                </Link>
              </td>
              <td>{{ order?.card?.title }}</td>
              <td>{{ order.gateway.name }}</td>
              <td>{{ formatCurrency(order.amount) }}</td>
              <td>
                <div class="capitalize" :class="badgeClass(order.status)">
                  {{ order.status }}
                </div>
              </td>
              <td class="text-center">
                {{ moment(order.created_at).format('DD MMM, YYYY') }}
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
                          <Link :href="'/user/card-orders/' + order.id" class="dropdown-link">
                            <Icon class="h-6" icon="fe:eye" />
                            <span>{{ trans('View') }}</span>
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

      <Pagination :links="orders.links" />
    </div>
  </main>
</template>
