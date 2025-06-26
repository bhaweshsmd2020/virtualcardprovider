<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import { useForm } from '@inertiajs/vue3'
import Multiselect from '@vueform/multiselect'
import SpinnerBtn from '@/Components/SpinnerBtn.vue'
import CreditCard from '@/Components/CreditCard.vue'
import { ref } from 'vue'
defineOptions({ layout: AdminLayout })

const props = defineProps(['categories', 'merchantCategories', 'card', 'card_variants'])
const form = useForm({
  _method: 'PATCH',
  ...props.card,
  is_featured: props.card.is_featured == 1 ? true : false
})

const submit = () => {
  form.post(route('admin.cards.update', props.card.id))
}
const creditCard = ref({
  exp_year: 28,
  exp_month: 28,
  last4: 1234,
  user: {
    name: 'User'
  },
  card: {
    card_variant: props.card.card_variant ?? 'basic'
  }
})
</script>

<template>
  <main class="container flex-grow p-4 sm:p-6">
    <PageHeader title="Edit Card" />

    <div class="mx-auto max-w-3xl">
      <form @submit.prevent="submit" class="space-y-5">
        <div class="card card-body">
          <div class="mb-2">
            <label class="label mb-1">{{ trans('Title') }}</label>
            <input v-model="form.title" type="text" name="title" required="" class="input" />
          </div>
          <div class="mb-2">
            <label class="label mb-1">{{ trans('Min Deposit') }}</label>
            <input v-model="form.min_deposit" type="number" name="min_deposit" required class="input" />
          </div>
          <div class="mb-2">
            <label class="label mb-1">{{ trans('Max Deposit') }}</label>
            <input v-model="form.max_deposit" type="number" name="max_deposit" required class="input" />
          </div>
          <div class="mb-2">
            <label class="label mb-1">{{ trans('Creation Fee') }} (%)</label>
            <input v-model="form.card_fee" type="number" name="card_fee" required class="input" step="any" />
          </div>
          <div class="mb-2">
            <label class="label mb-1">{{ trans('Card Topup Fee') }} (%)</label>
            <input v-model="form.increase_limit_fee" type="number" name="increase_limit_fee" required class="input" step="any" />
          </div>
          <div class="mb-2">
            <label class="label mb-1">{{ trans('Card Topup Limit') }}</label>
            <input v-model="form.topup_limit" type="number" name="topup_limit" required class="input" step="any" />
          </div>
          <div class="mb-2">
            <label class="label mb-1">{{ trans('Total Card Spending Limit') }}</label>
            <input v-model="form.spending_limit" type="number" name="spending_limit" required class="input" step="any" />
          </div>
          <div class="mb-2">
            <label class="label mb-1">{{ trans('Card Limit') }}</label>
            <input v-model="form.card_limit" type="number" name="card_limit" required class="input" />
          </div>
          <div class="mb-2">
            <label class="label mb-1">{{ trans('Required Balance') }}</label>
            <input
              v-model="form.required_balance"
              type="number"
              name="required_balance"
              required
              class="input"
            />
          </div>
          <div class="mb-2 mt-2">
            <label class="label mb-1">{{ trans('Image (Preview)') }}</label>
            <input
              @input="(e) => (form.preview = e.target.files[0])"
              type="file"
              class="input"
              name="preview"
              accept="image/*"
            />
          </div>
          <div class="mb-2 mt-2">
            <label class="label mb-1">{{ trans('Short Description') }}</label>
            <textarea
              v-model="form.description"
              name="description"
              required
              class="textarea"
              maxlength="500"
            ></textarea>
          </div>
          <div class="mb-2">
            <label class="label mb-1">{{ trans('Select Category') }}</label>
            <Multiselect
              class="multiselect-dark"
              :searchable="true"
              v-model="form.category_id"
              value-prop="id"
              label="title"
              mode="single"
              :options="categories"
              placeholder="Select Category"
            />
          </div>
          <div class="mb-2">
            <label class="label mb-1">{{ trans('Card Variant') }}</label>
            <Multiselect
              @select="
                () => {
                  creditCard.card.card_variant = form.card_variant
                }
              "
              class="multiselect-dark capitalize"
              v-model="form.card_variant"
              mode="single"
              :options="card_variants"
              placeholder="Select Card Variant"
            />
          </div>
          <div class="mx-auto mt-6 w-8/12">
            <CreditCard :credit-card="creditCard" />
          </div>
        </div>
        <div class="card card-body">
          <div class="mb-4 flex items-center justify-between">
            <p class="text-xl font-semibold">
              {{ trans('Merchant Categories') }}
              <span class="text-base font-normal">({{ trans('Optional') }})</span>
            </p>
            <button
              type="button"
              @click="
                () => {
                  form.categories = []
                  form.category_type = ''
                }
              "
              v-if="form.category_type"
              class="btn btn-xs btn-danger"
            >
              {{ trans(' Clear') }}
            </button>
          </div>
          <div class="mb-4 space-y-2">
            <label class="label mb-1">{{ trans('Add category to Allowed/Blocked') }} </label>
            <div class="mb-2 flex flex-wrap gap-4">
              <div class="flex items-center gap-1">
                <input
                  v-model="form.category_type"
                  id="allowed"
                  class="radio"
                  type="radio"
                  name="basic-radio"
                  value="allowed"
                />
                <label for="allowed" class="label">{{ trans('Allowed') }}</label>
              </div>
              <div class="flex items-center gap-1">
                <input
                  v-model="form.category_type"
                  id="blocked"
                  type="radio"
                  class="radio"
                  name="basic-radio"
                  value="blocked"
                />
                <label for="blocked" class="label">{{ trans('Blocked') }}</label>
              </div>
            </div>
            <p class="pt-2 text-xs">
              *{{ trans("default: All categories are allowed (it's needed for stripe)") }}
            </p>
          </div>
          <div class="mb-2">
            <label class="label mb-1">{{ trans('Select Merchant Category') }}</label>
            <Multiselect
              class="multiselect-dark"
              :searchable="true"
              v-model="form.categories"
              value-prop="value"
              label="name"
              mode="tags"
              :options="merchantCategories"
              placeholder="Select Merchant Category"
            />
          </div>
        </div>

        <div class="card card-body mb-2 mt-3 space-y-3">
          <div>
            <label class="label mb-1">{{ trans('Status') }}</label>
            <select v-model="form.status" class="select" name="status">
              <option value="active">{{ trans('Active') }}</option>
              <option value="inactive">{{ trans('Draft') }}</option>
            </select>
          </div>
          <div>
            <label for="toggle-featured" class="toggle toggle-sm">
              <input
                v-model="form.is_featured"
                class="toggle-input peer sr-only"
                id="toggle-featured"
                type="checkbox"
              />
              <div class="toggle-body"></div>
              <span class="label label-md">{{ trans('Is Featured?') }}</span>
            </label>
          </div>
          <div>
            <SpinnerBtn
              classes="btn btn-primary"
              :processing="form.processing"
              :btn-text="trans('Update')"
            />
          </div>
        </div>
      </form>
    </div>
  </main>
</template>
