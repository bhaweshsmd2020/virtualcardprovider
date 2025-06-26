<script setup>
import Sidebar from "@/Layouts/Admin/Sidebar.vue"
import Header from "@/Layouts/Admin/Header.vue"
import ToastrContainer from "@/Components/ToastrContainer.vue"
import ValidationErrors from "@/Components/Admin/ValidationErrors.vue"
import Modal from "@/Components/Admin/Modal.vue"
import ActionModal from "@/Components/Admin/ActionModal.vue"
import { navMenuItems } from "./menu"

import dropdown from "@/Plugins/Admin/dropdown"
import { onMounted, onUpdated } from "vue"
onMounted(function () {
  dropdown.init()
})
onUpdated(function () {
  dropdown.init()
})
</script>

<template>
  <div>
    <ToastrContainer />
    <ActionModal />
    <Modal />
    <Sidebar :navMenuItems="navMenuItems" />
    <div class="wrapper">
      <Header :navMenuItems="navMenuItems" panel="user" />
      <ValidationErrors />
      <div class="content">
        <slot></slot>
      </div>
    </div>
  </div>
</template>


<style>
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

  div:where(.swal2-container) button:where(.swal2-styled):where(.swal2-confirm) {
    background-color: #026f5e !important;
    color: #fff !important;
  }

  div:where(.swal2-container) button:where(.swal2-styled):where(.swal2-confirm):focus-visible {
    box-shadow: none !important;
  }

  .btn-primary{
    background-color: #026f5e !important;
  }

  .sidebar-menu.active{
    color: #026f5e !important;
    border-radius: 10px;
  }

  .sidebar-menu:hover{
    border-radius: 10px;
  }

  .bg-primary-100 {
    background-color: rgb(188 245 155 / 58%) !important;
  }

  .sidebar-content {
    border-top-right-radius: 25px;
  }

  .sidebar-submenu-item{
    color: rgb(201 213 209);
  }

  .sidebar-submenu-item:hover{
    --tw-bg-opacity: 1;
    background-color: rgb(188 245 155 / var(--tw-bg-opacity));
    --tw-text-opacity: 1;
    color: #026f5e !important;
  }
</style>
