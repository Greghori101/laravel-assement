<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

import AppLayout from '@/layouts/AppLayout.vue';
import { Form, Head, router } from '@inertiajs/vue3';

const breadcrumbs = [
    { title: 'Users', href: '/users' },
    { title: 'Create', href: '/users/create' },
];

</script>

<template>
    <Head title="Create User" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto h-full p-6">
            <Card>
                <CardHeader>
                    <CardTitle>Create User</CardTitle>
                    <CardDescription>
                        Enter the information below to create a new user.
                    </CardDescription>
                </CardHeader>

                <CardContent>
                    <Form
                        method="post"
                        action="/users"
                        class="flex flex-col gap-8"
                        :reset-on-success="[
                            'password',
                            'password_confirmation',
                        ]"
                        v-slot="{ errors, processing }"
                    >
                        <div class="grid gap-6">
                            <!-- USER INFO -->
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
                                        required
                                        placeholder="First name"
                                    />
                                    <InputError :message="errors.first_name" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="last_name">Last Name</Label>
                                    <Input
                                        id="last_name"
                                        name="last_name"
                                        type="text"
                                        required
                                        placeholder="Last name"
                                    />
                                    <InputError :message="errors.last_name" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="email">Email</Label>
                                    <Input
                                        id="email"
                                        name="email"
                                        type="email"
                                        required
                                        placeholder="email@example.com"
                                    />
                                    <InputError :message="errors.email" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="password">Password</Label>
                                    <Input
                                        id="password"
                                        name="password"
                                        type="password"
                                        required
                                        placeholder="Password"
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
                                        required
                                        placeholder="Confirm password"
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
                                        required
                                        placeholder="Country"
                                    />
                                    <InputError :message="errors.country" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="city">City</Label>
                                    <Input
                                        id="city"
                                        name="city"
                                        type="text"
                                        required
                                        placeholder="City"
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
                                        required
                                        placeholder="Post code"
                                    />
                                    <InputError :message="errors.post_code" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="street">Street</Label>
                                    <Input
                                        id="street"
                                        name="street"
                                        type="text"
                                        required
                                        placeholder="Street"
                                    />
                                    <InputError :message="errors.street" />
                                </div>
                            </div>
                        </div>

                        <CardFooter
                            class="flex justify-end gap-3 border-t pt-6"
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
                                Create User
                            </Button>
                        </CardFooter>
                    </Form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
