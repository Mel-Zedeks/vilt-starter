<template>
    <input
        :value="value" v-model="proxyChecked" type="checkbox"
        class=" border border-primary-800 border-opacity-60 rounded text-primary-500
         focus:outline-none focus:border-primary-800 focus:border-opacity-30 focus:ring-[2px] focus:ring-primary-500 focus:ring-opacity-50
        focus:ring-offset-0 mx-2
         ml-1 w-4 h-4 ease-linear transition-all duration-150">

</template>

<script setup>

import {computed} from "vue";

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        default: false,
    },
    value: {
        default: false,
    },
})
const emit=defineEmits(['update:checked'])
const proxyChecked = computed({
        get() {
            return props.checked;
        },
        set(val) {
            emit("update:checked", val);
        },
    }
)
</script>

<style scoped>

</style>
