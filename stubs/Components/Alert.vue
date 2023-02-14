<template>
    <div v-if="showAlert"
         class="border border-primary-500/30 shadow  font-medium text-sm mb-4 px-6 py-2 rounded space-x-2
flex justify-start items-start "
    :class="{'is-error':style==='error','is-success':style==='success','is-info':style==='info','is-warning':style==='warning'}">

        <span class=" animate-pulse" v-if="style==='error'">
            <font-awesome-icon icon="fa-solid fa-times-circle" ></font-awesome-icon>
        </span>
        <span class=" animate-pulse" v-if="style==='warning'">
            <font-awesome-icon icon="fa-solid fa-warning" ></font-awesome-icon>
        </span>
        <span class=" animate-pulse" v-if="style==='info'">
            <font-awesome-icon icon="fa-solid fa-info-circle" ></font-awesome-icon>
        </span>
        <span class=" animate-pulse" v-if="style==='success'">
            <font-awesome-icon icon="fa-solid fa-check-circle" ></font-awesome-icon>
        </span>

        <div>
            {{ message }}
        </div>

    </div>
</template>

<script setup>
import {computed, onMounted, ref, watch} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";

const showAlert = ref(false)
const calledBy = ref('')
const revealMessage = function (caller) {
    if (caller === calledBy.value) {
        showAlert.value = true
        setTimeout(() => {
            closeBanner()
        }, 5000);
    }
}

const closeBanner = () => {
    showAlert.value = false
    calledBy.value = ''
    usePage().props.value.notifications.alertMessage = null
}
const style = computed(() => usePage().props.value.notifications?.alertStyle || 'success')
const message = computed(() => usePage().props.value.notifications?.alertMessage || '')
onMounted(() => {
    if (message.value !== "") {
        if (calledBy.value === '') {
            calledBy.value = 'mounted'
            revealMessage('mounted')
        }
    }
})
watch(message, (msg) => {
    if (message.value)
        if (calledBy.value === '') {
            calledBy.value = 'updated'
            revealMessage('updated')

        }
});
</script>

<style scoped>

.is-success{
    @apply bg-emerald-700/30 text-emerald-700
}
.is-error{
    @apply bg-red-600/30 text-red-700
}
.is-warning{
    @apply bg-orange-500/30 text-orange-700
}
.is-info{
    @apply bg-sky-700/30 text-sky-700
}

</style>
