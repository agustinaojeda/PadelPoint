<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    correoAdmin: '',
    contrasenaAdmin: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('contrasenaAdmin'),
    });
};
</script>

<template>
    <AuthBase title="Iniciar sesión" description="Completa los campos debajo para iniciar sesión.">
        <Head title="Iniciar sesión" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="correoAdmin">Correo electrónico</Label>
                    <Input
                        id="correoAdmin"
                        type="email"
                        required
                        autofocus
                        tabindex="1"
                        autocomplete="email"
                        v-model="form.correoAdmin"
                        placeholder="correo@ejemplo.com"
                    />
                    <InputError :message="form.errors.correoAdmin" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="contrasenaAdmin">Contraseña</Label>
                        <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-sm" tabindex="5"> ¿Olvidaste la contraseña? </TextLink>
                    </div>
                    <Input
                        id="contrasenaAdmin"
                        type="password"
                        required
                        tabindex="2"
                        autocomplete="current-password"
                        v-model="form.contrasenaAdmin"
                        placeholder="Contraseña"
                    />
                    <InputError :message="form.errors.contrasenaAdmin" />
                </div>

                <div class="flex items-center justify-between" tabindex="3">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" v-model:checked="form.remember" tabindex="4" />
                        <span>Recordarme</span>
                    </Label>
                </div>

                <Button type="submit" class="mt-4 w-full" tabindex="4" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Ingresar
                </Button>
            </div>

        </form>
    </AuthBase>
</template>
