<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue"
import PageHeader from "@/Layouts/Admin/PageHeader.vue"
import { useForm } from "@inertiajs/vue3"
const props = defineProps(["roles"])
defineOptions({ layout: AdminLayout })

const form = useForm({
  first_name: "",
  last_name: "",
  email: "",
  password: "",
  password_confirmation: "",
  roles: "",
})
</script>

<template>
  <main class="container p-4 sm:p-6">
    <PageHeader />
    <div class="space-y-6">
      <form @submit.prevent="form.post(route('admin.admin.store'))">
        <div class="grid grid-cols-12">
          <div class="lg:col-span-5">
            <strong>{{ trans("Create Admin") }}</strong>
            <p>{{ trans("add admin profile information") }}</p>
          </div>
          <div class="card-wrapper lg:col-span-7">
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
                    class="input" id="password" required="" autocomplete="off" />
                  <div class="invalid-feedback text-danger d-block" v-if="form.errors.password">
                    {{ form.errors.password }}
                  </div>
                </div>
                <div class="mb-2">
                  <label for="password_confirmation">{{ trans("Password") }}</label>
                  <input type="password" placeholder="Confirm Password" v-model="form.password_confirmation"
                    name="password_confirmation" class="input" id="password_confirmation" required=""
                    autocomplete="off" />
                  <div class="invalid-feedback text-danger d-block" v-if="form.errors.password_confirmation">
                    {{ form.errors.password_confirmation }}
                  </div>
                </div>

                <div class="mb-2">
                  <label>{{ trans("Assign Roles") }}</label>
                  <select required v-model="form.roles" id="roles" class="select">
                    <option value="">{{ trans("Select") }}</option>
                    <option v-for="role in roles" :key="role.id" :value="role.name">
                      {{ role.name }}
                    </option>
                  </select>
                  <div class="invalid-feedback text-danger d-block" v-if="form.errors.roles">
                    {{ form.errors.roles }}
                  </div>
                </div>
                <div class="from-group row mt-3">
                  <div class="col-lg-12">
                    <button class="btn btn-primary">{{ trans("Create") }}</button>
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
