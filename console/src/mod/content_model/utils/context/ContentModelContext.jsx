import { useCompanyFacility } from '@/utils/context/CompanyFacilityContext'
import { createContext, useContext } from 'react'
import { config } from '../config'
const ContentModelContext = createContext(undefined)

export const useContentModel = () => {
    const context = useContext(ContentModelContext)
    if (context === undefined) {
        throw new Error('useContentModel must be used within a ContentModelProvider')
    }
    return context
}

export const ContentModelProvider = ({ children }) => {
    const { getBreads: getBreadsCompanyFacility, replacePath, company } = useCompanyFacility()

    const getBreads = (append = []) => {
        return getBreadsCompanyFacility([
            { name: config.name, path: replacePath(config.path) },
            ...append,
        ])
    }

    const value = {
        config: {
            ...config,
            path: replacePath(config.path),
            end_point: replacePath(config.end_point),
        },
        getBreads,
        replacePath,
        company,
    }

    return <ContentModelContext.Provider value={value}>{children}</ContentModelContext.Provider>
}
