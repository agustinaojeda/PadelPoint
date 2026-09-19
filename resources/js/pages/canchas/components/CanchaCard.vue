<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import type { Cancha } from '@/types';
import { formatDuracion, formatPrecio, formatearDias} from '@/utils/formateo';

const props = defineProps<{
    cancha: Cancha;
}>();

defineEmits<{
    (e: 'edit', cancha: Cancha): void;
    (e: 'delete', id: number): void;
    (e: 'activar', cancha: Cancha): void;
}>();


</script>

<template>
    <Card class="flex flex-col justify-between overflow-hidden">
        <div>
            <div class="relative h-48 w-full overflow-hidden bg-muted">
                <img :src="cancha.imagen_url || '/images/cancha-default.webp'" :alt="cancha.nombre" class="h-full w-full object-cover" />
                <Badge class="absolute right-3 top-3" :variant="cancha.esta_disponible ? 'default' : 'destructive'">
                    {{ cancha.esta_disponible ? 'Disponible' : 'Inactiva' }}
                </Badge>
            </div>

            <CardHeader class="pb-3">
                <CardTitle class="text-xl font-bold">{{ cancha.nombre }}</CardTitle>
                <div class="flex items-baseline gap-1">
                    <span class="text-lg font-bold text-emerald-600">
                        {{ formatPrecio(cancha.precio) }}
                    </span>
                    <span class="text-sm font-light text-emerald-600">
                        /por turno
                    </span>
                </div>
                <CardDescription v-if="cancha.descripcion && cancha.descripcion.trim()" class="mt-1 line-clamp-2">
                    {{ cancha.descripcion }}
                </CardDescription>
            </CardHeader>

            <CardContent class="space-y-3 text-sm">
                <div class="flex flex-wrap gap-2">
                    <Badge variant="outline">{{ cancha.es_techada ? 'Techada' : 'Descubierta' }}</Badge>
                    <Badge v-if="cancha.superficie && cancha.superficie.trim()" variant="outline">
                        {{ cancha.superficie }}
                    </Badge>
                    <Badge variant="outline">Turnos de {{ formatDuracion(cancha.duracion_turno) }}</Badge>
                </div>
                <div class="border-t pt-2 text-xs text-muted-foreground">
                    Horario: {{ cancha.hora_apertura.substring(0, 5) }} a {{ cancha.hora_cierre.substring(0, 5) }} hs
                </div>
                <div class="text-xs text-muted-foreground">Días: 
                        {{ formatearDias(cancha.dias_disponibles) }}
                    </div>
            </CardContent>
        </div>

        <CardFooter class="flex gap-2 border-t pt-2">
            
            <Button v-if="cancha.esta_disponible" variant="secondary" class="w-full" size="sm" @click="$emit('delete', cancha.id)"> Desactivar 
            </Button>
            <Button v-else variant="secondary" class="w-full" size="sm" @click="$emit('activar', cancha)"> Activar 
            </Button>

            <Button v-if="cancha.esta_disponible" class="w-full" size="sm" @click="$emit('edit', cancha)"> Editar </Button>
        </CardFooter>
    </Card>
</template>
