<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Cancha, SharedData } from '@/types';
import type { PageProps } from '@inertiajs/core';
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    columnFilteringFeature,
    columnVisibilityFeature,
    createColumnHelper,
    createExpandedRowModel,
    createFilteredRowModel,
    createPaginatedRowModel,
    createSortedRowModel,
    filterFn_includesString,
    FlexRender,
    rowExpandingFeature,
    rowPaginationFeature,
    rowSortingFeature,
    sortFn_alphanumeric,
    sortFn_text,
    tableFeatures,
    useTable,
} from '@tanstack/vue-table';
import { createReusableTemplate } from '@vueuse/core';
import { ArrowUpDown, CirclePlus, MoreHorizontal, Pencil, Trash } from 'lucide-vue-next';
import { computed, h } from 'vue';

interface CanchaPageProps extends SharedData, PageProps {
    canchas: Cancha[] | { data: Cancha[] };
}

const page = usePage<CanchaPageProps>();

const canchasList = computed<Cancha[]>(() => {
    const rawData = page.props.canchas;
    if (Array.isArray(rawData)) return rawData;
    return rawData?.data ?? [];
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Canchas',
        href: '/canchas',
    },
];

const [DefineTemplate, ReuseTemplate] = createReusableTemplate<{
    cancha: Cancha;
    onExpand: () => void;
}>();

const features = tableFeatures({
    columnFilteringFeature,
    columnVisibilityFeature,
    rowExpandingFeature,
    rowPaginationFeature,
    rowSortingFeature,
    expandedRowModel: createExpandedRowModel(),
    filteredRowModel: createFilteredRowModel(),
    paginatedRowModel: createPaginatedRowModel(),
    sortedRowModel: createSortedRowModel(),
    filterFns: { includesString: filterFn_includesString },
    sortFns: { alphanumeric: sortFn_alphanumeric, text: sortFn_text },
});

const columnHelper = createColumnHelper<typeof features, Cancha>();

const columns = columnHelper.columns([
    columnHelper.accessor('nombreCancha', {
        id: 'nombreCancha',
        header: ({ column }) => {
            return h(
                Button,
                {
                    variant: 'ghost',
                    onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
                },
                () => ['Nombre', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
            );
        },
        cell: ({ row }) => h('div', { class: 'font-medium whitespace-nowrap' }, row.getValue('nombreCancha')),
    }),
    columnHelper.accessor('esTechada', {
        id: 'esTechada',
        header: 'Techada',
        cell: ({ row }) => {
            const isTechada = row.getValue('esTechada');
            return h('div', { class: 'text-center' }, isTechada ? 'Sí' : 'No');
        },
    }),
    columnHelper.accessor('precio', {
        id: 'precio',
        header: ({ column }) => {
            return h('div', { class: 'text-right' }, [
                h(
                    Button,
                    {
                        variant: 'ghost',
                        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
                    },
                    () => ['Precio', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
                ),
            ]);
        },
        cell: ({ row }) => {
            const amount = Number.parseFloat(row.getValue('precio'));
            const formatted = new Intl.NumberFormat('es-AR', {
                style: 'currency',
                currency: 'ARS',
            }).format(amount);

            return h('div', { class: 'text-right font-medium whitespace-nowrap' }, formatted);
        },
    }),
    columnHelper.display({
        id: 'actions',
        enableHiding: false,
        cell: ({ row }) => {
            const cancha = row.original;
            return h(ReuseTemplate, {
                cancha,
                onExpand: () => row.toggleExpanded(),
            });
        },
    }),
]);

const table = useTable({
    features,
    data: canchasList,
    columns,
});
</script>

<template>
    <DefineTemplate v-slot="{ cancha }">
        <DropdownMenu>
            <DropdownMenuTrigger as-child>
                <Button variant="ghost" class="h-8 w-8 p-0">
                    <span class="sr-only">Abrir menú</span>
                    <MoreHorizontal class="h-4 w-4" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
                <DropdownMenuLabel>Acciones</DropdownMenuLabel>
                <DropdownMenuItem as-child>
                    <Link :href="route('canchas.edit', { cancha: cancha.id })" class="flex w-full cursor-pointer items-center">
                        <Pencil class="mr-2 h-4 w-4" /> Editar
                    </Link>
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem class="cursor-pointer text-red-600 focus:text-red-600">
                    <Trash class="mr-2 h-4 w-4" /> Eliminar
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    </DefineTemplate>

    <Head title="Canchas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Wrapper principal con ancho máximo y ocultamiento estricto de desbordamiento exterior -->
        <div class="w-full max-w-full overflow-hidden p-4 sm:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between pb-4">
                <Input
                    class="w-full sm:max-w-sm"
                    placeholder="Filtrar por nombre..."
                    :model-value="table.getColumn('nombreCancha')?.getFilterValue() as string"
                    @update:model-value="table.getColumn('nombreCancha')?.setFilterValue($event)"
                />
                <Button as-child size="sm" class="w-full sm:w-auto bg-green-600 text-white hover:bg-green-700">
                    <Link :href="route('canchas.create')">
                        <CirclePlus class="mr-2 h-4 w-4" /> Nueva Cancha
                    </Link>
                </Button>
            </div>

            <!-- Caja contenedora con ancho estricto calculando padding mobile (100vw - 2rem) -->
            <div class="w-[calc(100vw-2rem)] sm:w-full overflow-x-auto rounded-md border">
                <!-- Se asigna un min-width a la tabla para forzar el scroll interno en pantallas chicas -->
                <Table class="min-w-[500px] w-full">
                    <TableHeader>
                        <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                            <TableHead v-for="header in headerGroup.headers" :key="header.id">
                                <FlexRender v-if="!header.isPlaceholder" :header="header" />
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <template v-if="table.getRowModel().rows?.length">
                            <template v-for="row in table.getRowModel().rows" :key="row.id">
                                <TableRow>
                                    <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                                        <FlexRender :cell="cell" />
                                    </TableCell>
                                </TableRow>
                            </template>
                        </template>

                        <TableRow v-else>
                            <TableCell :colspan="columns.length" class="h-24 text-center">
                                No se encontraron canchas.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div class="flex items-center justify-end space-x-2 py-4">
                <Button variant="outline" size="sm" :disabled="!table.getCanPreviousPage()" @click="table.previousPage()">
                    Anterior
                </Button>
                <Button variant="outline" size="sm" :disabled="!table.getCanNextPage()" @click="table.nextPage()">
                    Siguiente
                </Button>
            </div>
        </div>
    </AppLayout>
</template>