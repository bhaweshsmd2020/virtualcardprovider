<script setup>
import ToastrContainer from '@/Components/ToastrContainer.vue'
import SeoMeta from '@/Components/Web/SeoMeta.vue'
import './../../scss/auth.css'
</script>

<template>
  <SeoMeta />
  <ToastrContainer />
  <main class="fix">
    <slot />
  </main>
</template>

<style>
.fix{
  background:#fff7ee;
}
.register-wrapper {
  height: auto;
  padding: 0px 50px;
  box-sizing: border-box;
}

.register-container {
  display: flex; /* ✅ Added to ensure flex layout */
  flex-wrap: wrap; /* ✅ Added to allow wrapping on small screens */
  flex-direction: row; /* ✅ Default: side by side on desktop */
  border-radius: 10px;
  overflow: hidden;
  width: 100%;
  padding: 0px;
  position: relative;
}

.top-left-logo {
  position: absolute;
  top: 35px;
  left: 30px;
  z-index: 10;
}

.top-left-logo img {
  width: 100px;
  height: auto;
}

.register-image {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
}
@media (max-width: 480px) {
  .register-image {
    display: none;
}
}
.register-image img {
  max-width: 100%;
  height: auto;
  padding-top:140px;
  width: 70%;
}

.register-form {
  flex: 1;
  padding: 30px;
  box-sizing: border-box;
  max-height: 100vh;
  overflow-y: auto;
}
.mb-30 {
    margin-bottom: 110px !important;
}
/* ✅ RESPONSIVE STYLES START HERE */

/* Tablets and below */
@media (max-width: 991px) {
  .register-wrapper {
    padding: 20px; /* ✅ Smaller padding on smaller screens */
  }

  .register-container {
    flex-direction: column; /* ✅ Stack image and form vertically */
    align-items: center;
  }

  .register-image,
  .register-form {
    width: 100%;
    flex: unset;
  }

  .register-form {
    padding: 20px; /* ✅ Reduced form padding */
  }

  .register-image img {
    width: 80%;
    padding-top: 40px;
  }

  .top-left-logo {
    top: 20px;
    left: 20px;
  }

  .top-left-logo img {
    width: 80px;
  }
}

/* Phones */
@media (max-width: 767px) {
  .register-wrapper {
    padding: 15px;
  }

  .register-form {
    padding: 15px;
  }

  .register-image img {
    width: 85%;
    padding-top: 30px;
  }
}

/* Extra small phones */
@media (max-width: 575px) {
  .register-wrapper {
    padding: 0px;
  }

  .register-form {
    padding: 15px;
  }

  .register-image img {
    width: 80%;
    padding-top: 20px;
  }

  .mb-30 {
    display: flex;
    margin-bottom: 40px !important; /* ✅ Less space on mobile */
  }

  .signin-button {
    display: block;
    margin-left: auto;  /* Center horizontally */
    margin-right: auto; /* Center horizontally */
    margin-top: 10px;
    width: fit-content;
  }

  .postbox__comment-input {
    margin-bottom: 15px;
  }

  .top-left-logo img {
    width: 60px;
  }
  .span-view {
    display: flex;
    align-items: baseline;
    margin-top: 55px;
}
}

/* ✅ END RESPONSIVE STYLES */

.password-requirements {
  list-style: none;
  padding-left: 0;
  font-size: 14px;
  color: gray;
}

.password-requirements .valid {
  color: green;
}

.error-text {
  color: red;
  font-size: 14px;
  margin-top: 5px;
  font-weight: bold;
}

.signin-button {
  border: 1px solid gray;
  border-radius: 50px;
  padding: 5px 15px;
  font-weight: bold;
  color: #000 !important;
  margin-left: 15px;
}

.postbox__comment-input input:focus ~ .floating-label,
.postbox__comment-input input:not(:focus):valid ~ .floating-label {
  color: #026f5e;
}

.custom-swal-popup {
  position: relative;
  padding: 20px;
  border-radius: 20px !important;
}

.swal-close-btn {
  position: absolute;
  top: 0px;
  right: 10px;
  background: none;
  border: none;
  font-size: 40px;
  cursor: pointer;
  color: #808080a8;
  font-weight: bold;
}

.swal-close-btn:hover {
  color: #ff0000;
}

div:where(.swal2-container)
  button:where(.swal2-styled):where(.swal2-confirm):focus-visible {
  box-shadow: none !important;
}

div:where(.swal2-container) button:where(.swal2-styled):where(.swal2-confirm) {
  background-color: #026f5e !important;
}

div:where(.swal2-container) button:where(.swal2-styled):where(.swal2-confirm):focus-visible {
  box-shadow: none !important;
}
</style>