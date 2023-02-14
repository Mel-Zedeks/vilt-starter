<template>
    <input
        class="border border-primary-800 border-opacity-30 px-3 py-3 placeholder-primary-500/50 text-primary-500 bg-white rounded text-sm shadow focus:outline-none focus:border-primary-800 focus:border-opacity-30 focus:ring-[2px] focus:ring-primary-500 focus:ring-opacity-50 w-full ease-linear
           transition-all duration-150
disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none"
        :value="modelValue" @input="$emit('update:modelValue', $event.target.value)"
        :placeholder="placeholder"/>
</template>

<script setup>

const props = defineProps(['modelValue', 'placeholder'])
defineEmits(['update:modelValue'])
</script>

<style scoped>
.is-valid {
    @apply border-2 border-emerald-500
}

.is-invalid {
    @apply  border-2 border-red-500 text-red-600 focus:border-red-500 focus:border-opacity-30 focus:ring-red-500 focus:ring-opacity-80
}
</style>
