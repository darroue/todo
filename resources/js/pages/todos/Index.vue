<script setup lang="ts">
import { Form, Head, Link, router, usePage } from '@inertiajs/vue3';
import { useEcho } from '@laravel/echo-vue';
import { CheckSquare, Plus, Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { destroy, index, show, store } from '@/routes/todos';
import type { Team, Todo } from '@/types';

type Props = {
    todos: Todo[];
};

defineProps<Props>();

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Todos',
                href: props.currentTeam ? index(props.currentTeam.slug).url : '/',
            },
        ],
    }),
});

const page = usePage();
const currentTeamSlug = () => page.props.currentTeam!.slug;
const teamId = computed(() => page.props.currentTeam?.id);

useEcho<{ action: string }>(
    `team.${teamId.value}`,
    'Todos\\TodoChanged',
    () => router.reload({ only: ['todos'] }),
    [teamId],
);
</script>

<template>
    <Head title="Todos" />

    <div class="flex flex-col space-y-6 px-4 py-6">
        <Heading
            variant="small"
            title="Todos"
            description="Manage todos for your team"
        />

        <Form
            v-bind="store.form(currentTeamSlug())"
            class="flex gap-2"
            v-slot="{ errors, processing }"
        >
            <div class="flex flex-col gap-1">
                <Label for="todo-title" class="sr-only">Title</Label>
                <Input
                    id="todo-title"
                    name="title"
                    placeholder="New todo title…"
                    required
                    class="w-72"
                    data-test="todo-title-input"
                />
                <InputError :message="errors.title" />
            </div>
            <Button type="submit" :disabled="processing" data-test="todo-create-button">
                <Plus /> Add todo
            </Button>
        </Form>

        <div class="space-y-3">
            <div
                v-for="todo in todos"
                :key="todo.id"
                data-test="todo-row"
                class="flex items-center justify-between rounded-lg border p-4"
            >
                <Link
                    :href="show({ current_team: currentTeamSlug(), todo: todo.id }).url"
                    class="flex items-center gap-3 hover:underline"
                >
                    <CheckSquare class="h-5 w-5 text-muted-foreground" />
                    <div>
                        <p class="font-medium">{{ todo.title }}</p>
                        <p class="text-sm text-muted-foreground">
                            {{ todo.completedTasksCount }}/{{ todo.tasksCount }} tasks completed
                        </p>
                    </div>
                </Link>

                <Form v-bind="destroy.form({ current_team: currentTeamSlug(), todo: todo.id })">
                    <Button
                        type="submit"
                        variant="ghost"
                        size="sm"
                        data-test="todo-delete-button"
                    >
                        <Trash2 class="h-4 w-4 text-destructive" />
                    </Button>
                </Form>
            </div>

            <p
                v-if="todos.length === 0"
                class="py-8 text-center text-muted-foreground"
            >
                No todos yet. Create one above.
            </p>
        </div>
    </div>
</template>
