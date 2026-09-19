// src/composables/useCanchaForm.ts
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import type { Cancha , FormCanchaData} from '@/types'
import { formatDiasDisponibles } from '@/utils/formateo'

const formDefaults: FormCanchaData = {
    nombre: '',
    superficie: 'Césped sintético',
    es_techada: false,
    precio: 10000,
    descripcion: '',
    duracion_turno: 90,
    cantidad_jugadores: 4,
    hora_apertura: '08:00',
    hora_cierre: '23:00',
    dias_disponibles: [1, 2, 3, 4, 5],
    imagen: null,
    esta_disponible: true,
}

export function useCanchaForm(
    props: { open: boolean; cancha?: Cancha | null },
    emit: (e: 'close') => void
) {
    const imagePreview = ref<string | null>(null)
    const form = useForm<FormCanchaData>({ ...formDefaults })

    const handleImageChange = (e: Event) => {
        const target = e.target as HTMLInputElement
        if (target.files && target.files[0]) {
            const file = target.files[0]
            form.imagen = file
            imagePreview.value = URL.createObjectURL(file)
        }
    }

    const clearSelectedImage = () => {
        form.imagen = null
        imagePreview.value = props.cancha?.imagen_url ?? null
    }

    const sincronizarFormulario = () => {
        form.clearErrors()
        imagePreview.value = props.cancha?.imagen_url ?? null

        if (props.cancha) {
            form.nombre = props.cancha.nombre ?? ''
            form.superficie = props.cancha.superficie ?? 'Césped sintético'
            form.es_techada = Boolean(props.cancha.es_techada)
            form.esta_disponible = Boolean(props.cancha.esta_disponible)
            form.precio = props.cancha.precio ?? 0
            form.descripcion = props.cancha.descripcion ?? ''
            form.duracion_turno = props.cancha.duracion_turno ?? 90
            form.cantidad_jugadores = props.cancha.cantidad_jugadores ?? 4
            form.hora_apertura = props.cancha.hora_apertura
                ? String(props.cancha.hora_apertura).substring(0, 5)
                : '08:00'
            form.hora_cierre = props.cancha.hora_cierre
                ? String(props.cancha.hora_cierre).substring(0, 5)
                : '23:00'
            form.dias_disponibles = formatDiasDisponibles(props.cancha.dias_disponibles)
            form.imagen = null
            delete form._method
        } else {
            form.defaults({ ...formDefaults })
            form.reset()
            delete form._method
        }
    }

    watch(
        [() => props.open, () => props.cancha],
        ([isOpen]) => {
            if (isOpen) {
                sincronizarFormulario()
            }
        },
        { immediate: true, deep: true }
    )

    const submit = () => {
        if (props.cancha) {
            form.transform((data) => ({
                ...data,
                _method: 'put',
            })).post(`/canchas/${props.cancha.id}`, {
                preserveScroll: true,
                onSuccess: () => emit('close'),
            })
        } else {
            form.post('/canchas', {
                preserveScroll: true,
                onSuccess: () => emit('close'),
            })
        }
    }

    return {
        form,
        imagePreview,
        handleImageChange,
        clearSelectedImage,
        submit,
    }
}