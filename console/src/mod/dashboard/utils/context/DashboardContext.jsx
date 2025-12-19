import { createContext, useContext } from 'react'

const DashboardContext = createContext(undefined)

export const useDashboard = () => {
    const context = useContext(DashboardContext)
    if (context === undefined) {
        throw new Error('useDashboard must be used within a DashboardProvider')
    }
    return context
}

export const DashboardProvider = ({ children }) => {
    const value = {}

    return <DashboardContext.Provider value={value}>{children}</DashboardContext.Provider>
}
