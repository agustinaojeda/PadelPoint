import { watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'

export function useFeedback() {
    const page = usePage()

    watch(
        () => page.props.flash as { success?: string; error?: string; info?: string },
        (flash) => {
            if (flash?.success) toast.success(flash.success)
            if (flash?.error) toast.error(flash.error)
            if (flash?.info) toast.info(flash.info)
        },
        { immediate: true, deep: true }
    )
}