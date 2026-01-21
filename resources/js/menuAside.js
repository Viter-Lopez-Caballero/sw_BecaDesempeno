import {
  mdiAccountCircle,
  mdiMonitor,
  mdiGithub,
  mdiLock,
  mdiAlertCircle,
  mdiSquareEditOutline,
  mdiTable,
  mdiViewList,
  mdiTelevisionGuide,
  mdiResponsive,
  mdiPalette,
  mdiReact,
  mdiPlus,
  mdiAccountEdit,
  mdiDeleteEmpty,
  mdiAccountTie,
  mdiChartLine,
  mdiFileChartOutline,
  mdiAccountSchool,
    mdiMinus,
    mdiViewModule,
    mdiLockCheckOutline,
    mdiLockOutline,
    mdiAccountSupervisor,
    mdiAccount,
    mdiSchool,
    mdiTree,
    mdiLightbulbOutline,
    mdiRocket,
    mdiLightbulbOnOutline,
    mdiBook,
    mdiTheater,
    mdiOfficeBuilding,
    mdiDomain,
    mdiFileDocument,
    mdiHandshake,
    mdiCalendarMonth,
    mdiCrowd,
    mdiHumanMaleBoard,
    mdiPageLayoutHeader,
    mdiFolderPlus,
    mdiOfficeBuildingPlus,
    mdiAccountCash,
    mdiDeskLampOn,
    mdiLan,
    mdiFileDocumentCheck,
    mdiBookCheck,
    mdiFolderOpen,
    mdiTruckDelivery,
    mdiListBox,
    mdiCity,
    mdiCityVariant,
    mdiHomeGroup 

} from "@mdi/js";

export default [
  
  {
    href: "/dashboard",
    icon: mdiMonitor,
    label: "Inicio",
  },
  {
    href: "/profile",
    label: "Perfil",
    icon: mdiAccountCircle,
  },
  {
    href: '/grafica',
    label: 'Graficas',
    icon: mdiChartLine,
    permission: 'grafica.index'
  },
  {
    href: '/reporte',
    label: 'Reportes',
    icon: mdiFileChartOutline,
    permission: 'reporte.index'

  },
  {
    label: "Seguridad",
    icon: mdiViewList,
    // role: "Admin",
    permission: "modulo.seguridad",
    menu: [
        {
            label: "Modulos",
            href: "/modulo",
            icon: mdiViewModule,
            permission: "modulo.index",
        },
        {
            label: "Permisos",
            href: "/permissions",
            icon: mdiLockCheckOutline,
            permission: "permissions.index",
        },
        {
            label: "Roles",
            href: "/perfiles",
            icon: mdiAccountSupervisor,
            permission: "perfiles.index",
        },
        {
            label: "Usuarios",
            href: "/usuarios",
            icon: mdiAccount,
            permission: "usuarios.index",
        },
    ],
},
    {
      label: 'Catálogos',
      icon: mdiFolderPlus,
      permission: 'modulo.catalogo',
      menu:[
        {
          label:'Instituciones',
          href: '/institucion',
          icon: mdiOfficeBuildingPlus,
          permission: 'institucion.index'
        },
        {
          label: 'Tipos de Estancia',
          href: '/tipo_estancia',
          icon: mdiAccountCash,
          permission: 'tipo_estancia.index'
        },
        {
          label: 'Departamentos',
          href: '/departamento',
          icon: mdiDeskLampOn,
          permission: 'departamento.index'
        },
        {
          label: 'Área',
          href: '/area',
          icon: mdiLan ,
          permission: 'area.index'
        },
        {
          label: 'SubArea',
          href: '/subarea',
          icon: mdiLan ,
          permission: 'subarea.index'
        },
        {
          label: 'Estados',
          href: '/estado',
          icon: mdiCityVariant,
          permission: 'estado.index'
        },
        {
          label: 'Municipio',
          href: '/municipio',
          icon: mdiHomeGroup   ,
          permission: 'municipio.index'
        },
        {
          label: 'Colonias',
          href: '/colonia',
          icon: mdiCityVariant,
          permission: 'colonia.index'
        }
      ]
    },
    {
      label: 'Documentación',
      icon: mdiFolderPlus,
      permission: 'modulo.documentacionR',
      menu:[
        {
          label:'Carta Aceptación',
          href: '/aceptacion',
          icon: mdiFileDocumentCheck,
          permission: 'aceptacionR.index'
        },
        {
          label:'Carta Liberacion',
          href: '/liberacion',
          icon: mdiBookCheck,
          permission: 'liberacionR.index'
        },
      ]
    },
    {
      label: 'Documentación',
      icon: mdiFolderPlus,
      permission: 'modulo.documentacionE',
      menu:[
        {
          label:'Carta Aceptación',
          href: '/aceptacion',
          icon: mdiFileDocumentCheck,
          permission: 'aceptacionE.index'
        },
        {
          label:'Carta Liberacion',
          href: '/liberacion',
          icon: mdiBookCheck,
          permission: 'liberacionE.index'
        },
      ]
    },
    /* {
      label: "Proyectos ",
      href: "/proyectoE",
      icon: mdiFolderOpen,
      permission: 'proyectoE.index'
    }, */
    {
      label: "Proyectos ",
      href: "/proyecto",
      icon: mdiFolderOpen,
      permission: 'proyecto.index'
    },
    {
      label: "Postulantes",
      href: "/postulante",
      icon: mdiAccountSchool,
      permission: 'postulante.index'
    },
    {
      label: "Entregables",
      href: "/entregable",
      icon: mdiTruckDelivery ,
      permission: 'entregableE.index'
    },
    {
      label: "Entregables",
      href: "/entregable",
      icon: mdiTruckDelivery ,
      permission: 'entregableP.index'
    },
    {
      label: "Registro de Estancia",
      href: "/estancia",
      icon: mdiListBox ,
      permission: 'estancia.index'
    },
];
