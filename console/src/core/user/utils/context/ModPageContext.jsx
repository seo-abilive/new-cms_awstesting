import { createContext, useContext } from 'react'
import { useLocation } from 'react-router'

const ModPageContext = createContext(undefined)

export const useModPage = () => {
    const context = useContext(ModPageContext)
    if (context === undefined) {
        throw new Error('useModPage must be used within a ModPageProvider')
    }
    return context
}

export const ModPageProvider = ({ children }) => {
    const location = useLocation()
    const { user_type, company, facility } = location.state || {}

    // In the future, module-specific logic can be added here.
    const value = {
        user_type,
        company,
        facility,
    }

    return <ModPageContext.Provider value={value}>{children}</ModPageContext.Provider>
}
