<template>
    <div class="flex flex-col justify-center items-center w-full">
        <form @submit.prevent="submit" class="w-full flex justify-center items-center">
            <Cards class="w-full p-5">
                <template v-slot:header v-if="hasHeader">
                    <div class="w-full flex justify-between items-center mb-2">
                        <div class="text-xl font-semibold text-primary-500 flex justify-start items-center space-x-3">
                            <BackButton @click.prevent="toBack"/>
                            <span v-show="formTitle">{{ formTitle }}</span>
                        </div>
                    </div>
                    <FormDivider/>
                </template>
                <div class="py-12 px-4">

                    <slot/>

                </div>
                <template v-slot:footer>
                    <FormDivider/>
                    <div class="w-full flex justify-end items-center mt-2">
                        <div class="flex justify-end items-center space-x-3 w-2/3">
                            <Button type="submit" class="max-w-max" v-show="formType==='create'" :is-loading="processing"
                                    @click="isAddNew=false" :disabled="processing">
                                <span>Save</span>
                            </Button>
                            <Button type="submit" class="max-w-max" v-show="formType==='create'" v-if="addNew" :is-loading="processing"
                                    @click="isAddNew=true" :disabled="processing">
                                <span>Save and Add New</span>
                            </Button>
                            <Button type="submit" class="max-w-max" v-show="formType==='edit'" :disabled="processing" :is-loading="processing">
                                <span>Update</span>
                            </Button>
                            <!--                        <x-button color="x-btn-warning" >Cancel</x-button>-->
                        </div>
                    </div>
                </template>
            </Cards>
        </form>
    </div>
</template>

<script setup>
import {ref} from "vue";
import Cards from "@/Components/Cards.vue";
import Button from "@/Components/Button.vue";
import FormDivider from "@/Components/Form/FormDivider.vue";
import BackButton from "@/Components/BackButton.vue";
import {usePageSupport} from "@/Composable/page-supports";

const isAddNew = ref(false)
const props = defineProps({
    formTitle: {
        type: String,
    },
    backRoute: {
        type: String,
        default: null
    },
    formType: {
        type: String,
        default: "create"
    },
    processing: {
        type: Boolean,
        default: false
    },
    addNew: {
        type: Boolean,
        default: false
    },
    hasHeader: {
        type: Boolean,
        default: true
    }
})
const {toBack} = usePageSupport()
const emit = defineEmits(['form-submitted'])
const submit = function () {
    emit("form-submitted", {addNew: isAddNew.value});
}
</script>

<style scoped>

</style>
