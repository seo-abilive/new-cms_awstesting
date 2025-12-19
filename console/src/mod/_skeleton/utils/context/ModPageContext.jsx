import { createContext, useContext } from 'react'

const ModPageContext = createContext(undefined)

export const useModPage = () => {
    const context = useContext(ModPageContext)
    if (context === undefined) {
        throw new Error('useModPage must be used within a ModPageProvider')
    }
    return context
}

export const ModPageProvider = ({ children }) => {
    // In the future, module-specific logic can be added here.
    const value = {}

    return <ModPageContext.Provider value={value}>{children}</ModPageContext.Provider>
}
