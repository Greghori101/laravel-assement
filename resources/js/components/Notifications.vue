<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

import { useEchoModel } from '@laravel/echo-vue';

import type { User } from '@/types';
import { DropdownMenuItem } from './ui/dropdown-menu';

interface NotificationData {
    user_id: number;
    first_name: string;
    last_name: string;
    email: string;
    created_at?: string;
}

const { user } = defineProps<{ user: User }>();

const notifications = ref<NotificationData[]>([]);
const showDropdown = ref(false);

const { channel } = useEchoModel('App.Models.User', user.id);

channel().notification((notification: any) => {
    notifications.value.unshift({
        user_id: notification.user_id,
        first_name: notification.first_name,
        last_name: notification.last_name,
        email: notification.email,
        created_at: new Date().toLocaleString(),
    });
});

function goToUser(userId: number) {
    router.visit(`/users/${userId}`);
    showDropdown.value = false;
}
</script>

<template>
    <div class="relative">
        <DropdownMenuItem
            v-for="(notif, index) in notifications"
            :key="index"
            :as-child="true"
        >
            <Link
                class="block w-full"
                prefetch
                :href="goToUser(notif.user_id)"
                as="button"
            >
                <p>
                    <strong
                        >{{ notif.first_name }} {{ notif.last_name }}</strong
                    >
                    ({{ notif.email }})
                </p>
                <small class="text-gray-500">{{ notif.created_at }}</small>
            </Link>
        </DropdownMenuItem>
        <DropdownMenuItem>
            <li v-if="notifications.length === 0" class="p-3 text-gray-400">
                No notifications yet.
            </li>
        </DropdownMenuItem>
    </div>
</template>
