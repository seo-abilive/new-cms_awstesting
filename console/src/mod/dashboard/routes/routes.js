import React from 'react'
import { config, manageConfig } from '../utils/config'
import { MasterIndex } from '../page/MasterIndex'
import { ManageIndex } from '../page/ManageIndex'
import { DashboardLayout } from '../utils/components/DashboardLayout'

export const routes = [
    {
        element: DashboardLayout,
        children: [
            {
                name: config.name,
                path: `${config.path}`,
                element: MasterIndex,
                menu: true,
            },
        ],
    },
]

export const manageRoutes = [
    {
        element: DashboardLayout,
        children: [
            {
                name: manageConfig.name,
                path: `${manageConfig.path}`,
                element: ManageIndex,
                menu: true,
            },
        ],
    },
]
