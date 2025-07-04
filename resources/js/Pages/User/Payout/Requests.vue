<script setup>
import UserLayout from '@/Layouts/User/UserLayout.vue'
import Paginate from '@/Components/Admin/Paginate.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import Overview from '@/Components/Admin/OverviewGrid.vue'
import trans from '@/Composables/transComposable'
import NoDataFound from '@/Components/Admin/NoDataFound.vue'
const props = defineProps([
  'payouts',
  'total_approved_requests',
  'total_pending_requests',
  'total_failed_requests',
  'buttons',
  'segments'
])
defineOptions({ layout: UserLayout })

const stats = [
  {
    value: props.total_approved_requests,
    title: trans('Total Complete Requests'),
    iconClass: 'bx bx-dollar'
  },
  {
    value: props.total_pending_requests,
    title: trans('Total Pending Requests'),
    iconClass: 'bx bx-dollar-circle'
  },
  {
    value: props.total_failed_requests,
    title: trans('Total Cancelled Requests'),
    iconClass: 'bx bx-x-circle'
  }
]
</script>

<template>
  <Head :title="trans('Payout Requests')" />
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader title="Payout Methods" :segments="segments" :buttons="buttons" />

    <Overview :items="stats" grid="3" />
    <div class="mx-auto mt-10 w-7/12 space-y-5" v-if="payouts.data.length > 0">
      <div
        v-for="payout in payouts.data"
        :key="payout.id"
        class="card flex items-center justify-between space-y-5 p-6"
      >
        <div class="flex items-center gap-x-5">
          <Link :href="route('user.payout.details', payout.invoice_no)">
            <img
              class="w-28"
              v-lazy="payout.method != null ? payout.method.image : ''"
              alt="image"
            />
          </Link>

          <div>
            <h5>
              <Link :href="route('user.payout.details', payout.invoice_no)">
                {{ payout.invoice_no }} -
                {{ payout.method != null ? payout.method.name : '' }}
              </Link>
            </h5>
            <p class="text-sm">
              {{ trans('Requested at : ') }} <span>{{ payout.created_at_date }}</span>
            </p>

            <div class="capitalize">
              <div v-if="payout.status == 'pending'" class="badge badge-warning">
                <p>{{ payout.status }}</p>
              </div>
              <div v-if="payout.status == 'completed'" class="badge badge-success">
                <p>{{ payout.status }}</p>
              </div>
              <div v-if="payout.status == 'failed'" class="badge badge-danger">
                <p>{{ payout.status }}</p>
              </div>
            </div>
          </div>
        </div>

        <Link :href="route('user.payout.details', payout.invoice_no)" class="btn btn-primary">
          {{ trans('View') }}
        </Link>
      </div>
    </div>
    <NoDataFound v-else :message="trans('No payout methods found')" />
    <Paginate :links="payouts.links" />
  </main>
</template>
