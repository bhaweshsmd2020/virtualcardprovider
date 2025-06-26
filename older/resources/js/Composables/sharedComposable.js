import { usePage, router } from '@inertiajs/vue3'
import { computed } from 'vue'
import trans from '@/Composables/transComposable'
import toast from '@/Composables/toastComposable'
import { modal } from '@/Composables/actionModalComposable'

export default () => {
    const textExcerpt = (text, length = 30) => {
        if (text) {
            return text?.length > length ? text?.substring(0, length) + '...' : text
        }
        return text || ''
    }
    const currentRoute = (route) => {
        return usePage().component === route
    }
    const currentRouteGroup = (route) => {
        const componentLocation = usePage().component.split('/').slice(0, 2).join('/')
        return componentLocation === route
    }

    const authUser = computed(() => {
        return usePage().props.user
    })

    const logout = () => {
        router.post('/logout')
    }

    const formatNumber = (num, precision = 1) => {
        const map = [
            { suffix: 'T', threshold: 1e12 },
            { suffix: 'B', threshold: 1e9 },
            { suffix: 'M', threshold: 1e6 },
            { suffix: 'K', threshold: 1e3 },
            { suffix: '', threshold: 1 }
        ]

        const found = map.find((x) => Math.abs(num) >= x.threshold)
        if (found) {
            const formatted = (num / found.threshold).toFixed(precision) + found.suffix
            return formatted
        }

        return num
    }

    const deleteRow = (actionUrl) => {
        modal.init(actionUrl, {
            method: 'delete',
            options: {
                message: trans('You would not be revert it back!'),
                confirm_text: trans('Are you sure?'),
                accept_btn_text: trans('Yes, Delete'),
                reject_btn_text: trans('No, Cancel')
            }
        })
    }
    const formatCurrency = (amount = 0, precision = 2, iconType = 'name') => {
        let formattedCurrency = ''
        if (!(typeof amount === 'number')) {
            return ''
        }
        const currency = usePage().props.currency
        if (iconType === 'name') {
            formattedCurrency =
                currency.position === 'right'
                    ? currency.name + ' ' + amount.toFixed(precision)
                    : currency.icon + ' ' + amount.toFixed(precision)
        } else if (iconType === 'both') {
            formattedCurrency = currency.icon + amount.toFixed(precision) + ' ' + currency.name
        } else {
            formattedCurrency =
                currency.position === 'right'
                    ? amount.toFixed(precision) + currency.icon
                    : currency.icon + amount.toFixed(precision)
        }

        return formattedCurrency
    }
    const formatAmount = (amount = 0, precision = 2) => {
        let formattedCurrency = ''
        if (!(typeof amount === 'number')) {
            return ''
        }
        formattedCurrency = amount.toFixed(precision)

        return formattedCurrency
    }
    const pickBy = (obj) => {
        const result = {}

        for (const key in obj) {
            const value = obj[key]

            if (value !== undefined && value !== null && value !== '') {
                if (Array.isArray(value) && value.length === 0) {
                    continue // Skip empty arrays
                } else if (typeof value === 'object' && Object.keys(value).length === 0) {
                    continue // Skip empty objects
                }

                result[key] = value
            }
        }

        return result
    }

    const getQueryParams = () => {
        const obj = {}
        const para = new URLSearchParams(window.location.search)

        for (const [key, value] of para) {
            if (obj.hasOwnProperty(key)) {
                if (Array.isArray(obj[key])) {
                    obj[key].push(value)
                } else {
                    obj[key] = [obj[key], value]
                }
            } else {
                obj[key] = value
            }
        }

        return obj
    }
    //copy text
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text)
        toast.success('Copied to clipboard')
    }
    function trim(text) {
        return text.replace(/[_-]/g, ' ')
    }

    function socialShare(media, url = null) {
        let shareableLinks = {
            facebook: 'https://www.facebook.com/sharer/sharer.php?u=',
            twitter: 'https://twitter.com/intent/tweet?url=',
            pinterest: 'https://pinterest.com/pin/create/button/?url=',
            linkedin: 'https://www.linkedin.com/sharing/share-offsite/?url=',
            instagram: 'https://www.instagram.com/?url='
        }
        if (shareableLinks.hasOwnProperty(media)) {
            return shareableLinks[media] + (url ?? window.location.href)
        }
        return 'invalidMediaError'
    }
    function uiAvatar(name = null, avatar = null) {
        return avatar ? avatar : `https://ui-avatars.com/api/?name=${name ? name : 'user'}`
    }
    function calculatePercent(num, percent) {
        return (percent / 100) * num
    }
    function badgeClass(status) {
        const classes = {
            active: 'badge-success',
            approved: 'badge-success',
            reversed: 'badge-success',
            pending: 'badge-warning',
            inactive: 'badge-danger',
            closed: 'badge-danger',
            failed: 'badge-danger',
            rejected: 'badge-danger',
            1: 'badge-success',
            2: 'badge-warning',
            0: 'badge-danger'
        }
        if (classes.hasOwnProperty(status)) {
            return `badge ${classes[status]}`
        } else {
            return 'badge-info'
        }
    }
    return {
        authUser,
        textExcerpt,
        currentRoute,
        currentRouteGroup,
        deleteRow,
        logout,
        formatCurrency,
        pickBy,
        formatNumber,
        getQueryParams,
        copyToClipboard,
        socialShare,
        trim,
        uiAvatar,
        calculatePercent,
        badgeClass,
        formatAmount
    }
}
