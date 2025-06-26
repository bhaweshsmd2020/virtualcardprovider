<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue"
import PageHeader from "@/Layouts/Admin/PageHeader.vue"
import { useForm } from "@inertiajs/vue3"
import Paginate from "@/Components/Admin/Paginate.vue"
import moment from "moment"
import { ref } from "vue"
import sharedComposable from "@/Composables/sharedComposable"
import OverviewGrid from "@/Components/Admin/OverviewGrid.vue"
import NoDataFound from "@/Components/Admin/NoDataFound.vue"
import SpinnerBtn from "@/Components/SpinnerBtn.vue"
import { onMounted } from "vue"
import drawer from "@/Plugins/Admin/drawer"
import trans from "@/Composables/transComposable"
import ImageInput from "@/Components/Admin/ImageInput.vue"
defineOptions({ layout: AdminLayout })
const { deleteRow } = sharedComposable()

onMounted(() => {
  drawer.init()
})

const props = defineProps([
  "brands",
  "totalBrands",
  "activeBrands",
  "inActiveBrands",
  "buttons",
])

const brandsOverviews = [
  {
    value: props.totalBrands,
    title: trans("Total Partners"),
    iconClass: "bx bx-bar-chart",
  },
  {
    value: props.activeBrands,
    title: trans("Active Partners"),
    iconClass: "bx bx-check-circle",
  },
  {
    value: props.inActiveBrands,
    title: trans("Inactive Partners"),
    iconClass: "bx bx-x-circle",
  },
]

const form = useForm({
  url: "",
  status: "1",
  type: "partner",
  preview: "",
})

const editPartnerForm = useForm({
  id: "",
  title: "",
  preview: "",
  type: "partner",
  status: "1",
  _method: "put",
})

const storePartner = () => {
  form.post(route("admin.partner.store"), {
    onSuccess: () => {
      form.reset()
      drawer.of("#addNewPartnerDrawer").hide()
    },
  })
}

function openEditFaqDrawer(partner) {
  editPartnerForm.id = partner.id
  editPartnerForm.title = partner.title
  editPartnerForm.status = partner.status
  editPartnerForm.type = partner.type
  editPartnerForm.preview = partner.preview
  drawer.of("#editPartnerDrawer").show()
}

const updatePartner = () => {
  if (!(editPartnerForm.preview instanceof File)) {
    editPartnerForm.preview = ""
  }

  editPartnerForm.post(route("admin.partner.update", editPartnerForm.id), {
    onSuccess: () => {
      editPartnerForm.reset()
      drawer.of("#editPartnerDrawer").hide()
    },
  })
}
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader title="Partners" :buttons="buttons" />
    <div class="space-y-6">
      <OverviewGrid :items="brandsOverviews" grid="3" />

      <div class="card">
        <div class="table-responsive whitespace-nowrap rounded-primary">
          <table class="table">
            <thead>
              <tr>
                <th>{{ trans("Image") }}</th>
                <th>{{ trans("Url") }}</th>
                <th>{{ trans("Type") }}</th>
                <th>{{ trans("Status") }}</th>
                <th>{{ trans("Created At") }}</th>
                <th class="!text-right">{{ trans("Action") }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="row in brands.data" :key="row.id">
                <td class="text-left">
                  <img v-lazy="row.preview" class="avatar rounded-square w-70-per" />
                </td>
                <td class="text-left">
                  {{ row.title }}
                </td>
                <td class="text-left">
                  {{ row.lang == "en" ? "Partner" : row.lang }}
                </td>

                <td class="text-left">
                  <span
                    class="badge"
                    :class="row.status == 1 ? 'badge-success' : 'badge-danger'"
                  >
                    {{ row.status == 1 ? trans("Active") : trans("Draft") }}
                  </span>
                </td>
                <td>
                  {{ moment(row.created_at).format("D-MMM-Y") }}
                </td>

                <td>
                  <div class="dropdown" data-placement="bottom-start">
                    <div class="dropdown-toggle">
                      <Icon class="w-30 text-lg" icon="bi:three-dots-vertical" />
                    </div>
                    <div class="dropdown-content w-40">
                      <ul class="dropdown-list">
                        <li class="dropdown-list-item">
                          <button @click="openEditFaqDrawer(row)" class="dropdown-link">
                            <Icon icon="bx:edit"></Icon>
                            <span>{{ trans("Edit") }}</span>
                          </button>
                        </li>

                        <li class="dropdown-list-item">
                          <button
                            class="dropdown-link"
                            @click="deleteRow(route('admin.partner.destroy', row.id))"
                          >
                            <Icon icon="bx:trash"></Icon>
                            <span>{{ trans("Delete") }}</span>
                          </button>
                        </li>
                      </ul>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
            <NoDataFound v-if="!brands.total" :for-table="true" />
          </table>
        </div>
      </div>
      <Paginate :links="brands.links" />
    </div>
  </main>

  <div id="addNewPartnerDrawer" class="drawer drawer-right">
    <form @submit.prevent="storePartner()">
      <div class="drawer-header">
        <h5>{{ trans("Add New Partner") }}</h5>
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
          <label>{{ trans("Brand Url") }}</label>
          <input type="text" name="url" v-model="form.url" class="input" />
        </div>
        <ImageInput :label="trans('Brand Image')" v-model="form.preview" class="mb-2" />
        <div class="mb-2">
          <label>{{ trans("Type") }}</label>
          <select class="select" name="type" v-model="form.type">
            <option value="partner">{{ trans("Partner / Brand") }}</option>
            <option value="integration">
              {{ trans("Integration Partner") }}
            </option>
          </select>
        </div>
        <div class="mb-2">
          <label>{{ trans("Status") }}</label>
          <select class="select" name="status">
            <option value="1">{{ trans("Active") }}</option>
            <option value="0">{{ trans("Deactivate") }}</option>
          </select>
        </div>
      </div>
      <div class="drawer-footer">
        <div class="flex justify-end gap-2">
          <button type="button" class="btn btn-secondary w-full" data-dismiss="drawer">
            <span> {{ trans("Close") }}</span>
          </button>
          <SpinnerBtn
            classes="btn btn-primary w-full"
            :processing="form.processing"
            :btn-text="trans('Create')"
          />
        </div>
      </div>
    </form>
  </div>

  <div id="editPartnerDrawer" class="drawer drawer-right">
    <form @submit.prevent="updatePartner()">
      <div class="drawer-header">
        <h5>{{ trans("Edit Partner") }}</h5>
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
          <label>{{ trans("Brand Url") }}</label>
          <input
            type="text"
            name="url"
            v-model="editPartnerForm.title"
            class="input"
            id="url"
          />
        </div>

        <ImageInput
          :label="trans('Brand Image')"
          v-model="editPartnerForm.preview"
          class="mb-2"
        />

        <div class="mb-2">
          <label>{{ trans("Type") }}</label>
          <select
            class="select"
            name="type"
            v-model="editPartnerForm.type"
            id="type"
            required
          >
            <option value="partner">{{ trans("Partner / Brand") }}</option>
            <option value="integration">
              {{ trans("Integration Partner") }}
            </option>
          </select>
        </div>
        <div class="mb-2">
          <label>{{ trans("Status") }}</label>
          <select
            v-model="editPartnerForm.status"
            class="select"
            name="status"
            id="status"
          >
            <option value="1">{{ trans("Active") }}</option>
            <option value="0">{{ trans("Deactive") }}</option>
          </select>
        </div>
      </div>
      <div class="drawer-footer">
        <div class="flex justify-end gap-2">
          <button type="button" class="btn btn-secondary w-full" data-dismiss="drawer">
            <span> {{ trans("Close") }}</span>
          </button>
          <SpinnerBtn
            classes="btn btn-primary w-full"
            :processing="editPartnerForm.processing"
            :btn-text="trans('Save Changes')"
          />
        </div>
      </div>
    </form>
  </div>
</template>
