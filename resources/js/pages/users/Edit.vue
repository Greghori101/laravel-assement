<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

import AppLayout from '@/layouts/AppLayout.vue';
import { Form, Head, router } from '@inertiajs/vue3';

interface Address {
    country: string;
    city: string;
    post_code: number;
    street: string;
}
interface User {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    address: Address;
}

const { user } = defineProps<{ user: User }>();

const breadcrumbs = [
    { title: 'Users', href: '/users' },
    { title: 'Edit', href: `/users/${user.id}/edit` },
];

function submit(values: any) {
    // Only send password data if provided
    const payload = {
        ...values,
        password: values.password || undefined,
        password_confirmation: values.password_confirmation || undefined,
    };

    if (!payload.password) delete payload.password;
    if (!payload.password_confirmation) delete payload.password_confirmation;

    router.patch(`/users/${user.id}`, payload);
}
</script>

<template>
    <Head title="Edit User" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto h-full p-6">
            <Card>
                <CardHeader>
                    <CardTitle>Edit User</CardTitle>
                    <CardDescription
                        >Edit the basic information for this
                        user.</CardDescription
                    >
                </CardHeader>

                <CardContent>
                    <Form
                        method="patch"
                        :action="`/users/${user.id}`"
                        class="flex flex-col gap-8"
                        v-slot="{ errors, processing, recentlySuccessful }"
                        :initial-values="{
                            first_name: user.first_name,
                            last_name: user.last_name,
                            email: user.email,
                            country: user.address?.country,
                            city: user.address?.city,
                            post_code: user.address?.post_code,
                            street: user.address?.street,
                            password: '',
                            password_confirmation: '',
                        }"
                    >
                        <div class="grid gap-6">
                            <!-- USER INFORMATION -->
                            <div class="grid gap-4">
                                <h3 class="text-lg font-semibold">
                                    User Information
                                </h3>

                                <div class="grid gap-2">
                                    <Label for="first_name">First Name</Label>
                                    <Input
                                        id="first_name"
                                        name="first_name"
                                        type="text"
                                        placeholder="First name"
                                        :default-value="user.first_name"
                                        required
                                    />
                                    <InputError :message="errors.first_name" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="last_name">Last Name</Label>
                                    <Input
                                        id="last_name"
                                        name="last_name"
                                        type="text"
                                        placeholder="Last name"
                                        :default-value="user.last_name"
                                        required
                                    />
                                    <InputError :message="errors.last_name" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="email">Email</Label>
                                    <Input
                                        id="email"
                                        name="email"
                                        type="email"
                                        placeholder="email@example.com"
                                        :default-value="user.email"
                                        required
                                    />
                                    <InputError :message="errors.email" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="password">New Password</Label>
                                    <Input
                                        id="password"
                                        name="password"
                                        type="password"
                                        placeholder="Leave empty to keep existing password"
                                    />
                                    <InputError :message="errors.password" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="password_confirmation"
                                        >Confirm Password</Label
                                    >
                                    <Input
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        type="password"
                                        placeholder="Confirm new password"
                                    />
                                    <InputError
                                        :message="errors.password_confirmation"
                                    />
                                </div>
                            </div>

                            <!-- ADDRESS -->
                            <div class="grid gap-4 border-t pt-6">
                                <h3 class="text-lg font-semibold">Address</h3>

                                <div class="grid gap-2">
                                    <Label for="country">Country</Label>
                                    <Input
                                        id="country"
                                        name="country"
                                        type="text"
                                        placeholder="Country"
                                        :default-value="user.address?.country"
                                        required
                                    />
                                    <InputError :message="errors.country" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="city">City</Label>
                                    <Input
                                        id="city"
                                        name="city"
                                        type="text"
                                        placeholder="City"
                                        :default-value="user.address?.city"
                                        required
                                    />
                                    <InputError :message="errors.city" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="post_code">Post Code</Label>
                                    <Input
                                        id="post_code"
                                        name="post_code"
                                        type="number"
                                        min="0"
                                        placeholder="Post code"
                                        :default-value="user.address?.post_code"
                                        required
                                    />
                                    <InputError :message="errors.post_code" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="street">Street</Label>
                                    <Input
                                        id="street"
                                        name="street"
                                        type="text"
                                        placeholder="Street"
                                        :default-value="user.address?.street"
                                        required
                                    />
                                    <InputError :message="errors.street" />
                                </div>
                            </div>
                        </div>

                        <CardFooter
                            class="flex justify-end gap-4 border-t pt-6"
                        >
                            <Button
                                variant="outline"
                                type="button"
                                @click="router.visit('/users')"
                                :disabled="processing"
                            >
                                Cancel
                            </Button>

                            <Button type="submit" :disabled="processing">
                                <Spinner v-if="processing" />
                                Save Changes
                            </Button>

                            <Transition
                                enter-active-class="transition ease-in-out"
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out"
                                leave-to-class="opacity-0"
                            >
                                <p
                                    v-show="recentlySuccessful"
                                    class="ml-2 text-sm text-neutral-600"
                                >
                                    Saved.
                                </p>
                            </Transition>
                        </CardFooter>
                    </Form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
