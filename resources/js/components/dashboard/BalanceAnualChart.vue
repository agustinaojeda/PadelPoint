<script setup lang="ts">
import { computed, ref } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { TrendingUp } from 'lucide-vue-next';
import { formatPrecio } from '@/utils/formateo';

interface BalanceItem {
    mes: string;
    num_mes: number;
    total: number;
}

const props = defineProps<{
    data: BalanceItem[];
}>();

const hoveredIndex = ref<number | null>(null);

const width = 600;
const height = 180;
const padding = 30;

const maxVal = computed(() => {
    const max = Math.max(...props.data.map((d) => d.total), 0);
    return max === 0 ? 1000 : max * 1.15;
});

const points = computed(() => {
    if (!props.data.length) return [];
    const stepX = (width - padding * 2) / (props.data.length - 1);

    return props.data.map((item, index) => {
        const x = padding + index * stepX;
        const y = height - padding - (item.total / maxVal.value) * (height - padding * 2);
        return { x, y, ...item };
    });
});

const pathD = computed(() => {
    if (points.value.length === 0) return '';
    return points.value.reduce((acc, point, i) => {
        if (i === 0) return `M ${point.x},${point.y}`;
        const prev = points.value[i - 1];
        const cx = (prev.x + point.x) / 2;
        return `${acc} C ${cx},${prev.y} ${cx},${point.y} ${point.x},${point.y}`;
    }, '');
});

const areaD = computed(() => {
    if (!pathD.value || points.value.length === 0) return '';
    const lastX = points.value[points.value.length - 1].x;
    const firstX = points.value[0].x;
    const bottomY = height - padding;
    return `${pathD.value} L ${lastX},${bottomY} L ${firstX},${bottomY} Z`;
});

const totalAnual = computed(() => props.data.reduce((acc, curr) => acc + curr.total, 0));
</script>

<template>
    <Card class="w-full">
        <CardHeader class="flex flex-row items-center justify-between pb-2 border-b">
            <div>
                <CardTitle class="text-base font-semibold flex items-center gap-2">
                    <TrendingUp class="h-4 w-4 text-emerald-500" />
                    Balance e Ingresos Anuales
                </CardTitle>
                <p class="text-xs text-muted-foreground mt-0.5">Evolución mes a mes del año en curso</p>
            </div>
            <div class="text-right">
                <span class="text-xs text-muted-foreground block">Total Acumulado</span>
                <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">
                    {{ formatPrecio(totalAnual) }}
                </span>
            </div>
        </CardHeader>

        <CardContent class="pt-4">
            <div class="relative w-full overflow-x-auto">
                <svg :viewBox="`0 0 ${width} ${height}`" class="w-full h-auto overflow-visible">
                    <defs>
                        <linearGradient id="balanceGradient" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#10b981" stop-opacity="0.35" />
                            <stop offset="100%" stop-color="#10b981" stop-opacity="0.0" />
                        </linearGradient>
                    </defs>

                    <!--guias horizontales-->
                    <line :x1="padding" :y1="padding" :x2="width - padding" :y2="padding" class="stroke-border/40" stroke-dasharray="3 3" />
                    <line :x1="padding" :y1="height / 2" :x2="width - padding" :y2="height / 2" class="stroke-border/40" stroke-dasharray="3 3" />
                    <line :x1="padding" :y1="height - padding" :x2="width - padding" :y2="height - padding" class="stroke-border/60" />

                    <!--area-->
                    <path v-if="areaD" :d="areaD" fill="url(#balanceGradient)" />

                    <!--linea-->
                    <path v-if="pathD" :d="pathD" fill="none" class="stroke-emerald-500" stroke-width="2.5" stroke-linecap="round" />

                    <!--puntos e interaccion-->
                    <g v-for="(p, index) in points" :key="p.mes">
                        <circle
                            :cx="p.x"
                            :cy="p.y"
                            :r="hoveredIndex === index ? 6 : 3.5"
                            class="fill-background stroke-emerald-500 transition-all duration-200 cursor-pointer"
                            stroke-width="2"
                            @mouseenter="hoveredIndex = index"
                            @mouseleave="hoveredIndex = null"
                        />

                        <!--est del mes-->
                        <text
                            :x="p.x"
                            :y="height - 8"
                            text-anchor="middle"
                            class="text-[10px] fill-muted-foreground font-medium select-none"
                        >
                            {{ p.mes }}
                        </text>

                        <g v-if="hoveredIndex === index" class="transition-opacity duration-200 pointer-events-none">
                            <rect
                                :x="p.x - 45"
                                :y="p.y - 32"
                                width="90"
                                height="24"
                                rx="5"
                                class="fill-popover stroke-border shadow-md"
                            />
                            <text
                                :x="p.x"
                                :y="p.y - 16"
                                text-anchor="middle"
                                class="text-[10px] font-bold fill-popover-foreground select-none"
                            >
                                ${{ p.total.toLocaleString('es-AR') }}
                            </text>
                        </g>
                    </g>
                </svg>
            </div>
        </CardContent>
    </Card>
</template>