<template>
    <div class="relative flex justify-start items-center space-x-2">
        <button :class="color"
                class="bg-blue-200/50 rounded-full font-bold uppercase px-3 py-3 outline-none focus:outline-none  ease-linear transition-all duration-150" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="20"  fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>

        </button>
        <div>
            <slot></slot>
        </div>
    </div>
</template>

<script setup>
import {Inertia} from "@inertiajs/inertia";

const props=defineProps({
    color:{
        type:String,
        default:"text-primary-500 "
    }
})

</script>

<style scoped>

</style>
