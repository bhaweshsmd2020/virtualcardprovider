<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue"
import SpinnerBtn from "@/Components/SpinnerBtn.vue"
import toast from "@/Composables/toastComposable"
import PageHeader from "@/Layouts/Admin/PageHeader.vue"
import { useForm } from "@inertiajs/vue3"

defineOptions({ layout: AdminLayout })

const props = defineProps(["roles", "user"])

const form = useForm({
  first_name: props.user.first_name,
  last_name: props.user.last_name,
  email: props.user.email,
  password: null,
  password_confirmation: null,
  status: props.user.status,
  roles: props.user.roles[0].name,
})

const update = () => {
  const url = route("admin.admin.update", props.user.id)

  form.put(url, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success(trans("Sub admin profile updated"))
    },
  })
}
</script>

<template>
  <main class="container p-4 sm:p-6">
    <PageHeader />
    <div class="space-y-6">
      <form @submit.prevent="update">
        <div class="grid grid-cols-12">
          <div class="col-span-5">
            <strong>{{ trans("Edit Admin") }}</strong>
            <p>{{ trans("Edit admin profile information") }}</p>
          </div>
          <div class="col-span-7">
            <div class="card">
              <div class="card-body">
                <div class="mb-2">
                  <label for="name">{{ trans("First Name") }}</label>
                  <input type="text" placeholder="Jhon" v-model="form.first_name" class="input" id="first_name"
                    required="" autocomplete="off" />
                  <div class="invalid-feedback text-danger d-block" v-if="form.errors.first_name">
                    {{ form.errors.first_name }}
                  </div>
                </div>
                <div class="mb-2">
                  <label for="name">{{ trans("Last Name") }}</label>
                  <input type="text" placeholder="Doe" v-model="form.last_name" class="input" id="last_name" required=""
                    autocomplete="off" />
                  <div class="invalid-feedback text-danger d-block" v-if="form.errors.last_name">
                    {{ form.errors.last_name }}
                  </div>
                </div>
                <div class="mb-2">
                  <label for="email">{{ trans("Email") }}</label>
                  <input type="email" placeholder="Enter Email" v-model="form.email" name="email" class="input"
                    id="email" required="" autocomplete="off" />
                  <div class="invalid-feedback text-danger d-block" v-if="form.errors.email">
                    {{ form.errors.email }}
                  </div>
                </div>
                <div class="mb-2">
                  <label for="password">{{ trans("Password") }}</label>
                  <input type="password" placeholder="Enter password" v-model="form.password" name="password"
                    class="input" id="password" autocomplete="off" />
                  <div class="invalid-feedback text-danger d-block" v-if="form.errors.password">
                    {{ form.errors.password }}
                  </div>
                </div>
                <div class="mb-2">
                  <label for="password_confirmation">{{ trans("Password") }}</label>
                  <input type="password" placeholder="Confirm Password" v-model="form.password_confirmation"
                    name="password_confirmation" class="input" id="password_confirmation" autocomplete="off" />
                  <div class="invalid-feedback text-danger d-block" v-if="form.errors.password_confirmation">
                    {{ form.errors.password_confirmation }}
                  </div>
                </div>
                <div class="mb-2">
                  <label>{{ trans("Assign Roles") }}</label>
                  <select required v-model="form.roles" id="roles" class="select">
                    <option v-for="role in roles" :key="role.id" :value="role.name">
                      {{ role.name }}
                    </option>
                  </select>
                  <div class="invalid-feedback text-danger d-block" v-if="form.errors.roles">
                    {{ form.errors.roles }}
                  </div>
                </div>
                <div class="mb-2">
                  <label>{{ trans("Status") }}</label>
                  <select name="status" class="select" v-model="form.status">
                    <option value="">{{ trans("Select") }}</option>
                    <option :value="true">{{ trans("Active") }}</option>
                    <option :value="false">{{ trans("Deactive") }}</option>
                  </select>
                </div>
                <div class="mb-2 mt-3">
                  <div class="col-lg-12">
                    <SpinnerBtn :processing="form.processing" :btn-text="trans('Update Profile')" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </main>
</template>
