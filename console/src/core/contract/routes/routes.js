import React from 'react'
import { config } from '../utils/config'
import { Index as CompanyIndex } from '../page/Company/Index'
import { New as CompanyNew, Edit as CompanyEdit } from '../page/Company/Form'
import { Sort as CompanySort } from '../page/Company/Sort'
import { ContractCompanyLayout } from '../utils/components/ContractCompanyLayout'
import { Index as FacilityIndex } from '../page/Facility/Index'
import { New as FacilityNew, Edit as FacilityEdit } from '../page/Facility/Form'
import { Sort as FacilitySort } from '../page/Facility/Sort'
import { ContractFacilityLayout } from '../utils/components/ContractFacilityLayout'

export const routes = [
    {
        element: ContractCompanyLayout,
        children: [
            {
                name: config.name,
                path: `${config.path}/company`,
                element: CompanyIndex,
                menu: true,
            },
            {
                path: `${config.path}/company/new`,
                element: CompanyNew,
            },
            {
                path: `${config.path}/company/edit/:id`,
                element: CompanyEdit,
            },
            {
                path: `${config.path}/company/sort`,
                element: CompanySort,
            },
        ],
    },
    {
        element: ContractFacilityLayout,
        children: [
            {
                name: config.name,
                path: `${config.path}/facility`,
                element: FacilityIndex,
                menu: true,
            },
            {
                path: `${config.path}/facility/new`,
                element: FacilityNew,
            },
            {
                path: `${config.path}/facility/edit/:id`,
                element: FacilityEdit,
            },
            {
                path: `${config.path}/facility/sort`,
                element: FacilitySort,
            },
        ],
    },
]
