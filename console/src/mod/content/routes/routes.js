import React from 'react'
import { config } from '@/mod/content/utils/config'
import { Index } from '@/mod/content/page/Index'
import { New, Edit } from '@/mod/content/page/Form'
import { Sort } from '@/mod/content/page/Sort'
import { ContentLayout } from '@/mod/content/utils/components/ContentLayout'

import { Index as CateIndex } from '@/mod/content/page/category/Index'
import { New as CateNew, Edit as CateEdit } from '@/mod/content/page/category/Form'
import { Sort as CateSort } from '@/mod/content/page/category/Sort'

export const routes = [
    {
        element: ContentLayout,
        children: [
            {
                name: config.name,
                path: `${config.path}/category`,
                element: CateIndex,
                menu: true,
            },
            {
                path: `${config.path}/category/new`,
                element: CateNew,
            },
            {
                path: `${config.path}/category/edit/:id`,
                element: CateEdit,
            },
            {
                path: `${config.path}/category/sort`,
                element: CateSort,
            },
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
