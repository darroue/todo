export type Todo = {
    id: number;
    title: string;
    tasksCount: number;
    completedTasksCount: number;
    createdAt: string;
};

export type TaskAttachment = {
    id: number;
    filename: string;
    mimeType: string;
    size: number;
    url: string;
    isImage: boolean;
    extension: string;
};

export type Task = {
    id: number;
    title: string;
    description: string | null;
    isCompleted: boolean;
    completedAt: string | null;
    attachments: TaskAttachment[];
};

export type TodoDetail = {
    id: number;
    title: string;
    createdAt: string;
};
