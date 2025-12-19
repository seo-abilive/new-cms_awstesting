import React from 'react'
import { config } from '@/mod/action_log/utils/config'
import { Index } from '@/mod/action_log/page/Index'
import { ModPageLayout } from '@/mod/action_log/utils/components/ModPageLayout'

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
        ],
    },
]
