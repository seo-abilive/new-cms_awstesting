import React from 'react'
import { config } from '../utils/config'
import { Index } from '../page/Index'
import { New, Edit } from '../page/Form'
import { ModPageLayout } from '../utils/components/ModPageLayout'

export const routes = [
    {
        element: ModPageLayout,
        children: [
            {
                name: config.name,
                path: `${config.path}`,
                element: Index,
                menu: true,
            },
            {
                path: `${config.path}/new`,
                element: New,
            },
            {
                path: `${config.path}/edit/:id`,
                element: Edit,
            },
        ],
    },
]
