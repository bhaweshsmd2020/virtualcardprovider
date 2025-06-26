<script setup>
import NavMenu from './NavMenu.vue'
import { usePage } from '@inertiajs/vue3'
import sharedComposable from '@/Composables/sharedComposable'
const { authUser } = sharedComposable()
const page = usePage()
const primaryData = page.props?.primaryData ?? {}
</script>
<template>

  <header class="theme-main-menu menu-overlay menu-style-three sticky-menu">
    <div class="inner-content">
      <div class="top-header position-relative">
        <div class="d-flex align-items-center justify-content-between">
          <div class="logo order-lg-0">
            <Link href="/" class="d-flex align-items-center">
            <img v-lazy="primaryData.deep_logo" alt="">
            </Link>
          </div>
          <!-- logo -->
          <div class="right-widget ms-auto ms-lg-0 me-3 me-lg-0 order-lg-3">
            <ul class="d-flex align-items-center style-none">
              <li class="d-flex align-items-center login-btn-one" v-if="!authUser">
                <img src="/assets/images/icon/icon_16.svg" alt="" class="lazy-img icon me-2" >
                <a href="/login" class="fw-500">{{ trans('Login') }}</a>
              </li>
              <li class="d-flex align-items-center login-btn-one" v-if="authUser">
                <img src="/assets/images/icon/icon_23.svg" alt="" class="lazy-img icon me-2" >
                <a href="/user/dashboard" class="fw-500">{{ trans('Dashboard') }}</a>
              </li>
              <li class="d-none d-md-inline-block ms-3 ms-xl-5">
                <a href="/register" class="btn-ten tran3s">{{ trans('Get Started') }}</a>
              </li>
            </ul>
          </div> <!--/.right-widget-->
          <NavMenu />
        </div>
      </div> <!--/.top-header-->
    </div> <!-- /.inner-content -->
  </header>

</template>
