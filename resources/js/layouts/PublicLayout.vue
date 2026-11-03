<script setup lang="ts">
import PublicFooter from '@/components/PublicFooter.vue';
import PublicNavbar from '@/components/PublicNavbar.vue';
import { useFeedback } from '@/composables/useFeedback';
import { Toaster } from 'vue-sonner';
import 'vue-sonner/style.css';

useFeedback();
</script>

<template>
    <div class="flex min-h-screen flex-col bg-slate-50">
        <PublicNavbar />

        <main class="flex-1">
            <slot />
        </main>

        <PublicFooter />

        <Toaster position="bottom-right" richColors />
    </div>
</template>
