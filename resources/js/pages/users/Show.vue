<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
const props = defineProps<{ user: any }>();
const breadcrumbs = [
    { title: 'Users', href: '/users' },
    { title: props.user.name, href: `/users/${props.user.id}` },
];
</script>

<template>
    <Head title="User Details" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex min-h-[80vh] flex-col items-center justify-center">
            <Card class="w-full max-w-xl">
                <CardHeader>
                    <CardTitle>User Details</CardTitle>
                </CardHeader>
                <CardContent class="flex flex-col gap-4">
                    <div><strong>Name:</strong> {{ props.user.name }}</div>
                    <div><strong>Email:</strong> {{ props.user.email }}</div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
