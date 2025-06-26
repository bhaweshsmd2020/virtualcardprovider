<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import trans from '@/Composables/transComposable'
import Overview from '@/Components/Admin/OverviewGrid.vue'
import Pagination from '@/Components/Admin/Paginate.vue'
import NoDataFound from '@/Components/Admin/NoDataFound.vue'
import sharedComposable from '@/Composables/sharedComposable'
import WalletCard from '@/Components/User/WalletCard.vue'
import moment from 'moment'
const { deleteRow, formatCurrency, textExcerpt, uiAvatar, badgeClass } = sharedComposable()

defineOptions({ layout: AdminLayout })
const props = defineProps([
  'user',
  'orders',
  'card_orders',
  'totalOrders',
  'totalPendingOrders',
  'totalCompleteOrders',
  'totalDeclinedOrders',
  'accounts',
  'lastLogin',
  'othercity',
  'otherstate'
])

const userStats = [
  {
    value: props.totalOrders,
    title: trans('Total Orders'),
    iconClass: 'bx bx-badge-check'
  },
  {
    value: props.totalPendingOrders,
    title: trans('Pending Orders'),
    iconClass: 'bx bx-badge'
  },
  {
    value: props.totalCompleteOrders,
    title: trans('Completed Orders'),
    iconClass: 'bx bx-check'
  },
  {
    value: props.totalDeclinedOrders,
    title: trans('Declined Orders'),
    iconClass: 'bx bx-x-circle'
  }
]
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader title="User details" />
    <div class="space-y-6">
      <Overview :items="userStats" />
      <section class="grid grid-cols-1 gap-8 sm:grid-cols-2 xl:grid-cols-4">
        <WalletCard :action-buttons="false" :accounts="accounts" />
      </section>
      <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
        <!-- Left Section Start  -->
        <section class="card card-body col-span-1 flex flex-col gap-2">
          <!-- User Avatar & Status  -->
          <div class="flex flex-col items-center">
            <div class="relative my-2 h-20 w-20 rounded-full">
              <img
                v-lazy="uiAvatar(user.name, user.avatar)"
                alt="avatar-img"
                class="h-full w-full rounded-full"
              />
            </div>

            <div class="text-sm">
              <p>
                {{ trans('Status: ') }}
                <span
                  :class="
                    user.status == 1
                      ? 'badge badge-success badge-sm'
                      : 'badge badge-danger badge-sm'
                  "
                >
                  <small>{{ user.status == 1 ? 'Active' : 'Suspended' }}</small>
                </span>
              </p>
              <p>{{ trans('Join Date: ') }} {{ user.created_at_date }}</p>
              <p>{{ trans('Last Login Date: ') }} {{ lastLogin?.login_at || 'N/A' }}</p>
              <p>{{ trans('Last Login Location: ') }} {{ lastLogin?.country || 'N/A' }}</p>
              <p>{{ trans('Last Login IP: ') }} {{ lastLogin?.ip_address || 'N/A' }}</p>
            </div>
          </div>
        </section>

        <!-- Right Section Start  -->
        <section class="card card-body col-span-1 space-y-3">
          <h5 class="border-b border-gray-700 pb-1 text-lg">
            {{ trans('Information') }}
          </h5>
          <div class="space-y-1 text-sm">
            <p>{{ trans('Balance : ') }} {{ formatCurrency(user?.balance || 0) }}</p>
            <p>{{ trans('Name : ') }} {{ user.name }}</p>
            <p>{{ trans('Username : ') }} {{ user?.username }}</p>
            <p>{{ trans('Email : ') }} {{ user?.email || 'N/A' }}</p>
            <p>{{ trans('Phone : ') }} {{ user?.phone || 'N/A' }}</p>
          </div>
        </section>
        <section class="card card-body col-span-1 space-y-3">
          <h5 class="border-b border-gray-700 pb-1 text-lg">
            {{ trans('Address') }}
          </h5>
          <div class="space-y-1 text-sm">
            <p>{{ trans('Country : ') }} {{ user?.country.name || 'N/A' }}</p>
            <p>
              {{ trans('State : ') }}
              {{
                user.state_id === null
                  ? 'N/A'
                  : user.state_id === 1000
                  ? (otherstate?.name || 'N/A')
                  : (user?.state?.name || 'N/A')
              }}
            </p>
            <p>
              {{ trans('City : ') }}
              {{
                user.city_id === null
                  ? 'N/A'
                  : user.city_id === 1000
                  ? (othercity?.name || 'N/A')
                  : (user?.city?.name || 'N/A')
              }}
            </p>
            <p>{{ trans('Postal Code : ') }} {{ user.postal_code || 'N/A' }}</p>
            <p>{{ trans('Address Line : ') }} {{ user?.address_line || 'N/A' }}</p>
          </div>
        </section>
        <section class="card card-body col-span-1 space-y-3">
          <h5 class="border-b border-gray-700 pb-1 text-lg">
            {{ trans('Others') }}
          </h5>
          <div class="space-y-1 text-sm">
            <p>
              {{ trans('Date Of Birth : ') }}
              {{ user?.dob_day }}-{{ user?.dob_month }}-{{ user?.dob_year }}
            </p>
            <p>
              {{ trans('Email Verified : ') }}
              <span
                :class="
                  user.email_verified_at
                    ? 'badge badge-success badge-sm'
                    : 'badge badge-danger badge-sm'
                "
              >
                <small>{{ user.email_verified_at ? 'verified' : 'unverified' }}</small>
              </span>
            </p>
            <p>
              {{ trans('Phone Verified : ') }}
              <span
                :class="
                  user.phone_verified_at
                    ? 'badge badge-success badge-sm'
                    : 'badge badge-danger badge-sm'
                "
              >
                <small>{{ user.phone_verified_at ? 'verified' : 'unverified' }}</small>
              </span>
            </p>
            <p>
              {{ trans('KYC Verified : ') }}
              <span
                :class="
                  user.kyc_verified_at
                    ? 'badge badge-success badge-sm'
                    : 'badge badge-danger badge-sm'
                "
              >
                <small>{{ user.kyc_verified_at ? 'verified' : 'unverified' }}</small>
              </span>
            </p>
            <p v-if="user.google2fa_secret">
              {{ trans('2fa Authorization : ') }}
              <span
                :class="
                  user.google2fa_secret
                    ? 'badge badge-success badge-sm'
                    : 'badge badge-danger badge-sm'
                "
              >
                <small>{{ user.google2fa_secret ? 'enabled' : 'disabled' }}</small>
              </span>
            </p>
          </div>
        </section>
      </div>

      <div class="table-responsive whitespace-nowrap rounded-primary">
        <h5 class="p-2">{{ trans('Orders') }}</h5>
        <table class="table">
          <thead>
            <tr>
              <th>{{ trans('Order No') }}</th>
              <th>{{ trans('Plan Name') }}</th>
              <th>{{ trans('Payment Mode') }}</th>
              <th>{{ trans('Amount') }}</th>
              <th>{{ trans('Status') }}</th>
              <th>{{ trans('Created At') }}</th>
            
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
                <div class="capitalize" :class="badgeClass(order.status)">
                  {{
                    trans(
                      order.status == 2 ? 'pending' : order.status == 1 ? 'approved' : 'declined'
                    )
                  }}
                </div>
              </td>
              <td class="text-center">
                {{ order.created_at_diff }}
              </td>
             
            </tr>
          </tbody>
          <NoDataFound v-else for-table="true" />
        </table>
        <Pagination :links="orders.links" />
      </div>

      <div class="table-responsive whitespace-nowrap rounded-primary">
        <h5 class="p-2">{{ trans('Card Orders') }}</h5>
        <div class="table-responsive whitespace-nowrap rounded-primary">
          <table class="table">
            <thead>
              <tr>
                <th>{{ trans('Order No') }}</th>
                <th>{{ trans('Card Name') }}</th>
                <th>{{ trans('Prepaid Card') }}</th>
                <th>{{ trans('User Name') }}</th>
                <th>{{ trans('Payment Mode') }}</th>
                <th>{{ trans('Amount') }}</th>
                <th>{{ trans('Status') }}</th>
                <th>{{ trans('Created At') }}</th>
                
              </tr>
            </thead>
            <tbody v-if="card_orders.total">
              <tr v-for="order in card_orders.data" :key="order.id">
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
                  {{ order?.credit_card?.card || trans('N/A') }}
                </td>
                <td>
                  <Link class="underline" :href="route('admin.users.show', order?.user?.id)">
                    {{ order?.user?.name }}</Link
                  >
                </td>
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
               
              </tr>
            </tbody>
            <NoDataFound v-else for-table="true" />
          </table>
        </div>
      </div>
    </div>
  </main>
</template>
