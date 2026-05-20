export type Todo = {
    id: number;
    title: string;
    tasksCount: number;
    completedTasksCount: number;
    createdAt: string;
};

export type Task = {
    id: number;
    title: string;
    description: string | null;
    isCompleted: boolean;
    completedAt: string | null;
};

export type TodoDetail = {
    id: number;
    title: string;
    createdAt: string;
};
