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
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 animate-in fade-in duration-200"
        >
            <div 
                class="bg-background rounded-xl shadow-xl w-full p-6 border max-h-[90vh] overflow-y-auto cursor-default transition-all"
                :class="maxWidth || 'max-w-lg'"
            >
                <div class="mb-4">
                    <h2 class="text-xl font-bold tracking-tight">{{ title }}</h2>
                    <p v-if="description" class="text-sm text-muted-foreground mt-0.5">{{ description }}</p>
                </div>

                <slot />
            </div>
        </div>
    </Teleport>
</template>