<script setup>
import { usePage } from '@inertiajs/vue3'
import footerComposable from '@/Composables/footerComposable'
import Newsletter from '@/Components/Web/Newsletter.vue'
import LanguageSwitch from '@/Components/Web/LanguageSwitch.vue'
const page = usePage()
const primaryData = page.props.primaryData
const { footerRight, footerCenter, footerLeft, socials } = footerComposable()
</script>
<template>
  <div class="footer-large-wrapper position-relative">
    <Newsletter />
    <div class="footer-four">
      <div class="container">
        <div class="inner-wrapper">
          <div class="row justify-content-between">
            <div class="col-lg-4 footer-intro mb-40">
              <div class="logo">
                <Link href="/">
                  <img v-lazy="primaryData.logo" alt="" />
                </Link>
              </div>
              <!-- logo -->
              <div class="footer-slogan">
                <p v-html="primaryData.slogan"></p>
                <ul class="style-none">
                  <li class="d-flex align-items-center">
                    <img src="/assets/images/icon/icon_49.svg" alt="" class="lazy-img icon me-3" />
                    <a :href="'mailto:' + primaryData.contact_email">
                      {{ primaryData.contact_email }}
                    </a>
                  </li>
                  <li class="d-flex align-items-center">
                    <img src="/assets/images/icon/icon_50.svg" alt="" class="lazy-img icon me-3" />
                    <a class="first-child" :href="'tel:' + primaryData.contact_phone">{{
                      primaryData.contact_phone
                    }}</a>
                  </li>
                </ul>
                <template v-if="Object.keys($page.props?.languages ?? {}).length > 1">
                  <LanguageSwitch />
                </template>
              </div>
            </div>
            <div class="col-lg-2 col-sm-4 mb-20">
              <h5 class="footer-title">{{ footerLeft.name }}</h5>
              <ul class="footer-nav-link style-none">
                <li v-for="item in footerLeft.data" :key="item.id">
                  <Link v-if="item.href" :href="item.href" :target="item.target">
                    {{ item.text }}
                  </Link>
                </li>
              </ul>
            </div>
            <div class="col-lg-2 col-sm-4 mb-20">
              <h5 class="footer-title">{{ footerCenter.name }}</h5>
              <ul class="footer-nav-link style-none">
                <li v-for="item in footerCenter.data" :key="item.id">
                  <Link v-if="item.href" :href="item.href" :target="item.target">
                    {{ item.text }}
                  </Link>
                </li>
              </ul>
            </div>
            <div class="col-xxl-2 col-lg-3 col-sm-4 mb-20">
              <h5 class="footer-title">{{ footerRight.name }}</h5>
              <ul class="footer-nav-link style-none">
                <li v-for="item in footerRight.data" :key="item.id">
                  <Link v-if="item.href" :href="item.href" :target="item.target">
                    {{ item.text }}
                  </Link>
                </li>
              </ul>
            </div>
            <img src="/assets/images/shape/shape_17.svg" class="shape_17" alt="shape" />
          </div>
        </div>
        <!-- /.inner-wrapper -->
      </div>
      <div class="container">
        <div class="bottom-footer">
          <div class="row">
            <div class="col-xl-4 col-lg-3 order-lg-3 mb-15">
              <ul
                v-if="Object.keys(socials).length"
                class="style-none d-flex align-items-center justify-content-center justify-content-lg-end social-icon"
              >
                <li>
                  <a target="_blank" :href="socials.facebook">
                    <i class="bi bi-facebook"></i>
                  </a>
                </li>
                <li>
                  <a target="_blank" :href="socials.twitter"><i class="bi bi-twitter"></i></a>
                </li>
                <li>
                  <a target="_blank" :href="socials.instagram"><i class="bi bi-instagram"></i></a>
                </li>
                <li>
                  <a target="_blank" :href="socials.linkedin"><i class="bi bi-linkedin"></i></a>
                </li>
              </ul>
            </div>
            <div class="col-xl-4 col-lg-6 order-lg-2 mb-15">
              <ul class="style-none bottom-nav d-flex justify-content-center order-lg-last">
                <li>
                  <Link href="/privacy-policy">{{ trans('Privacy Policy') }}</Link>
                </li>
                <li>
                  <Link href="/terms-and-conditions">{{ trans('Terms & Conditions') }}</Link>
                </li>
                <li>
                  <Link href="/contact-us"> {{ trans('Contact Us') }}</Link>
                </li>
              </ul>
            </div>
            <div class="col-xl-4 col-lg-3 order-lg-1 mb-15">
              <div class="copyright text-lg-start order-lg-first text-center">
                <span v-html="primaryData.copyright_text"></span>
              </div>
            </div>
          </div>
        </div>
        <!-- /.bottom-footer -->
      </div>
    </div>
  </div>
</template>
