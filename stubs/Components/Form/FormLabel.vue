<template>
    <label class="block mb-2 text-sm font-medium text-primary-500/70"
           :for="forId">
        <span v-if="value">{{ value }}</span>
        <span v-else class=""><slot></slot></span>
        <span v-if="required" class="text-red-500 text-base ml-1">*</span></label>
</template>

<script setup>
const props = defineProps({
    value: String,
    forId: String,
    required: {type: Boolean, default: false}
})
</script>

<style scoped>

</style>
