import {Inertia} from "@inertiajs/inertia";
import {useFormattor} from "@/Composible/formattor";
import {pickBy} from "lodash";
import {reactive,ref} from "vue";

export function useZedeksDatatable() {
    const {downloadPdf} = useFormattor()
    const receiveActions = (event) => {
        const routeUrl = route(event.route, event.params)
        Inertia.visit(routeUrl, {
            method: event?.type,
        })
    }
    const exports = (event,name="") => {
        return downloadPdf(event.element, {
            filename: name?name+".pdf":"newpdf.pdf",
        })
    }
    let query= {
        search: "",
        page: "",
        pageLength: "",
        filter_term: "",
        filter_value: "",
        sortKey: "",
        sortDirection: "",
    }
    const processDataFetch = (e, type)=> {

        let all_params = route().params

        // let query = pickBy(this.query)
        if (type === 'search') {
            query.search = e.autoSearch.trim()
        }
        if (type === 'pagination') {
            query.page = e.label
        }
        if (type === 'pageLength') {
            query.pageLength = e.pageLength
        }
        if (type === 'filter') {
            query.filter_term = e.column
            query.filter_value = e.key

        }
        if (type === 'sort') {
            query.sortKey = e.key
            query.sortDirection = e.direction
        }
        let _query = pickBy({...all_params, ...query})

        Inertia.get(route(route().current(), _query), {}, {preserveState: true, preserveScroll: true})
    }
    // const autoSearch = (event) => {
    //     const routeUrl = route(event.route, event.params)
    //     Inertia.visit(routeUrl, {
    //         method: event?.type,
    //     })
    // }

    return {receiveActions, exports, processDataFetch}
}
