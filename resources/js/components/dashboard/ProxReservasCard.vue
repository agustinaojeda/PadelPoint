<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Reserva } from '@/types';
import { formatHora, formatPrecio, sanitizeWhatsAppPhone } from '@/utils/formateo';
import { Link } from '@inertiajs/vue3';
import { ArrowUpRight, Calendar, Clock, Phone, User } from 'lucide-vue-next';

defineProps<{
    reservas: Reserva[];
}>();
</script>

<template>
    <Card class="flex flex-col">
        <CardHeader class="flex flex-row items-center justify-between border-b pb-3">
            <CardTitle class="flex items-center gap-2 text-base font-semibold">
                <Clock class="h-4 w-4 text-primary" />
                Turnos de Hoy
            </CardTitle>
            <Button variant="ghost" size="sm" as-child class="h-7 text-xs">
                <Link :href="route('reservas.index')">
                    Ver todo <ArrowUpRight class="ml-1 h-3.5 w-3.5" />
                </Link>
            </Button>
        </CardHeader>
        <CardContent class="flex-1 pt-4">
            <div
                v-if="reservas.length === 0"
                class="flex flex-col items-center justify-center p-8 text-center text-muted-foreground"
            >
                <Calendar class="mb-2 h-10 w-10 text-muted-foreground/40" />
                <p class="text-sm font-medium">No hay turnos agendados para hoy</p>
            </div>

            <div v-else class="space-y-3">
                <div
                    v-for="reserva in reservas"
                    :key="reserva.id"
                    class="flex items-center justify-between rounded-lg border p-3 text-sm transition hover:bg-muted/40"
                >
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-foreground">
                                {{ formatHora(reserva.hora_inicio) }} - {{ formatHora(reserva.hora_fin) }} hs
                            </span>
                            <Badge variant="outline" class="text-[10px]">
                                {{ reserva.cancha?.nombre || 'Cancha' }}
                            </Badge>
                        </div>
                        <div class="flex items-center gap-3 text-xs text-muted-foreground">
                            <span class="flex items-center gap-1">
                                <User class="h-3 w-3" /> {{ reserva.nombre_cliente }}
                            </span>
                            <a
                                v-if="reserva.telefono_cliente"
                                :href="`https://wa.me/${sanitizeWhatsAppPhone(reserva.telefono_cliente)}`"
                                target="_blank"
                                class="flex items-center gap-1 text-primary hover:underline"
                            >
                                <Phone class="h-3 w-3" /> {{ reserva.telefono_cliente }}
                            </a>
                        </div>
                    </div>

                    <div class="text-right">
                        <span class="text-sm font-semibold text-foreground">
                            {{ formatPrecio(reserva.monto_total) }}
                        </span>
                        <Badge
                            class="mt-1 block text-[10px] capitalize"
                            :variant="reserva.estado === 'confirmada' ? 'default' : 'secondary'"
                        >
                            {{ reserva.estado }}
                        </Badge>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>