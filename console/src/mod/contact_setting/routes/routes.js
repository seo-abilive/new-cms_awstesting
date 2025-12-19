import React from 'react'
import { config } from '@/mod/contact_setting/utils/config'
import { Index } from '@/mod/contact_setting/page/Index'
import { New, Edit } from '@/mod/contact_setting/page/Form'
import { Sort } from '@/mod/contact_setting/page/Sort'
import { ModPageLayout } from '@/mod/contact_setting/utils/components/ModPageLayout'

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
            {
                path: `${config.path}/sort`,
                element: Sort,
            },
        ],
    },
]
