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
    mdiFileTree,
    mdiBell,
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
        permission: "institutions.index",
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
        icon: mdiFileTree,
        permission: "sub_areas.index",
    },
    {
        label: "Documentos",
        route: `${routePrefix}documents.index`,
        icon: mdiFileDocumentMultiple,
        permission: "documents.index",
    },
    {
        label: "Rúbrica",
        route: `${routePrefix}rubrics.index`,
        icon: mdiClipboardTextOutline,
        permission: "rubrics.index",
    },
    {
        label: "Plantillas",
        route: `${routePrefix}templates.index`,
        icon: mdiFileDocumentMultiple, // or mdiFileCertificate if imported
        permission: "templates.index",
    },
];

export const menuConfigs = {
    superAdmin: [
        {
            label: "Inicio",
            route: "superadmin.dashboard",
            icon: mdiHome,
        },
        {
            label: "Control de Solicitudes",
            route: "superadmin.control-applications", // control-solicitudes -> control-applications (checked web.php line 181)
            icon: mdiFileDocumentMultiple,
        },
        {
            label: "Convocatorias",
            route: "announcements.index",
            icon: mdiBullhorn,
            permission: "announcements.index",
        },
        {
            label: "Seguridad",
            icon: mdiSecurity,
            items: getSecurityItems("security."), // seguridad. -> security. (checked web.php line 227)
        },
        {
            label: "Catálogo",
            icon: mdiBookOpenPageVariant,
            items: getCatalogItems("catalog."), // catalogo. -> catalog. (checked web.php line 287)
        },
    ],
    admin: [
        {
            label: "Inicio",
            route: "admin.dashboard",
            icon: mdiHome,
            permission: "admin.dashboard",
        },
        {
            label: "Solicitudes",
            route: "admin.applications.index",
            icon: mdiFileDocumentMultiple,
            permission: "applications.index",
        },
        {
            label: "Seguridad",
            icon: mdiSecurity,
            items: [
                {
                    label: "Usuarios",
                    route: "admin.evaluators.index", // evaluadores.index -> evaluators.index (checked web.php line 188)
                    icon: mdiAccount,
                    permission: "users.index",
                }
            ]
        },
        {
            label: "Reconocimiento",
            route: "admin.recognitions.index",
            icon: mdiStar,
            permission: "recognitions.index",
        }
    ],
    teacher: [
        {
            label: "Inicio",
            route: "teacher.dashboard",
            icon: mdiHome,
        },
        {
            label: "Convocatorias",
            route: "teacher.announcements.index",
            icon: mdiBullhorn,
            permission: "announcements.index",
        },
        {
            label: "Seguridad",
            icon: mdiSecurity,
            items: getSecurityItems("security."),
        },
        {
            label: "Catálogo",
            icon: mdiBookOpenPageVariant,
            items: getCatalogItems("catalog."),
        },
    ],
    evaluator: [
        {
            label: "Inicio",
            route: "evaluator.dashboard",
            icon: mdiHome,
        },
        {
            label: "Evaluaciones",
            route: "evaluator.evaluations.index",
            icon: mdiSchool,
            permission: "evaluator.evaluations.index",
        },
        {
            label: "Reconocimientos",
            route: "evaluator.recognitions.index",
            icon: mdiStar,
            permission: "evaluator.recognitions.index",
        },
        {
            label: "Seguridad",
            icon: mdiSecurity,
            items: getSecurityItems("security."),
        },
        {
            label: "Catálogo",
            icon: mdiBookOpenPageVariant,
            items: getCatalogItems("catalog."),
        },
    ]
};
