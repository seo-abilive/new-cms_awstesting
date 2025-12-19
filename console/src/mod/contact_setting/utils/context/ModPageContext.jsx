import { useCompanyFacility } from '@/utils/context/CompanyFacilityContext'
import { createContext, useContext } from 'react'
import { config } from '@/mod/contact_setting/utils/config'

const ModPageContext = createContext(undefined)

export const useModPage = () => {
    const context = useContext(ModPageContext)
    if (context === undefined) {
        throw new Error('useModPage must be used within a ModPageProvider')
    }
    return context
}

export const ModPageProvider = ({ children }) => {
    const { company_alias, facility_alias } = useCompanyFacility()

    // In the future, module-specific logic can be added here.
    const value = {
        config: {
            ...config,
            path: `${config.path
                .replace(':company_alias', company_alias)
                .replace(':facility_alias', facility_alias)}`,
            end_point: `${config.end_point
                .replace(':company_alias', company_alias)
                .replace(':facility_alias', facility_alias)}`,
        },
    }

    return <ModPageContext.Provider value={value}>{children}</ModPageContext.Provider>
}
