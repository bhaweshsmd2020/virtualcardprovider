<script setup lang="ts">
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue"
import PageHeader from "@/Layouts/Admin/PageHeader.vue"
import trans from "@/Composables/transComposable"
import { modal } from "@/Composables/actionModalComposable"

defineOptions({ layout: AdminLayout })
defineProps(["kycRequest", "segments", "buttons"])

const submitForm = (route, request, status) => {
  
  let data = {
    request: request,
    status: status,
  }

  modal.init(route, {
    method: "post",
    data,
    options: {
      confirm_text: "Are you sure want to do this action ?",
      message: "",
      accept_btn_text: "Yes, Confirm",
      reject_btn_text: "No, Cancel",
    },
  })
}
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader />

    <div class="space-y-4">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <!-- <img src="{{ get_gravatar(kycRequest.user?.email) }}" alt="" class="card-img"> -->
            </div>
            <div class="col-md-8">
              <table class="table table-striped">
                <tbody>
                  <tr>
                    <th>{{ trans("Name") }}</th>
                    <td>
                      {{ kycRequest.user?.name }}
                    </td>
                  </tr>
                  <tr>
                    <th>{{ trans("Email") }}</th>
                    <td>{{ kycRequest.user?.email }}</td>
                  </tr>
                  <tr>
                    <th>{{ trans("Phone") }}</th>
                    <td>{{ kycRequest.user?.phone }}</td>
                  </tr>
                  <tr>
                    <th>{{ trans("KYC Verified At") }}</th>
                    <td>
                      <div v-if="kycRequest.user?.kyc_verified_at">
                        {{ kycRequest.user?.kyc_verified_at }}
                      </div>
                      <div v-else>
                        <div class="badge badge-danger">
                          {{ trans("Not yet verified!") }}
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>{{ trans("Current Status") }}</th>
                    <td>
                      <span v-if="kycRequest.status == 0" class="badge badge-warning">
                        {{ trans("Pending") }}</span
                      >
                      <span
                        v-else-if="kycRequest.status == 1"
                        class="badge badge-primary"
                      >
                        {{ trans("Approved") }}</span
                      >
                      <span v-else-if="kycRequest.status == 2" class="badge badge-danger">
                        {{ trans("Rejected") }}</span
                      >
                      <span v-else-if="kycRequest.status == 3" class="badge badge-dark">
                        {{ trans("Re-Submitted") }}</span
                      >
                    </td>
                  </tr>
                  <tr>
                    <th>{{ trans("Submitted At") }}</th>
                    <td>{{ kycRequest.created_at_date }}</td>
                  </tr>
                  <tr>
                    <th>{{ trans("Type") }}</th>
                    <td>{{ kycRequest.type.charAt(0).toUpperCase() + kycRequest.type.slice(1) }}</td>
                  </tr>

                  <tr v-if="kycRequest.status == 2">
                    <th>{{ trans("Rejected At") }}</th>
                    <td>{{ kycRequest.rejected_at }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <hr class="mb-10" />

          <table class="table mt-3">
            <thead>
              <tr>
                <td colspan="2">
                  <h4 class="text-center bg-gray-300 dark:bg-gray-700 py-2 rounded-md">
                    {{ trans("Submissions") }}
                  </h4>
                </td>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in kycRequest.data" :key="item">
                <th>{{ item.label }}</th>
                <td>
                  <a
                    target="_blank"
                    class="btn btn-success"
                    :href="item.value"
                    v-if="item.type == 'file'"
                    >{{ trans("View") }}</a
                  >
                  <p v-else>{{ item.value }}</p>
                </td>
              </tr>
            </tbody>
          </table>

          <div class="flex gap-3 justify-center mt-5">
            <template v-if="kycRequest.status == 0 || kycRequest.status == 3">
              <button
                class="btn btn-primary"
                @click="
                  submitForm(route('admin.kyc-requests.store'), kycRequest.id, 'approve')
                "
              >
                <Icon icon="heroicons-outline:check" />
                {{ trans("Approve") }}
              </button>

              <button
                class="btn btn-danger"
                @click="
                  submitForm(route('admin.kyc-requests.store'), kycRequest.id, 'reject')
                "
              >
                <Icon icon="heroicons-outline:x" />
                {{ trans("Reject") }}
              </button>
            </template>
            <template v-if="kycRequest.status == 1">
              <button
                class="btn btn-danger"
                @click="
                  submitForm(route('admin.kyc-requests.store'), kycRequest.id, 'reject')
                "
              >
                <Icon icon="heroicons-outline:x" />
                {{ trans("Reject") }}
              </button>
            </template>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>
