import { Config } from 'ziggy-js';

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export interface Message {
    id: number;
    platform: 'Telegram' | 'Whatsapp' | 'Discord' | 'Slack';
    content: string;
    recipient: string[];
    attachment_url?: string | null;
    created_at: string;
    user_id: number;
}

export interface ChartStats {
    labels: string[],
    data: number[]
}

export interface Pagination<T> {
    data: T[];
    current_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
    last_page: number;
    total: number;
    per_page: number;
}

export interface Column<T = any> {
    key: keyof T | string;
    label: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    ziggy: Config & { location: string };
};

