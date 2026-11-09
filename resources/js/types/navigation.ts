import type { Component } from 'vue';

export interface NavItem {
    title: string;
    href: string;
    icon?: Component;
    isActive?: boolean;
    badge?: number;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export type BreadcrumbItemType = BreadcrumbItem;
