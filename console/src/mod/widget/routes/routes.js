import React from 'react'
import { config } from '../utils/config'
import { Index as ContactIndex } from '../page/contact/Index'
import { Confirm as ContactConfirm } from '../page/contact/Confirm'
import { Thanks as ContactThanks } from '../page/contact/Thanks'
import { ContactLayout } from '../utils/components/ContactLayout'

export const routes = [
    {
        element: ContactLayout,
        children: [
            {
                path: `${config.path}/contact/confirm/:token`,
                element: ContactConfirm,
            },
            {
                path: `${config.path}/contact/thanks/:token`,
                element: ContactThanks,
            },
            {
                name: config.name,
                path: `${config.path}/contact/:token`,
                element: ContactIndex,
                menu: true,
            },
        ],
    },
]
