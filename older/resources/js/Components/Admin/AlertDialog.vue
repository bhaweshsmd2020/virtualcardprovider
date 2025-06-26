<script setup>
import { ref } from 'vue';

const props = defineProps({
    toggleBtn: {
        type: Object,
        default: {
            text: 'Open Dialog'
        }
    }
})

const dialogVisibility = ref(false)
const dialogCardVisibility = ref(false)

const toggleDialogVisibility = () => {
    dialogVisibility.value ? hideDialog() : showDialog()
}

const hideDialog = () => {
    dialogCardVisibility.value = false
    setTimeout(() => {
        dialogVisibility.value = false
    }, 100)
}

const showDialog = () => {
    dialogVisibility.value = true
    setTimeout(() => {
        dialogCardVisibility.value = true
    }, 100)
}

</script>

<template>
    <Transition name="fade">
        <div v-if="dialogVisibility" class="h-screen w-screen bg-black/60 fixed top-0 left-0 z-[100]">
            <div class="flex justify-center items-center h-full" @click.self="hideDialog">
                <Transition name="bounce">
                    <div class="bg-white h-80 w-2/5 rounded-lg shadow" v-if="dialogCardVisibility">
                        <div class="flex h-full items-end p-5 justify-end gap-x-3">
                            <button class="btn border bg-slate-200" @click="toggleDialogVisibility">Close</button>
                            <button class="btn btn-dark">Save</button>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>
    </Transition>
    <button @click="showDialog" class="btn btn-primary">{{ props.toggleBtn.text ?? 'open' }}</button>
</template>

<style>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 200ms ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.bounce-enter-active {
    animation: bounce-in 200ms ease-out;
}

.bounce-leave-active {
    animation: bounce-in 200ms ease-out reverse;
}

@keyframes bounce-in {
    0% {
        transform: scale(0.8);
    }

    100% {
        transform: scale(1);
    }
}
</style>
