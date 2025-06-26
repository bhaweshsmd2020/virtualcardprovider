<script setup>
import sharedComposable from '@/Composables/sharedComposable'

defineProps({
  assets: [Object, Array]
})

const { deleteRow } = sharedComposable()
</script>

<template>
  <div
    v-for="(asset, i) in assets"
    :key="i"
    class="relative h-60 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-800"
  >
    <div class="dropdown absolute right-2 top-2 z-10" data-placement="bottom-end">
      <div class="dropdown-toggle p-1">
        <button
          type="button"
          class="h-8 w-8 rounded-full bg-white font-medium shadow-sm dark:bg-slate-800"
        >
          <i class="bx bx-dots-vertical-rounded text-xl"></i>
        </button>
      </div>

      <div class="dropdown-content w-32 !overflow-visible p-1">
        <Link
          v-if="asset.mime_type === 'image'"
          :href="route('user.image-editor.edit', asset.uuid)"
          class="dropdown-link"
        >
          <i class="h-4 text-slate-400" data-feather="edit"></i>
          <span class="text-sm">{{ trans('Edit') }}</span>
        </Link>
        <Link
          as="button"
          class="dropdown-link"
          @click="deleteRow(route('user.assets.destroy', asset.id))"
        >
          <i class="h-4 text-slate-400" data-feather="trash-2"></i>
          <span class="text-sm">{{ trans('Delete') }}</span>
        </Link>
      </div>
    </div>
    <template v-if="asset.mime_type === 'image'">
      <img v-lazy="asset.path" class="h-52 w-full object-cover" alt="image" />
    </template>
    <template v-if="asset.mime_type === 'video'">
      <video controls class="h-52 w-full">
        <source :src="asset.path" type="video/mp4" />
      </video>
    </template>
    <template v-if="asset.mime_type === 'voice'">
      <audio controls class="h-52 w-full">
        <source :src="asset.path" type="audio/wav" />
        <source :src="asset.path" type="audio/mp3" />
      </audio>
    </template>
    <div class="ml-auto mt-1 flex max-w-max items-center rounded-md px-1">
      <i class="bx bx-file-blank mr-1 text-base"></i>

      <span class="text-xs font-semibold capitalize">{{ asset.file_size }} MB</span>
    </div>
  </div>
</template>
