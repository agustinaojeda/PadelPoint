import { ref, watch, onMounted, onUnmounted, type Ref } from 'vue'
import { router } from '@inertiajs/vue3'

interface PaginatedData<T> {
    data: T[];
    current_page: number;
    next_page_url: string | null;
}

export function useScrollInfinito<T extends { id: number | string }>(
    paginatedProps: () => PaginatedData<T>,
    onlyPropKey: string = 'canchas'
) {
    const items = ref<T[]>([...paginatedProps().data]) as Ref<T[]>
    const cargando = ref(false)
    const triggerScroll = ref<HTMLElement | null>(null)
    let observer: IntersectionObserver | null = null

    watch(
        paginatedProps,
        (newData) => {
            if (newData.current_page === 1) {
                items.value = [...newData.data]
            } else {
                const idsExistentes = new Set(items.value.map(item => item.id))
                const unicas = newData.data.filter(item => !idsExistentes.has(item.id))
                items.value = [...items.value, ...unicas]
            }
            cargando.value = false
        }
    )

    const cargarMas = () => {
        const data = paginatedProps()
        if (cargando.value || !data.next_page_url) return

        cargando.value = true
        router.get(
            data.next_page_url,
            {},
            {
                preserveState: true,
                preserveScroll: true,
                only: [onlyPropKey],
            }
        )
    }

    onMounted(() => {
        observer = new IntersectionObserver(
            (entries) => {
                if (entries[0].isIntersecting && paginatedProps().next_page_url) {
                    cargarMas()
                }
            },
            { rootMargin: '150px' }
        )

        if (triggerScroll.value) {
            observer.observe(triggerScroll.value)
        }
    })

    onUnmounted(() => {
        if (observer) observer.disconnect()
    })

    return {
        items,
        cargando,
        triggerScroll,
    }
}