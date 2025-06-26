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
  const buttonMessage = urlParams.get('buttonmes')
  const redirectUrl = urlParams.get('redirect') || '/user/account' // Default to account if missing

  if (successMessage) {
    Swal.fire({
      icon: 'success',
      title: `<h2 style='font-size: 20px;'>${successMessage}</h2>`,
      confirmButtonText: `${buttonMessage}`,
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
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = redirectUrl;
      }
    });

    // Remove 'success' & 'redirect' parameters from URL after displaying
    const url = new URL(window.location.href)
    url.searchParams.delete('success')
    url.searchParams.delete('redirect')
    window.history.replaceState({}, document.title, url)
  }

  const errorMessage = urlParams.get('error')

  if (errorMessage) {
    Swal.fire({
      icon: 'error',
      title: `<h2 style='font-size: 20px;'>${errorMessage}</h2>`,
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

    // Remove 'success' parameter from URL after displaying
    const url = new URL(window.location.href)
    url.searchParams.delete('error')
    window.history.replaceState({}, document.title, url)
  }
});

onMounted(() => {
  drawer.init()
})
const { formatCurrency, trim, badgeClass } = sharedComposable()
const props = defineProps([
  'segments',
  'topUps',
  'request',
  'totalTopUps',
  'totalAmount',
  'totalApprovedTopUps',
  'totalRejectedTopUps',
  'type'
])

const orderOverviews = [
  {
    value: props.totalTopUps,
    title: trans('Total'),
    iconClass: 'bx bx-badge-check'
  },
  {
    value: formatCurrency(props.totalAmount || 0),
    title: trans('Total Amount'),
    iconClass: 'bx bx-dollar-circle'
  },
  {
    value: props.totalApprovedTopUps,
    title: trans('Completed Topup'),
    iconClass: 'bx bx-check'
  },
  {
    value: props.totalRejectedTopUps,
    title: trans('Declined Topup'),
    iconClass: 'bx bx-x-circle'
  }
]

const filterForm = [
  {
    value: 'invoice_no',
    label: 'invoice'
  },
  {
    value: 'amount',
    label: 'Amount'
  },
  {
    label: 'Status',
    value: 'status',
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
    <PageHeader :title="trans('Card Topup History')" :buttons="buttons" />

    <div class="space-y-4">
      <Overview :items="orderOverviews" />
      <FilterDropdown :options="filterForm" />
      <div class="table-responsive whitespace-nowrap rounded-primary">
        <table class="table">
          <thead>
            <tr>
              <th>{{ trans('Invoice') }}</th>
              <th>{{ trans('Payment Method') }}</th>
              <th>{{ trans('For') }}</th>
              <th>{{ trans('Amount') }}</th>

              <th>{{ trans('Status') }}</th>
              <th>{{ trans('Created At') }}</th>
              <th class="!text-right">{{ trans('Action') }}</th>
            </tr>
          </thead>
          <tbody v-if="topUps.total">
            <tr v-for="topUp in topUps.data" :key="topUp.id">
              <td>
                <Link
                  :href="'/user/credit-card/topupdetails/' + topUp.id"
                  class="text-sm font-medium text-primary-500 transition duration-150 ease-in-out hover:underline"
                >
                  {{ topUp.invoice_no }}
                </Link>
              </td>
              <td>{{ topUp?.gateway?.name || 'balance' }}</td>
              <td class="capitalize">{{ trim(topUp.type) }}</td>
              <td>{{ formatCurrency(topUp.amount) }}</td>

              <td>
                <div class="capitalize" :class="badgeClass(topUp.status)">
                  {{ topUp.status }}
                </div>
              </td>
              <td>
                {{ moment(topUp.created_at).format('DD MMM, YYYY') }}
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
                          <Link :href="'/user/credit-card/topupdetails/' + topUp.id" class="dropdown-link">
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

      <Pagination :links="topUps.links" />
    </div>
  </main>
</template>
