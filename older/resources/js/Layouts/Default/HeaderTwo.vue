<script setup>
import NavMenu from '../Default/NavMenu.vue'
import { usePage } from '@inertiajs/vue3'
import sharedComposable from '@/Composables/sharedComposable'
import LanguageSwitch from '@/Components/Web/LanguageSwitch.vue'
const { authUser } = sharedComposable()
const page = usePage()
const primaryData = page.props?.primaryData ?? {}
</script>
<template>
  <header class="theme-main-menu menu-overlay menu-style-two sticky-menu">
    <div class="gap-fix info-row">
      <div class="d-md-flex justify-content-between">
        <div class="greetings text-center">
          <span class="opacity-50">{{ primaryData.top_pre_text }} </span>
          <span class="fw-500">&nbsp;{{ primaryData.top_text }}</span>
        </div>
        <ul class="style-none d-none d-md-flex contact-info">
          <li class="d-flex align-items-center">
            <img src="/assets/images/icon/icon_14.svg" alt="" class="lazy-img icon me-2" />
            <a class="fw-500" :href="'mailto:' + primaryData.contact_email">
              {{ primaryData.contact_email }}
            </a>
          </li>
          <li class="d-flex align-items-center">
            <a href="https://wa.me/17867374617" target="_blank">
              <img src="/assets/images/whatsapp.png" alt="" class="lazy-img icon " style="width:150px;" />
            </a>
          </li>
        </ul>
      </div>
    </div>
    <div class="inner-content gap-fix">
      <div class="top-header position-relative">
        <div class="d-flex align-items-center">
          <div class="logo order-lg-0">
            <Link href="/" class="d-flex align-items-center">
            <img v-lazy="primaryData.deep_logo" alt="logo" class="logo-w200" />
            </Link>
          </div>
          <!-- logo -->
          <div class="right-widget order-lg-3 ms-auto d-flex">
            <LanguageSwitch class="d-none d-md-inline-block me-3" />
            <ul class="d-flex align-items-center style-none">
              <li v-if="!authUser" class="d-flex align-items-center login-btn-one me-md-0 me-3">
                <img src="/assets/images/icon/icon_16.svg" alt="" class="lazy-img icon me-2" />
                <Link href="/login" class="fw-500">{{ trans('Login') }}</Link>
              </li>
              <li v-if="!authUser" class="d-none d-md-inline-block ms-lg-5 me-lg-0 me-3 ms-3">
                <a href="/register" class="signup-btn-one icon-link">
                  <span>{{ trans('Signup') }}</span>
                  
                </a>
              </li>
              <li v-if="authUser" class="d-none d-md-inline-block ms-lg-5 me-lg-0 me-3 ms-3">
                <a href="/user/dashboard" class="signup-btn-one icon-link">
                  <span>{{ trans('Dashboard') }}</span>
                 
                </a>
              </li>
            </ul>
          </div>
          <!--/.right-widget-->
          <NavMenu layout-name="Web" />
        </div>
      </div>
      <!--/.top-header-->
    </div>
  </header>
</template>
