<template>
    <p v-if="errorMessage" class="mt-2 text-red-600 text-sm">
        {{ errorMessage }}
    </p>
</template>

<script setup>
import {usePage} from "@inertiajs/inertia-vue3";
import {computed} from "vue";

const props = defineProps({
    name:{
        type:String,
        required:true
    }
})
const errors = computed(() => {
    return usePage().props.value.errors
})
const errorMessage = computed(() => {
    return errors.value.hasOwnProperty(props.name)?errors.value[props.name]:""
})
</script>

<style scoped>

</style>
