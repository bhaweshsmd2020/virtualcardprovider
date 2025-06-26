<script setup>
import { usePage } from '@inertiajs/vue3'
import footerComposable from '@/Composables/footerComposable'
import LanguageSwitch from '@/Components/Web/LanguageSwitch.vue'
const page = usePage()
const primaryData = page.props.primaryData
const { footerRight, footerCenter, footerLeft, socials } = footerComposable()
</script>
<template>
  <div class="footer-two" style="
    margin-top: 50px;">
    
        <div class="container">
          <div class="row justify-content-between" style="position: relative;
    top: 150px;">
            <div class="col-xl-3 col-lg-4 footer-intro ">
              <div class="d-flex logo md-mb-20 mb-15 justify-content-center">
                <Link href="/">
                  <img v-lazy="primaryData.logo" alt="logo" class="object-fit-cover" />
                </Link>
              </div>
              <!-- logo -->
              <p v-html="primaryData.slogan" class="lh-sm md-mb-20"></p>

              <ul
                v-if="Object.keys(socials).length"
                class="style-none d-flex align-items-center social-icon footer-social-items mb-20 mt-20"
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
              <template v-if="Object.keys($page.props?.languages ?? {}).length > 1">
                <LanguageSwitch classes="mt-5 mb-10" />
              </template>
            </div>
            <div class="col-lg-2 col-sm-4 mb-20" >
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
           
          <div class="row justify-content-between footer-content">
             <div  class="copyright-text text-center mb-3"
                :class="{ noLang: Object.keys($page.props?.languages ?? {}).length < 2 }"
              > Virtual card provider ( VCP) is a technology company, not a bank or an FDIC member. Company offers PSP and Card issuing services through partnership with FDIC member banks in USA.</div> 
                <div class="copyright text-center mb-2"
                :class="{ noLang: Object.keys($page.props?.languages ?? {}).length < 2 }"
                v-html="primaryData.copyright_text"
              ></div>
          </div>
          
          </div>
        </div>
        <img
          src="/assets/images/lazy.svg"
          data-src="/assets/images/shape/shape_06.svg"
          alt=""
          class="lazy-img shapes shape_01"
        />
        <img
          src="/assets/images/lazy.svg"
          data-src="/assets/images/shape/shape_06.svg"
          alt=""
          class="lazy-img shapes shape_02"
        />
     
  </div>
</template>
