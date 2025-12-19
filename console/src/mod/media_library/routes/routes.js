import React from 'react'
import { config } from '../utils/config'
import { Index } from '../page/Index'
import { Edit } from '../page/Form'
import { Sort } from '../page/Sort'
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
                path: `${config.path}/edit/:id`,
                element: Edit,
            },
            {
                path: `${config.path}/sort`,
                element: Sort,
            },
        ],
    },
]
