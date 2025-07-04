<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import { useForm } from '@inertiajs/vue3'
import SpinnerBtn from '@/Components/SpinnerBtn.vue'
import InputFieldError from '@/Components/InputFieldError.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps(['buttons', 'integration'])
const integration = props.integration
const form = useForm({
  title: integration.title,
  preview: '',
  bg_color: integration.bg_color ?? 'red',
  short_description: integration.short_description,
  long_description: integration.long_description,
  is_featured: integration.is_featured,
  is_active: integration.is_active,
  // seo
  seo: {
    title: integration.meta?.title,
    image: integration.meta?.image,
    description: integration.meta?.description,
    tags: integration.meta?.tags
  },
  _method: 'put'
})

const submit = () => {
  form.post(route('admin.integrations.update', props.integration), {
    onSuccess: () => {
      form.reset()
    }
  })
}
</script>

<template>
  <main class="container p-4 sm:p-6">
    <PageHeader title="Edit Integration" :buttons="buttons" />
    <div class="card mx-auto w-[800px]">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="mb-2">
            <label class="label mb-2">{{ trans('Title') }}</label>
            <input type="text" v-model="form.title" class="input" />
            <InputFieldError :message="form.errors.title" />
          </div>

          <div class="mb-2 mt-2">
            <label>{{ trans('Short Description') }}</label>
            <textarea
              v-model="form.short_description"
              name="short_description"
              class="textarea"
              maxlength="500"
            ></textarea>
            <InputFieldError :message="form.errors.short_description" />
          </div>

          <div class="grid grid-cols-2 gap-2">
            <div class="mb-2">
              <label class="label mb-2">{{ trans('Preview') }}</label>
              <input
                type="file"
                @change="(e) => (form.preview = e.target.files[0])"
                class="input"
              />
              <InputFieldError :message="form.errors.preview" />
            </div>

            <div class="mb-2">
              <label class="label mb-2">{{ trans('Preview BG Color') }}</label>
              <br />
              <input type="color" v-model="form.bg_color" class="h-8 w-16" />
              <InputFieldError :message="form.errors.bg_color" />
            </div>
          </div>

          <div class="mb-2 mt-3">
            <label>{{ trans('Long Description') }}</label>
            <RichEditor v-model="form.long_description" />
            <InputFieldError :message="form.errors.long_description" />
          </div>

          <h6 class="mb-2 mt-10">{{ trans('SEO Settings') }}</h6>
          <div class="mb-2">
            <label>{{ trans('SEO Meta Title') }}</label>
            <input v-model="form.seo.title" type="text" class="input" />
            <InputFieldError :message="form.errors['seo.title']" />
          </div>

          <div class="mb-2">
            <label>{{ trans('SEO Meta Image') }}</label>
            <input
              @change="($event) => (form.seo.image = $event.target.files[0])"
              type="file"
              class="input"
              accept="image/*"
            />
            <InputFieldError :message="form.errors['seo.image']" />
          </div>
          <div class="mb-2 mt-2">
            <label>{{ trans('SEO Meta Description') }}</label>
            <textarea v-model="form.seo.description" class="input h-100"></textarea>
            <InputFieldError :message="form.errors['seo.description']" />
          </div>
          <div class="mb-2 mt-2">
            <label>{{ trans('SEO Meta Tags') }}</label>
            <input v-model="form.seo.tags" type="text" class="input" />
            <InputFieldError :message="form.errors['seo.tags']" />
          </div>

          <div class="mb-2 mt-3">
            <div>
              <label for="toggle-status" class="toggle toggle-sm">
                <input
                  v-model="form.is_active"
                  class="toggle-input peer sr-only"
                  id="toggle-status"
                  type="checkbox"
                />
                <div class="toggle-body"></div>
                <span class="label label-md">{{ trans('Is Active?') }}</span>
              </label>
            </div>
          </div>

          <div class="mb-2 mt-3">
            <div>
              <label for="toggle-featured-status" class="toggle toggle-sm">
                <input
                  v-model="form.is_featured"
                  class="toggle-input peer sr-only"
                  id="toggle-featured-status"
                  type="checkbox"
                />
                <div class="toggle-body"></div>
                <span class="label label-md">{{ trans('Is Featured?') }}</span>
              </label>
            </div>
          </div>

          <div class="mb-2">
            <SpinnerBtn
              classes="btn btn-primary"
              :processing="form.processing"
              :btn-text="trans('Update')"
            />
          </div>
        </form>
      </div>
    </div>
  </main>
</template>
