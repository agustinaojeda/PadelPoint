<script setup lang="ts">
defineProps<{
    open: boolean;
    title: string;
    description?: string;
    maxWidth?: string;
}>();

defineEmits<{
    (e: 'close'): void;
}>();
</script>

<template>
    <Teleport to="body">
        <div
            v-if="open"
            @click.self="$emit('close')"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm duration-200 animate-in fade-in"
        >
            <div
                class="max-h-[90vh] w-full cursor-default overflow-y-auto rounded-xl border bg-background p-6 shadow-xl transition-all"
                :class="maxWidth || 'max-w-lg'"
            >
                <div class="mb-4">
                    <h2 class="text-xl font-bold tracking-tight">{{ title }}</h2>
                    <p v-if="description" class="mt-0.5 text-sm text-muted-foreground">{{ description }}</p>
                </div>

                <slot />
            </div>
        </div>
    </Teleport>
</template>
