<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface User {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface UsersPaginationData {
    data: User[];
    links: PaginationLink[];
    // Add other pagination properties if needed (e.g., current_page, last_page, total, etc.)
}

const props = defineProps<{ users: UsersPaginationData; search?: string }>();

const breadcrumbs = [{ title: 'Users', href: '/users' }];

const searchInput = ref(props.search || '');

function searchUsers(): void {
    router.get(
        '/users',
        { search: searchInput.value },
        { preserveState: true, replace: true },
    );
}

function clearSearch(): void {
    searchInput.value = '';
    router.get('/users', {}, { preserveState: true, replace: true });
}

function destroy(id: number): void {
    if (confirm('Are you sure?')) {
        router.delete(`/users/${id}`);
    }
}
</script>

<template>
    <Head title="Users" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto h-full p-6">
            <Card class="w-full">
                <CardHeader>
                    <div
                        class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
                    >
                        <CardTitle>Users</CardTitle>
                        <Button as="a" href="/users/create">
                            Create User
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <!-- Search Field -->
                    <form
                        @submit.prevent="searchUsers"
                        class="mb-6 flex flex-col items-stretch gap-2 sm:flex-row sm:items-center"
                    >
                        <Input
                            v-model="searchInput"
                            placeholder="Search users by name or email..."
                            class="w-full sm:w-64"
                        />
                        <Button type="submit" variant="outline">Search</Button>
                        <Button
                            v-if="searchInput"
                            type="button"
                            variant="ghost"
                            @click="clearSearch"
                            >Clear</Button
                        >
                    </form>

                    <div class="overflow-x-auto">
                        <Table class="w-full">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>First Name</TableHead>
                                    <TableHead>Last Name</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead class="text-right"
                                        >Actions</TableHead
                                    >
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow
                                    v-for="user in props.users?.data || []"
                                    :key="user.id"
                                >
                                    <TableCell>{{ user.first_name }}</TableCell>
                                    <TableCell>{{ user.last_name }}</TableCell>
                                    <TableCell>{{ user.email }}</TableCell>

                                    <TableCell class="text-right">
                                        <Button
                                            as="a"
                                            :href="`/users/${user.id}`"
                                            size="sm"
                                            variant="outline"
                                            class="mr-2"
                                        >
                                            View
                                        </Button>
                                        <Button
                                            as="a"
                                            :href="`/users/${user.id}/edit`"
                                            size="sm"
                                            variant="outline"
                                            class="mr-2"
                                        >
                                            Edit
                                        </Button>
                                    </TableCell>
                                </TableRow>
                                <TableRow
                                    v-if="
                                        !props.users?.data ||
                                        props.users.data.length === 0
                                    "
                                >
                                    <TableCell colspan="4" class="text-center"
                                        >No users found.</TableCell
                                    >
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <!-- Pagination Controls -->
                    <div
                        v-if="props.users?.links?.length > 3"
                        class="mt-4 flex flex-wrap justify-center gap-2"
                    >
                        <Button
                            v-for="link in props.users.links"
                            :key="link.label"
                            :disabled="!link.url"
                            :variant="link.active ? 'default' : 'outline'"
                            @click="link.url && router.visit(link.url)"
                            v-html="link.label"
                            size="sm"
                        />
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
