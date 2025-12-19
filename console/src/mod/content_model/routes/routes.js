import React from 'react'
import { config, markupConfig } from '../utils/config'
import { Index } from '../page/Index'
import { New, Edit } from '../page/Form'
import { Sort } from '../page/Sort'
import { Index as MarkupIndex } from '../page/markup/Index'
import { New as MarkupNew, Edit as MarkupEdit } from '../page/markup/Form'
import { ContentModelLayout } from '../utils/components/ContentModelLayout'
import { ContentModelMarkupLayout } from '../utils/components/ContentModelMarkupLayout'

export const routes = [
    {
        element: ContentModelLayout,
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
    {
        element: ContentModelMarkupLayout,
        children: [
            {
                name: markupConfig.name,
                path: `${markupConfig.path}`,
                element: MarkupIndex,
            },
            {
                path: `${markupConfig.path}/new`,
                element: MarkupNew,
            },
            {
                path: `${markupConfig.path}/edit/:id`,
                element: MarkupEdit,
            },
        ],
    },
       
]
