<script setup lang="ts">
import { Head, useForm } from "@inertiajs/vue3";
import UserLayout from "@/Layouts/User/UserLayout.vue";
import SpinnerBtn from "@/Components/SpinnerBtn.vue";
import { ref } from "vue";
import trans from "@/Composables/transComposable";
import { usePage } from "@inertiajs/vue3";
import sharedComposable from '@/Composables/sharedComposable'

defineOptions({ layout: UserLayout });

const { kyc_methods, kyc_type } = defineProps(["kyc_methods", "kyc_type"]);

const selectedMethod = ref(kyc_methods[0] ? kyc_methods[0] : {});

const { authUser } = sharedComposable()

const setSelectedMethod = (method) => {
  selectedMethod.value = method;
  form.method = selectedMethod.value?.id;
  form.fields = selectedMethod.value?.fields ?? [];
};

const flattenObject = (obj, parentKey) => {
  let result = {};

  Object.keys(obj).forEach((key) => {
    const value = obj[key];
    const _key = parentKey ? key : key;

    if (typeof value === "object" && !(value instanceof File)) {
      result = { ...result, ...flattenObject(value, _key) };
    } else {
      result[_key] = value;
    }
  });

  return result;
};

const userName = `${authUser.value.first_name} ${authUser.value.last_name}`;

const form = useForm({
  method: selectedMethod.value?.id,
  fields: selectedMethod.value?.fields.map((field, index) => ({
    ...field,
    value: index === 0 ? userName : field.value,
  })) ?? [],
  note: "",
  type: kyc_type
});

const submit = () => {
  form.post(route("user.kyc.store"));
};
</script>

<template>
  <Head title="KYC verification | User Panel" />

  <main class="container flex-grow p-4 sm:p-6">
   

    <div class="flex items-center justify-between">
      <h2 class="main-title">{{ trans("KYC verification") }}</h2>
      <Link :href="route('user.kyc.types', kyc_type)" class="btn btn-primary">
        {{ trans("Back") }}
      </Link>
    </div>

    <div class="w-8/12 mx-auto mt-3 card">
      <div v-if="kyc_methods.length" class="card-body">
        <div class="flex gap-2">
          <button
            v-for="(method, index) in kyc_methods"
            :key="index"
            @click="setSelectedMethod(method)"
            type="button"
            :class="[selectedMethod.id == method.id ? 'btn-primary' : 'border']"
            class="btn"
          >
            {{ method.title }}
          </button>
        </div>

        <form class="p-3" @submit.prevent="submit()">
          <template v-for="(field, index) in form.fields" :key="field.id">
            <div v-if="field.type == 'textarea'" class="mb-2 dash-input-wrapper">
              <label> {{ field.label }} </label>
              <textarea v-model="field.value" class="textarea"></textarea>
            </div>

            <div v-else-if="field.type == 'file'" class="mb-2">
              <label> {{ field.label }} </label>
              <input
                type="file"
                @change="(e) => (field.value = e.target.files[0])"
                class="input"
              />
            </div>

            <div v-else class="mb-2 dash-input-wrapper">
              <label :for="`fields_${index}`">{{ field.label }}</label>
              <input
                :type="field.type"
                v-model="field.value"
                :id="`fields_${index}`"
                class="input"
              />
            </div>
          </template>

          <SpinnerBtn :processing="form.processing" :btn-text="trans('Submit')" />
        </form>
      </div>
      <div v-else class="py-6 text-center">
        {{ trans("No remaining items to submit") }}
      </div>
    </div>
  </main>
</template>
