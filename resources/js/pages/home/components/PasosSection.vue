<script setup lang="ts">
const pasos = [
    {
        numero: 1,
        titulo: 'Elegí tu cancha',
        descripcion: 'Mirá las canchas disponibles y seleccioná la que mejor te convenga según horario y tipo de turno.',
    },
    {
        numero: 2,
        titulo: 'Confirmá la disponibilidad',
        descripcion: 'Seleccioná fecha, horario y duración del turno para ver la disponibilidad actualizada.',
    },
    {
        numero: 3,
        titulo: 'Completá tu reserva',
        descripcion: 'Ingresá tus datos y revisá el detalle de tu turno antes de confirmar la reserva.',
    },
];
</script>

<template>
    <section id="como-funciona" class="w-full scroll-mt-20 pb-16 ">
        <div class="container mx-auto max-w-6xl px-6 md:px-12">
            <div class="mb-14 text-center">
                <h2 class="text-3xl font-extrabold text-slate-900  md:text-4xl">
                    Reservá en <span class="relative inline-block underline decoration-lime-400 decoration-4 underline-offset-8">tres pasos</span>
                </h2>
                <p class="mt-4 text-sm text-slate-500  md:text-base">
                    Elegí tu cancha, confirmá la disponibilidad y completá tu reserva en minutos.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <div
                    v-for="paso in pasos"
                    :key="paso.numero"
                    class="flex flex-col items-center rounded-3xl border border-slate-200/80 bg-white p-8 text-center transition-all duration-300 hover:border-slate-300 hover:shadow-lg"
                >
                    <div
                        class="mb-6 flex h-14 w-14 items-center justify-center rounded-full bg-slate-950 font-extrabold text-lime-400 shadow-md "
                    >
                        <span class="text-xl">{{ paso.numero }}</span>
                    </div>

                    <h3 class="mb-3 text-xl font-bold text-slate-900 ">
                        {{ paso.titulo }}
                    </h3>

                    <p class="text-sm leading-relaxed text-slate-500 ">
                        {{ paso.descripcion }}
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>
