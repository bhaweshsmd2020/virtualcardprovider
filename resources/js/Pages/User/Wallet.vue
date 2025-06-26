<script setup>
import PageHeader from '@/Layouts/Admin/PageHeader.vue'
import UserLayout from '@/Layouts/User/UserLayout.vue'
import WalletCard from '@/Components/User/WalletCard.vue'
import GetCard from '@/Components/User/GetCard.vue'
import Modal from '@/Components/Admin/Modal.vue'
import { useModalStore } from '@/Store/modalStore'
import sharedComposable from '@/Composables/sharedComposable'
defineOptions({ layout: UserLayout })
const modal = useModalStore()
const props = defineProps(['buttons', 'accounts', 'qrCode', 'qrCodeLink', 'card_intro_details'])

const { copyToClipboard } = sharedComposable()
function convertSvgToPng(svgContent) {
  const canvas = document.createElement('canvas')
  const ctx = canvas.getContext('2d')

  const DOMURL = window.URL || window.webkitURL || window
  const img = new Image()
  const svgBlob = new Blob([svgContent], { type: 'image/svg+xml' })
  const url = DOMURL.createObjectURL(svgBlob)

  img.onload = function () {
    canvas.width = img.width
    canvas.height = img.height
    ctx.drawImage(img, 0, 0)

    const imgData = canvas.toDataURL('image/png')

    //download link
    const link = document.createElement('a')
    link.href = imgData
    link.download = 'qr-code.png'
    link.textContent = 'Download PNG'
    document.body.appendChild(link)
    link.click()

    DOMURL.revokeObjectURL(url)
  }

  img.src = url
}
</script>

<template>
  <Modal
    :header-state="true"
    header-title="Received Payment Via QR"
    :state="modal.states['qrShare']"
  >
    <div class="mb-2 mt-4 flex gap-2">
      <input type="text" class="input" readonly :value="qrCodeLink" />
      <button class="btn btn-soft-primary" @click="copyToClipboard(qrCodeLink)">
        {{ trans('Copy') }}
      </button>
    </div>

    <div class="flex flex-col items-center justify-center">
      <div v-html="qrCode" class="flex w-full scale-75 items-center justify-center"></div>
      <button type="button" @click="convertSvgToPng(qrCode)" class="btn btn-soft-primary">
        {{ trans('Download') }}
      </button>
    </div>
  </Modal>
  <main class="container flex-grow p-4 sm:p-6">
    <div class="-mb-9 ml-auto mr-2 flex w-1/12 justify-end sm:mr-0" style="position: relative; top: 52px;">
      <button @click="modal.open('qrShare')" class="btn btn-primary btn-sm whitespace-nowrap">
        <Icon icon="ph:qr-code-light" /> {{ trans('QR Code') }}
      </button>
    </div>
    <PageHeader title="Wallets" classes="mr-0 mt-4 sm:mt-0 sm:mr-28" :buttons="buttons" />

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
      <div class="col-span-1 lg:col-span-12">
        <section class="grid grid-cols-1 gap-8 sm:grid-cols-2 xl:grid-cols-4">
          <WalletCard :accounts="accounts" />
        </section>
      </div>
    </div>
  </main>
</template>
