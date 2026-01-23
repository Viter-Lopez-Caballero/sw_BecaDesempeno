import {
    mdiViewModule,
    mdiLockCheckOutline,
    mdiAccountSupervisor,
    mdiAccount,
    mdiSecurity,
    mdiMonitorDashboard,
} from "@mdi/js";

export const menuItems = [
    {
        label: "Inicio",
        items: [
            {
                label: "Dashboard",
                route: "dashboard",
                icon: mdiMonitorDashboard,
            },
        ],
    },
    {
        label: "Seguridad",
        items: [
            {
                label: "Módulos",
                route: "modules.index",
                icon: mdiViewModule,
            },
            {
                label: "Permisos",
                route: "permissions.index",
                icon: mdiLockCheckOutline,
            },
            {
                label: "Roles",
                route: "roles.index",
                icon: mdiAccountSupervisor,
            },
            {
                label: "Usuarios",
                route: "users.index",
                icon: mdiAccount,
            },
        ],
    },
];
