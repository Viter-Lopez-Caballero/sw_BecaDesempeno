import {
    mdiHome,
    mdiSecurity,
    mdiViewModule,
    mdiLockCheckOutline,
    mdiAccountSupervisor,
    mdiAccount,
    mdiFileDocumentMultiple,
    mdiBullhorn,
    mdiBookOpenPageVariant,
    mdiOfficeBuilding,
    mdiLightbulbOnOutline,
    mdiCalendar,
    mdiClipboardTextOutline,
    mdiSchool,
    mdiStar,
} from "@mdi/js";

// Helper to create consistent security items with dynamic route prefix
const getSecurityItems = (routePrefix) => [
    {
        label: "Módulos",
        route: `${routePrefix}modules.index`,
        icon: mdiViewModule,
        permission: "modules.index",
    },
    {
        label: "Permisos",
        route: `${routePrefix}permissions.index`,
        icon: mdiLockCheckOutline,
        permission: "permissions.index",
    },
    {
        label: "Roles",
        route: `${routePrefix}roles.index`,
        icon: mdiAccountSupervisor,
        permission: "roles.index",
    },
    {
        label: "Usuarios",
        route: `${routePrefix}users.index`,
        icon: mdiAccount,
        permission: "users.index",
    },
];

// Helper for Catalog items
const getCatalogItems = (routePrefix) => [
    {
        label: "Instituciones",
        route: `${routePrefix}institutions.index`,
        icon: mdiOfficeBuilding,
        permission: "instituciones.index",
    },
    {
        label: "Áreas Prioritarias",
        route: `${routePrefix}priority-areas.index`,
        icon: mdiLightbulbOnOutline,
        permission: "priority_areas.index",
    },
    {
        label: "Sub Áreas",
        route: `${routePrefix}sub-areas.index`,
        icon: mdiBookOpenPageVariant,
        permission: "sub_areas.index",
    },
    {
        label: "Documentos",
        route: `${routePrefix}documentos`,
        icon: mdiFileDocumentMultiple,
        permission: "documents.index",
    },
    {
        label: "Calendario",
        route: `${routePrefix}calendario`,
        icon: mdiCalendar,
        permission: "catalogo.index",
    },
    {
        label: "Rúbrica",
        route: `${routePrefix}rubrics.index`,
        icon: mdiClipboardTextOutline,
        permission: "rubrics.index",
    },
];

export const menuConfigs = {
    superAdmin: [
        {
            label: "Inicio",
            route: "superadmin.inicio",
            icon: mdiHome,
        },
        {
            label: "Control de Solicitudes",
            route: "solicitudes.index",
            icon: mdiFileDocumentMultiple,
            permission: "solicitudes.index",
        },
        {
            label: "Convocatorias",
            route: "convocatorias.index",
            icon: mdiBullhorn,
            permission: "convocatorias.index",
        },
        {
            label: "Seguridad",
            icon: mdiSecurity,
            items: getSecurityItems("seguridad."),
        },
        {
            label: "Catálogo",
            icon: mdiBookOpenPageVariant,
            items: getCatalogItems("catalogo."),
        },
    ],
    admin: [
        {
            label: "Inicio",
            route: "inicio.dashboard",
            icon: mdiHome,
        },
        {
            label: "Solicitudes",
            route: "solicitudes.index",
            icon: mdiFileDocumentMultiple,
            permission: "solicitudes.index",
        },
        {
            label: "Seguridad",
            icon: mdiSecurity,
            items: [
                {
                    label: "Usuarios",
                    route: "seguridad.users.index",
                    icon: mdiAccount,
                    permission: "users.index",
                }
            ]
        },
        {
            label: "Reconocimiento",
            route: "reconocimiento.index",
            icon: mdiStar,
            permission: "reconocimiento.index",
        }
    ],
    docente: [
        {
            label: "Inicio",
            route: "inicio.dashboard",
            icon: mdiHome,
        },
        {
            label: "Convocatorias",
            route: "convocatorias.index",
            icon: mdiBullhorn,
            permission: "convocatorias.index",
        },
        {
            label: "Seguridad",
            icon: mdiSecurity,
            items: getSecurityItems("seguridad."),
        },
        {
            label: "Catálogo",
            icon: mdiBookOpenPageVariant,
            items: getCatalogItems("catalogo."),
        },
    ],
    evaluador: [
        {
            label: "Inicio",
            route: "inicio.dashboard",
            icon: mdiHome,
        },
        {
            label: "Evaluaciones",
            route: "evaluaciones.index",
            icon: mdiSchool,
            permission: "evaluaciones.index",
        },
        {
            label: "Reconocimiento",
            route: "reconocimiento.index",
            icon: mdiStar,
            permission: "reconocimiento.index",
        }
    ]
};
