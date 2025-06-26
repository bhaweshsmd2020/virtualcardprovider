<script setup>
import Header from '@/Layouts/Default/Header.vue'
import HeaderTwo from '@/Layouts/Default/HeaderTwo.vue'
import FooterTwo from '@/Layouts/Default/FooterTwo.vue'
import SeoMeta from '@/Components/Web/SeoMeta.vue'
import ScrollToTopBtn from '@/Layouts/Default/ScrollToTopBtn.vue'
import ToastrContainer from '@/Components/ToastrContainer.vue'

</script>
<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/679b897c825083258e0dc0d5/1iirp24b6';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<template>
  <SeoMeta />
  <ScrollToTopBtn />
  <ToastrContainer />
  <main class="main-page-wrapper">
    <Header v-if="$page.url === '/'" />
    <HeaderTwo v-else />
    <slot />
    <FooterTwo />
  </main>
</template>
