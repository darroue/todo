<script setup lang="ts">
import { Form, Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { useEcho } from '@laravel/echo-vue';
import { ArrowLeft, Check, Plus, Trash2 } from 'lucide-vue-next';
import { computed, nextTick, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index, show } from '@/routes/todos';
import { destroy as destroyTask, store as storeTask, update as updateTask } from '@/routes/todos/tasks';
import type { Task, Team, TodoDetail } from '@/types';

type Props = {
    todo: TodoDetail;
    tasks: Task[];
};

const props = defineProps<Props>();

defineOptions({
    layout: (props: { currentTeam?: Team | null; todo: TodoDetail }) => ({
        breadcrumbs: [
            {
                title: 'Todos',
                href: props.currentTeam ? index(props.currentTeam.slug).url : '/',
            },
            {
                title: props.todo.title,
                href: props.currentTeam
                    ? show({ current_team: props.currentTeam.slug, todo: props.todo.id }).url
                    : '/',
            },
        ],
    }),
});

const page = usePage();
const currentTeamSlug = () => page.props.currentTeam!.slug;
const taskArgs = () => ({ current_team: currentTeamSlug(), todo: props.todo.id });

const newTaskKey = ref(0);

const teamId = computed(() => page.props.currentTeam?.id);

const editingTaskId = ref<number | null>(null);
const editTitle = ref('');
const editDescription = ref('');
const editTitleEl = ref<HTMLInputElement | null>(null);

function startEdit(task: Task) {
    editingTaskId.value = task.id;
    editTitle.value = task.title;
    editDescription.value = task.description ?? '';
    nextTick(() => editTitleEl.value?.focus());
}

function cancelEdit() {
    editingTaskId.value = null;
}

function saveEdit(task: Task) {
    if (editingTaskId.value !== task.id) {
        return;
    }

    const form = useForm({
        title: editTitle.value.trim() || task.title,
        description: editDescription.value.trim() || null,
    });

    form.patch(updateTask({ ...taskArgs(), task: task.id }).url, {
        preserveScroll: true,
        onSuccess: () => {
            editingTaskId.value = null;
        },
    });
}

useEcho<{ todoId: number }>(
    `team.${teamId.value}`,
    'Todos\\TaskChanged',
    (e) => {
        if (e.todoId === props.todo.id) {
            router.reload({ only: ['tasks'] });
        }
    },
    [teamId],
);
</script>

<template>
    <Head :title="todo.title" />

    <div class="flex flex-col space-y-6 px-4 py-6">
        <div class="flex items-center gap-4">
            <Button variant="ghost" size="sm" as-child>
                <Link :href="index(currentTeamSlug()).url">
                    <ArrowLeft class="h-4 w-4" />
                </Link>
            </Button>
            <Heading variant="small" :title="todo.title" description="Manage tasks for this todo" />
        </div>

        <!-- Add task form -->
        <Form
            :key="newTaskKey"
            v-bind="storeTask.form(taskArgs())"
            class="flex flex-col gap-3 rounded-lg border p-4"
            v-slot="{ errors, processing }"
            @success="newTaskKey++"
        >
            <p class="text-sm font-medium">Add a new task</p>
            <div class="flex flex-col gap-1">
                <Label for="task-title">Title</Label>
                <Input
                    id="task-title"
                    name="title"
                    placeholder="Task title…"
                    required
                    data-test="task-title-input"
                />
                <InputError :message="errors.title" />
            </div>
            <div class="flex flex-col gap-1">
                <Label for="task-description">Description</Label>
                <textarea
                    id="task-description"
                    name="description"
                    placeholder="Optional description…"
                    rows="2"
                    class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                    data-test="task-description-input"
                />
                <InputError :message="errors.description" />
            </div>
            <div>
                <Button type="submit" :disabled="processing" data-test="task-create-button">
                    <Plus /> Add task
                </Button>
            </div>
        </Form>

        <!-- Task list -->
        <div class="space-y-2">
            <div
                v-for="task in tasks"
                :key="task.id"
                data-test="task-row"
                class="flex items-start justify-between rounded-lg border p-4"
                :class="task.isCompleted ? 'opacity-60' : ''"
            >
                <div class="flex items-start gap-3">
                    <Form
                        v-bind="updateTask.form({ ...taskArgs(), task: task.id })"
                        class="mt-0.5"
                    >
                        <input type="hidden" name="isCompleted" :value="task.isCompleted ? '0' : '1'" />
                        <button type="submit" class="cursor-pointer" :data-test="`task-toggle-${task.id}`">
                            <div
                                class="flex h-5 w-5 items-center justify-center rounded border-2"
                                :class="task.isCompleted ? 'border-primary bg-primary text-primary-foreground' : 'border-muted-foreground'"
                            >
                                <Check v-if="task.isCompleted" class="h-3 w-3" />
                            </div>
                        </button>
                    </Form>

                    <div class="flex-1">
                        <template v-if="editingTaskId === task.id">
                            <input
                                ref="editTitleEl"
                                v-model="editTitle"
                                class="w-full rounded border border-input bg-transparent px-2 py-0.5 text-sm font-medium focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                :data-test="`task-edit-title-${task.id}`"
                                @keydown.enter.prevent="saveEdit(task)"
                                @keydown.escape="cancelEdit"
                                @blur="saveEdit(task)"
                            />
                            <textarea
                                v-model="editDescription"
                                rows="2"
                                class="mt-1 w-full rounded border border-input bg-transparent px-2 py-0.5 text-sm text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                :data-test="`task-edit-description-${task.id}`"
                                @keydown.escape="cancelEdit"
                                @blur="saveEdit(task)"
                            />
                        </template>
                        <template v-else>
                            <p
                                class="cursor-text font-medium"
                                :class="task.isCompleted ? 'line-through' : ''"
                                :data-test="`task-title-${task.id}`"
                                @dblclick="startEdit(task)"
                            >
                                {{ task.title }}
                            </p>
                            <p
                                v-if="task.description"
                                class="mt-1 cursor-text text-sm text-muted-foreground"
                                :data-test="`task-description-${task.id}`"
                                @dblclick="startEdit(task)"
                            >
                                {{ task.description }}
                            </p>
                            <p
                                v-else
                                class="mt-1 cursor-text text-sm text-muted-foreground/40 italic"
                                @dblclick="startEdit(task)"
                            >
                                Add description…
                            </p>
                        </template>
                    </div>
                </div>

                <Form v-bind="destroyTask.form({ ...taskArgs(), task: task.id })">
                    <Button
                        type="submit"
                        variant="ghost"
                        size="sm"
                        :data-test="`task-delete-${task.id}`"
                    >
                        <Trash2 class="h-4 w-4 text-destructive" />
                    </Button>
                </Form>
            </div>

            <p
                v-if="tasks.length === 0"
                class="py-8 text-center text-muted-foreground"
            >
                No tasks yet. Add one above.
            </p>
        </div>
    </div>
</template>
