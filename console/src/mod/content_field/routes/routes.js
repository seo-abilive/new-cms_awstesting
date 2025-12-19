import React from 'react'
import { config } from '@/mod/content_field/utils/config'
import { Index } from '@/mod/content_field/page/Index'
import { New, Edit } from '@/mod/content_field/page/Form'
import { Sort } from '@/mod/content_field/page/Sort'
import { Index as CustomIndex } from '@/mod/content_field/page/custom/Index'
import { New as CustomNew, Edit as CustomEdit } from '@/mod/content_field/page/custom/Form'
import { Sort as CustomSort } from '@/mod/content_field/page/custom/Sort'
import { ContentFieldLayout } from '@/mod/content_field/utils/components/ContentFieldLayout'

export const routes = [
    {
        element: ContentFieldLayout,
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
            {
                path: `${config.path}/custom`,
                element: CustomIndex,
            },
            {
                path: `${config.path}/custom/new`,
                element: CustomNew,
            },
            {
                path: `${config.path}/custom/edit/:id`,
                element: CustomEdit,
            },
            {
                path: `${config.path}/custom/sort`,
                element: CustomSort,
            },
        ],
    },
]
