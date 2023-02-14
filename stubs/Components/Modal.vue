<template>
    <div v-if="modalShow"
        class="transition-opacity ease-in-out duration-200  z-30  fixed w-full h-full top-0 left-0 flex items-center justify-center">
        <div class=" absolute w-full h-full bg-gray-900 z-30 opacity-50"></div>

        <div class="z-50 w-3/4 flex justify-center items-center">
            <Cards class="py-4 px-8 overflow-y-auto w-1/2">
                <template v-slot:header>
                    <div class="w-full flex justify-between items-center">
                        <div class="text-xl font-semibold text-primary-500 flex justify-start items-center space-x-3">
                            <span>{{ title }}</span>
                        </div>
                        <div class="flex justify-end items-center">
                            <span><font-awesome-icon icon="fa-solid fa-times-circle" @click="closeModal" class="h-6 text-red-600 cursor-pointer"/></span>
                        </div>
                    </div>
                    <FormDivider/>
                </template>

                <div class="mb-4">
                    <slot></slot>
                </div>

                <template #footer class="">
                    <div class="pt-4 ">
                        <FormDivider class="mb-2"/>
                        <div class="flex justify-end items-center w-2/3 float-right space-x-2">
                            <Button class="w-fit" color="bg-red-600 hover:bg-red-500 active:bg-red-700"
                                    @click.prevent="cancelModal" v-if="hasCancel">{{ cancelBtn }}
                            </Button>
                            <Button class="w-fit" color="bg-green-600 hover:bg-green-500 active:bg-green-700"
                                    @click.prevent="okModal" v-if="hasOk">{{ okBtn }}
                            </Button>

                        </div>
                    </div>
                </template>
            </Cards>
        </div>
    </div>
</template>

<script setup>

import Cards from "@/Components/Cards.vue";
import FormDivider from "@/Components/Form/FormDivider.vue";
import Button from "@/Components/Button.vue";
import {reactive, ref} from "vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const props = defineProps({
    title: {
        type: String,
        default: ""
    },
    cancelBtn: {
        type: String,
        default: "Cancel"
    },
    hasCancel: {
        type: Boolean,
        default: true
    },
    okBtn: {
        type: String,
        default: "Ok"
    },
    hasOk: {
        type: Boolean,
        default: true
    },
    manualClose: {
        type: Boolean,
        default: false
    },
})



// const emits = defineEmits(['modalClose', 'modalCancel', 'modalOk'])
const modalShow=ref(false)
const resolvePromise=ref('')
const rejectPromise=ref('')

const showModal = ()=>{
    modalShow.value=true
    return new Promise((resolve,reject)=>{
        resolvePromise.value=resolve
        rejectPromise.value=reject
    })
}
const hideModal=()=>{
    modalShow.value=false
}
const closeModal = () => {
    modalShow.value=false
    // emits('modalClose');
}
const okModal = () => {
    if (props.manualClose) {
        hideModal()
    }
     resolvePromise.value({ok:true})
    // emits('modalOk');
}
const cancelModal = () => {
   if (props.manualClose) {
       hideModal()
   }
    rejectPromise.value({cancel:true})
    // emits('modalCancel');
}
defineExpose({
    showModal,hideModal
})
</script>

<style scoped>

</style>
