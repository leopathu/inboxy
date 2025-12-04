export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    role: string;
    current_brand_id: number | null;
}

export interface Brand {
    id: number;
    name: string;
    company: string;
    description?: string;
    website?: string;
    country?: string;
    timezone?: string;
    created_at: string;
    updated_at: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
};
