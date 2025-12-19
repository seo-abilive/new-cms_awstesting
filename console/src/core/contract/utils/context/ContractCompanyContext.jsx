import { createContext, useContext } from 'react'
import { config } from '../config'

const ContractCompanyContext = createContext(undefined)

export const useContractCompany = () => {
    const context = useContext(ContractCompanyContext)
    if (context === undefined) {
        throw new Error('useContractCompany must be used within a ContractCompanyProvider')
    }
    return context
}

export const ContractCompanyProvider = ({ children }) => {
    let clone = { ...config }
    clone.name = '企業管理'
    clone.path = config.path + '/company'
    clone.end_point = config.end_point + '/company'

    const value = {
        config: clone,
    }

    return (
        <ContractCompanyContext.Provider value={value}>{children}</ContractCompanyContext.Provider>
    )
}
