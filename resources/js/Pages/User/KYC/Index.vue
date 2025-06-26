<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3"
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import NoDataFound from "@/Components/Admin/NoDataFound.vue"
import UserLayout from "@/Layouts/User/UserLayout.vue"
import sharedComposable from "@/Composables/sharedComposable"
import trans from "@/Composables/transComposable"
import { onMounted, watch } from 'vue'
import Swal from "sweetalert2"

const props = defineProps(["KYCDocuments"])
const { authUser } = sharedComposable()

defineOptions({ layout: UserLayout })

const showAlert = () => {
  const documents = props.KYCDocuments?.data || [];

  const hasIdentityDocument = documents.some(doc => doc.type === 'identity');
  const hasAddressDocument = documents.some(doc => doc.type === 'address');

  // Show alert if identity document is missing
  if (!hasIdentityDocument) {
    Swal.fire({
      icon: "warning",
      title: "<h2 style='font-size: 20px;'>Identity verification is not completed. Please submit your Identity ID.</h2>",
      confirmButtonText: "Verify Now",
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
        window.location.href = route('user.kyc.tips', 'identity');
      }
    });
    return;
  }

  // Show alert if address document is missing
  if (!hasAddressDocument) {
    Swal.fire({
      icon: "warning",
      title: "<h2 style='font-size: 20px;'>Address verification is not completed. Please submit your Address Proof.</h2>",
      confirmButtonText: "Verify Now",
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
        window.location.href = route('user.kyc.tips', 'address');
      }
    });
    return;
  }

  // If both documents are present, check if either is under review
  const isUnderReview = documents.some(doc =>
    (doc.type === 'identity' || doc.type === 'address') &&
    (doc.status === 0 || doc.status === 3)
  );

  if (isUnderReview) {
    Swal.fire({
      icon: "info",
      title: "<h2 style='font-size: 20px;'>Your account is being verified. Please allow 1 working day for review. Need more help, Get in touch via support@virtualcardprovider.com</h2>",
      confirmButtonText: "Okay",
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
  }

  const isRejected = documents.some(doc => doc.status === 2);
  if (isRejected) {
    Swal.fire({
      icon: "error",
      title: "<h2 style='font-size: 20px;'>One or more of your KYC documents were rejected. Please resubmit. Need help? Contact support@virtualcardprovider.com</h2>",
      confirmButtonText: "Okay",
      allowOutsideClick: false,
      customClass: { popup: "custom-swal-popup" },
      didOpen: (toast) => {
        const closeButton = document.createElement("button");
        closeButton.innerHTML = "&times;";
        closeButton.classList.add("swal-close-btn");
        closeButton.onclick = () => Swal.close();
        toast.appendChild(closeButton);
      },
    });
  }
};

onMounted(() => {
  showAlert() // Initial check
})

watch(() => props.KYCDocuments, (newValue) => {
  if (newValue) {
    showAlert()
  }
}, { immediate: true, deep: true })
</script>

<template>
  <main class="container p-4 sm:p-6">
    <PageHeader :title="trans('KYC Requests')" />
    <div class="mb-3">
      <h4 class="text-center" v-if="authUser.kyc_verified_at">
        <!-- <span class="badge badge-success">
          {{ trans("Congratulations, You are verified now") }}
        </span> -->
      </h4>

      <div v-else class="alert alert-info">
        <p>{{ trans("Profile verification is not completed") }}</p>
        <Link class="btn btn-primary" :href="route('user.kyc.tips', 'identity')">{{
          trans("Verify Identity")
        }}</Link>
        <Link class="btn btn-primary" :href="route('user.kyc.tips', 'address')">{{
          trans("Verify Address")
        }}</Link>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table job-alert-table">
        <thead class="border-0">
          <tr>
            <th>{{ trans("Method") }}</th>
            <!-- <th>{{ trans("Note") }}</th> -->
            <!-- <th>{{ trans("Documents") }}</th> -->
            <th>{{ trans("Type") }}</th>
            <th>{{ trans("Status") }}</th>
            <th>{{ trans("Action") }}</th>
          </tr>
        </thead>
        <tbody class="border-top-0" v-if="KYCDocuments.total">
          <tr v-for="(document, index) in KYCDocuments.data" :key="index">
            <td>{{ document.method.title ?? null }}</td>
            <!-- <td>{{ document.note }}</td> -->
            <!-- <td>{{ document.data?.length ?? 0 }}</td> -->
            <td>{{ document.type.charAt(0).toUpperCase() + document.type.slice(1) }}</td>
            <td>
              <span v-if="document.status == 0" class="badge badge-warning">
                {{ trans("Pending") }}</span
              >
              <span v-else-if="document.status == 1" class="badge badge-primary">
                {{ trans("Approved") }}</span
              >
              <span v-else-if="document.status == 2" class="badge badge-danger">
                {{ trans("Rejected") }}</span
              >
              <span v-else-if="document.status == 3" class="badge badge-dark">
                {{ trans("Re-Submitted") }}</span
              >
            </td>
            <td class="text-end">
              <Link
                v-if="document.status == 2"
                class="py-2 btn btn-dark me-4"
                :href="route('user.kyc.tips', document.type)"
              >
                {{ trans("Re Submit") }}
              </Link>

              <Link class="btn btn-primary" :href="route('user.kyc.show', document.id)">
               <Icon icon="bx:show" />
              </Link>
            </td>
          </tr>
          <tr></tr>
        </tbody>
        <NoDataFound v-else for-table="true" />
      </table>
    </div>
  </main>
</template>
