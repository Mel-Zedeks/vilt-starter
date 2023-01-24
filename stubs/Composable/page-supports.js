import { isEmpty} from "lodash";
import {usePage} from "@inertiajs/inertia-vue3";

export function usePageSupport() {
    const toBack = () => {
        window.history.back()
    }

    const toSubmit = (form, e, formType = "create", router, option = {},transform={}) => {


        let options = isEmpty(option) ? {
            // onSuccess: () => {
            //     form.reset()
            //     form.clearErrors()
            // }
        } : option
        // console.log(form, e, formType, router, options,{...options, only: Object.keys(router['params'])})
        if (formType === "edit") {
            form.transform((data) => ({
                ...data,
                stayOnPage: e.addNew,
                ...transform
            })).patch(route(router['prefix'] + '.update', router['params']),options)
        } else {
            form.transform((data) => ({
                ...data,
                stayOnPage: e.addNew,
                ...transform
            })).post(route(router['prefix'] + '.store'), options)
        }

    }
    const canDo=(permission)=>{
       return !!usePage().props.value.auth.user.user_permissions.find(p => p === permission)
    }
    return {toBack, toSubmit,canDo}
}
