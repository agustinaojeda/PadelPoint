export interface User {
    id: number;
    nombreAdmin: string;
    correoAdmin: string;
    avatar?: string;
    created_at: string;
    updated_at: string;
}

export interface Auth {
    user: User | null;
}

export interface SharedData extends Record<string, unknown> {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    reservasPendientes: number;
    ziggy: {
        location: string;
        url: string;
        port: null | number;
        defaults: Record<string, unknown>;
        routes: Record<string, string>;
    };
}
