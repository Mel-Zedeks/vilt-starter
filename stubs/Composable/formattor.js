import moment from "moment";
import html2pdf from "html2pdf.js";

export function useFormattor() {
    /*
    Date convertor
    * */
    const toInputField = (date => moment(date).format('YYYY-MM-DD'))

    const toDefault = (date => moment(date).format('D MMMM YYYY'))
    /*
    * number convertor to money
    * */
    const toMoney = (amount => new Intl.NumberFormat('en-US', {
        // style: 'currency',
        // currency: 'USD',

        // These options are needed to round to whole numbers if that's what you want.
        minimumFractionDigits: 2, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
        maximumFractionDigits: 2, // (causes 2500.99 to be printed as $2,501)
    }).format(amount));


    /*
    * string convertor and formatter
    * */
    const toTitle=(text => {
        text = text.replace(/_/g, " ")
        const arr = text.split(" ");
        for (let i = 0; i < arr.length; i++) {
            arr[i] = arr[i].charAt(0).toUpperCase() + arr[i].slice(1);
        }
        return arr.join(" ")
    })

    /*
    * pdf converter
    * */
    const paginateElements = function (element) {

        const parentElement = element // .firstChild
        const ArrOfContentChildren = Array.from(element.children)

        let childrenHeight = 0

        /* Loop through Elements and add there height with childrenHeight variable. Once the childrenHeight is >= this.paginateElementsByHeight, create a div with a class named 'html2pdf__page-break' and insert the element before the element that will be in the next page */
        for (const childElement of ArrOfContentChildren) {
            // Get The First Class of the element
            const elementFirstClass = childElement.classList[0]
            const isPageBreakClass = elementFirstClass === 'html2pdf__page-break'
            if (isPageBreakClass) {
                childrenHeight = 0
            } else {
                // Get Element Height
                const elementHeight = childElement.clientHeight

                // Get Computed Margin Top and Bottom
                const elementComputedStyle =
                    childElement.currentStyle || window.getComputedStyle(childElement)
                const elementMarginTopBottom =
                    parseInt(elementComputedStyle.marginTop) +
                    parseInt(elementComputedStyle.marginBottom)

                // Add Both Element Height with the Elements Margin Top and Bottom
                const elementHeightWithMargin =
                    elementHeight + elementMarginTopBottom

                if (
                    childrenHeight + elementHeight <
                    1400
                ) {
                    childrenHeight += elementHeightWithMargin
                } else {
                    const section = document.createElement('div')
                    section.classList.add('html2pdf__page-break')
                    parentElement.insertBefore(section, childElement)

                    // Reset Variables made the upper condition false
                    childrenHeight = elementHeightWithMargin
                }
            }
        }
    }
    const downloadPdf = async function (element, options = {}) {
        const localOptions = {
            margin: 0,
            image: {
                type: 'jpeg',
                quality: 0.98,
            },
            filename: "newPdf.pdf",
            enableLinks: false,
            html2canvas: {
                scale: 2,
                useCORS: true,
            },
            jsPDF: {
                unit: 'in',
                format: "a4",
                orientation: "portrait",
            },
        }
        paginateElements(element)
        const pfdObject = html2pdf().set({...localOptions, ...options}).from(element)
        let pdfBlobUrl = null
        pdfBlobUrl = await pfdObject.save().output('bloburl')
        if (pdfBlobUrl) {
            const res = await fetch(pdfBlobUrl)
            return await res.blob()
        }
    }

    const toSlug=(text, separator = "-")=>{
        return text
            .toString()
            .normalize('NFD')                   // split an accented letter in the base letter and the accent
            .replace(/[\u0300-\u036f]/g, '')   // remove all previously split accents
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9 ]/g, '')   // remove all chars not letters, numbers and spaces (to be replaced)
            .replace(/\s+/g, separator);
    }
    return {toInputField, toDefault, toMoney,downloadPdf,toTitle, toSlug}
}
