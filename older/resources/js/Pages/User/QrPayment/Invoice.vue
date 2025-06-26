<script setup>
import { Head } from '@inertiajs/vue3'
import BlankLayout from '@/Layouts/BlankLayout.vue'
import sharedComposable from '@/Composables/sharedComposable'
defineOptions({ layout: BlankLayout })
const { formatCurrency, uiAvatar } = sharedComposable()
const props = defineProps(['invoice_data', 'topUp'])
</script>

<template>
  <Head :title="trans('Payment Invoice')" />
  <div class="payment-container">
    <div class="payment-content">
      <div class="payment-header">
        <img
          class="rounded-full"
          v-lazy="uiAvatar(topUp?.user.first_name, topUp?.user.avatar)"
          alt="logo"
        />
        <p class="font-semibold text-black">{{ trans('Invoice') }} #{{ topUp.invoice_no }}</p>
      </div>

      <!-- gateways table -->

      <div class="gateway-form" :id="'gateway-form' + topUp.gateway.id">
        <table class="payment-table">
          <tr>
            <td>
              {{ trans('Method Name: ') }}
            </td>
            <td class="text-center">
              {{ topUp.gateway.name }}
            </td>
          </tr>
          <template v-if="topUp.gateway.currency != null">
            <tr>
              <td>
                {{ trans('Gateway Currency: ') }}
              </td>
              <td class="text-center">
                {{ topUp.gateway.currency }}
              </td>
            </tr>
          </template>
          <template v-if="topUp.gateway.charge != 0">
            <tr>
              <td>
                {{ trans('Gateway Charge: ') }}
              </td>
              <td class="text-center">
                {{ formatCurrency(topUp.gateway.charge) }}
              </td>
            </tr>
          </template>
          <tr>
            <td>
              {{ trans('Payed Amount: ') }}
            </td>
            <td class="text-center">
              {{ formatCurrency(topUp.amount) }}
            </td>
          </tr>
        </table>
      </div>
      <p class="mb-2 text-sm text-black">{{ trans('Sender Details') }}</p>
      <div class="gateway-form">
        <table class="payment-table">
          <tr>
            <td>
              {{ trans('Name: ') }}
            </td>
            <td class="text-center">
              {{ topUp.meta?.name }}
            </td>
          </tr>
          <tr>
            <td>
              {{ trans('Email: ') }}
            </td>
            <td class="text-center">
              {{ topUp.meta?.email }}
            </td>
          </tr>
          <tr>
            <td>
              {{ trans('Note: ') }}
            </td>
            <td class="max-w-20 text-center">
              {{ topUp.meta?.note }}
            </td>
          </tr>
        </table>
      </div>

      <br />

      <div class="payment-invoice">
        <div class="payment-border">
          <b>{{ trans('Invoiced To') }}</b>
          <br />
          {{ topUp?.user.name }}<br />
          {{ topUp?.user.address }}
        </div>
        <div class="payment-border">
          <b>{{ trans('Pay To') }}</b>
          <br />
          {{ invoice_data.company_name }} <br />
          {{ invoice_data.address }}, {{ invoice_data.city }} <br />
          {{ invoice_data.post_code }},
          {{ invoice_data.country }}
        </div>
      </div>

      <div class="payment-details">
        <table class="payment-table">
          <tr>
            <td>
              <b>{{ trans('Name') }}</b>
            </td>
          </tr>

          <tr>
            <td>{{ topUp?.user.name }}</td>
          </tr>

          <tr>
            <td>{{ trans('Total') }} :</td>
            <td class="text-center">
              {{ formatCurrency(topUp.amount) }}
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</template>
